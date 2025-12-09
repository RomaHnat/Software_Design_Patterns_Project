<?php
namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use SaleService;
use SaleRepository;
use CategoryRepository;
use CustomerRepository;
use Customer;
use Category;
use Sale;
use SaleItem;

class SaleServiceTest extends TestCase {
    private SaleService $service;
    private SaleRepository|MockObject $mockSaleRepo;
    private CategoryRepository|MockObject $mockCategoryRepo;
    private CustomerRepository|MockObject $mockCustomerRepo;

    protected function setUp(): void {
        $this->mockSaleRepo = $this->createMock(SaleRepository::class);
        $this->mockCategoryRepo = $this->createMock(CategoryRepository::class);
        $this->mockCustomerRepo = $this->createMock(CustomerRepository::class);

        $this->service = new SaleService(
            $this->mockSaleRepo,
            $this->mockCategoryRepo,
            $this->mockCustomerRepo
        );
    }

    /* TEST: Placing sale with insufficient stock */
    public function testPlaceSaleThrowsExceptionForInsufficientStock(): void {
        $mockCustomer = $this->createMock(Customer::class);
        $mockCustomer->method('getCustomerId')->willReturn(1);
        $this->mockCustomerRepo->method('findByEmail')->willReturn($mockCustomer);

        $mockCategory = $this->createMock(Category::class);
        $mockCategory->method('getName')->willReturn('Large Bottle');
        $mockCategory->method('canFulfillOrder')->willReturn(false);

        $this->mockCategoryRepo->method('findById')->willReturn($mockCategory);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient stock for Large Bottle');

        $this->service->placeSale('test@example.com', [
            ['categoryId' => 'AA', 'quantity' => 100]
        ]);
    }

    public function testPlaceSaleThrowsExceptionForNonExistentCustomer(): void {
        $this->mockCustomerRepo->method('findByEmail')->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Customer not found');

        $this->service->placeSale('nonexistent@example.com', [
            ['categoryId' => 'AA', 'quantity' => 10]
        ]);
    }

    public function testPlaceSaleThrowsExceptionForNonExistentCategory(): void {
        $mockCustomer = $this->createMock(Customer::class);
        $mockCustomer->method('getCustomerId')->willReturn(1);
        $this->mockCustomerRepo->method('findByEmail')->willReturn($mockCustomer);

        $this->mockCategoryRepo->method('findById')->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Category XX not found');

        $this->service->placeSale('test@example.com', [
            ['categoryId' => 'XX', 'quantity' => 10]
        ]);
    }

    /* TEST: Transaction rollback on failure */
    public function testPlaceSaleRollsBackTransactionOnFailure(): void {
        $mockCustomer = $this->createMock(Customer::class);
        $mockCustomer->method('getCustomerId')->willReturn(1);
        $this->mockCustomerRepo->method('findByEmail')->willReturn($mockCustomer);

        $mockCategory = $this->createMock(Category::class);
        $mockCategory->method('canFulfillOrder')->willReturn(true);
        $mockCategory->method('getPricePer')->willReturn(5.00);

        $this->mockCategoryRepo->method('findById')->willReturn($mockCategory);

        $this->mockSaleRepo->expects($this->once())->method('beginTransaction');
        $this->mockSaleRepo->expects($this->once())
            ->method('save')
            ->willThrowException(new \Exception('Database error'));
        $this->mockSaleRepo->expects($this->once())->method('rollback');
        $this->mockSaleRepo->expects($this->never())->method('commit');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database error');

        $this->service->placeSale('test@example.com', [
            ['categoryId' => 'AA', 'quantity' => 10]
        ]);
    }

    public function testPlaceSaleSuccessfullyCreatesTransaction(): void {
        $mockCustomer = $this->createMock(Customer::class);
        $mockCustomer->method('getCustomerId')->willReturn(1);
        $this->mockCustomerRepo->method('findByEmail')->willReturn($mockCustomer);

        $mockCategory = $this->createMock(Category::class);
        $mockCategory->method('canFulfillOrder')->willReturn(true);
        $mockCategory->method('getPricePer')->willReturn(5.00);

        $this->mockCategoryRepo->method('findById')->willReturn($mockCategory);
        $this->mockSaleRepo->method('save')->willReturn(1);

        $this->mockSaleRepo->expects($this->once())->method('beginTransaction');
        $this->mockSaleRepo->expects($this->once())->method('save');
        $this->mockSaleRepo->expects($this->once())->method('addSaleItem');
        $this->mockCategoryRepo->expects($this->once())->method('reduceStock');
        $this->mockSaleRepo->expects($this->once())->method('commit');
        $this->mockSaleRepo->expects($this->never())->method('rollback');

        $result = $this->service->placeSale('test@example.com', [
            ['categoryId' => 'AA', 'quantity' => 10]
        ]);

        $this->assertInstanceOf(Sale::class, $result);
    }

    /* TEST: Cancel sale restores stock */
    public function testCancelSaleRestoresStockCorrectly(): void {
        $mockSale = $this->createMock(Sale::class);
        $this->mockSaleRepo->method('findById')->willReturn($mockSale);

        $mockSaleItem1 = $this->createMock(SaleItem::class);
        $mockSaleItem1->method('getCategoryId')->willReturn('AA');
        $mockSaleItem1->method('getQuantity')->willReturn(20);

        $mockSaleItem2 = $this->createMock(SaleItem::class);
        $mockSaleItem2->method('getCategoryId')->willReturn('BB');
        $mockSaleItem2->method('getQuantity')->willReturn(15);

        $this->mockSaleRepo->method('findSaleItems')->willReturn([
            $mockSaleItem1,
            $mockSaleItem2
        ]);

        $this->mockSaleRepo->expects($this->once())->method('beginTransaction');
        $this->mockCategoryRepo->expects($this->exactly(2))
            ->method('addStock')
            ->with($this->logicalOr(
                $this->equalTo('AA'),
                $this->equalTo('BB')
            ));
        $this->mockSaleRepo->expects($this->once())->method('deleteSaleItems')->with(1);
        $this->mockSaleRepo->expects($this->once())->method('delete')->with(1);
        $this->mockSaleRepo->expects($this->once())->method('commit');
        $this->mockSaleRepo->expects($this->never())->method('rollback');

        $this->service->cancelSale(1);
    }

    public function testCancelSaleThrowsExceptionForNonExistentSale(): void {
        $this->mockSaleRepo->method('findById')->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Sale does not exist');

        $this->service->cancelSale(999);
    }

    public function testCancelSaleRollsBackOnFailure(): void {
        $mockSale = $this->createMock(Sale::class);
        $this->mockSaleRepo->method('findById')->willReturn($mockSale);

        $mockSaleItem = $this->createMock(SaleItem::class);
        $mockSaleItem->method('getCategoryId')->willReturn('AA');
        $mockSaleItem->method('getQuantity')->willReturn(20);

        $this->mockSaleRepo->method('findSaleItems')->willReturn([$mockSaleItem]);

        $this->mockSaleRepo->expects($this->once())->method('beginTransaction');
        $this->mockCategoryRepo->expects($this->once())
            ->method('addStock')
            ->willThrowException(new \Exception('Stock restoration failed'));
        $this->mockSaleRepo->expects($this->once())->method('rollback');
        $this->mockSaleRepo->expects($this->never())->method('commit');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stock restoration failed');

        $this->service->cancelSale(1);
    }
}
