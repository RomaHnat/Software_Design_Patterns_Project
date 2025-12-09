<?php
namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use CategoryService;
use CategoryRepository;
use Category;

class CategoryServiceTest extends TestCase {
    private CategoryService $service;
    private CategoryRepository|MockObject $mockRepo;

    protected function setUp(): void {
        $this->mockRepo = $this->createMock(CategoryRepository::class);
        $this->service = new CategoryService($this->mockRepo);
    }

    /* TEST: Adding category with invalid ID format */
    public function testAddCategoryThrowsExceptionForInvalidIdFormat(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Category ID must be exactly 2 uppercase letters');

        $this->service->addCategory([
            'cid' => 'A',
            'cname' => 'Test Water',
            'cdescription' => 'Test Description',
            'cnumberof' => 10,
            'cpriceper' => 5.00
        ]);
    }

    public function testAddCategoryThrowsExceptionForInvalidIdWithNumbers(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Category ID must be exactly 2 uppercase letters');

        $this->service->addCategory([
            'cid' => 'A1',
            'cname' => 'Test Water',
            'cdescription' => 'Test Description',
            'cnumberof' => 10,
            'cpriceper' => 5.00
        ]);
    }

    public function testAddCategoryThrowsExceptionForInvalidIdTooLong(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Category ID must be exactly 2 uppercase letters');

        $this->service->addCategory([
            'cid' => 'ABC',
            'cname' => 'Test Water',
            'cdescription' => 'Test Description',
            'cnumberof' => 10,
            'cpriceper' => 5.00
        ]);
    }

    /* Adding category with duplicate ID */
    public function testAddCategoryThrowsExceptionForDuplicateId(): void {
        $existingCategory = $this->createMock(Category::class);
        $this->mockRepo->method('findById')
            ->with('AA')
            ->willReturn($existingCategory);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Primary Key has been violated');

        $this->service->addCategory([
            'cid' => 'AA',
            'cname' => 'Test Water',
            'cdescription' => 'Test Description',
            'cnumberof' => 10,
            'cpriceper' => 5.00
        ]);
    }

    /* TEST: Adding to warehouse increases stock correctly */
    public function testAddToWarehouseIncreasesStock(): void {
        $mockCategory = $this->createMock(Category::class);
        $mockCategory->method('getNumberOf')->willReturn(10);

        $updatedCategory = $this->createMock(Category::class);
        $updatedCategory->method('getNumberOf')->willReturn(60);

        $this->mockRepo->expects($this->exactly(2))
            ->method('findById')
            ->with('AA')
            ->willReturnOnConsecutiveCalls($mockCategory, $updatedCategory);

        $this->mockRepo->expects($this->once())
            ->method('addStock')
            ->with('AA', 50);

        $result = $this->service->addToWarehouse('AA', 50);

        $this->assertEquals(60, $result->getNumberOf());
    }

    public function testAddToWarehouseThrowsExceptionForNonExistentCategory(): void {
        $this->mockRepo->method('findById')->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Category not found');

        $this->service->addToWarehouse('XX', 50);
    }

    public function testAddToWarehouseThrowsExceptionForNegativeQuantity(): void {
        $mockCategory = $this->createMock(Category::class);
        $this->mockRepo->method('findById')->willReturn($mockCategory);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Quantity must be greater than zero');

        $this->service->addToWarehouse('AA', -10);
    }

    public function testAddToWarehouseThrowsExceptionForZeroQuantity(): void {
        $mockCategory = $this->createMock(Category::class);
        $this->mockRepo->method('findById')->willReturn($mockCategory);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Quantity must be greater than zero');

        $this->service->addToWarehouse('AA', 0);
    }

    /* TEST: Updating category with ID change */
    public function testUpdateCategoryWithIdChange(): void {
        $existingCategory = $this->createMock(Category::class);
        $existingCategory->method('getCategoryId')->willReturn('AA');

        $newCategory = $this->createMock(Category::class);
        $newCategory->method('getCategoryId')->willReturn('BB');

        $callCount = 0;
        $this->mockRepo->expects($this->exactly(3))
            ->method('findById')
            ->willReturnCallback(function($id) use ($existingCategory, $newCategory, &$callCount) {
                $callCount++;
                if ($callCount === 1 && $id === 'AA') return $existingCategory;
                if ($callCount === 2 && $id === 'BB') return null;
                if ($callCount === 3 && $id === 'BB') return $newCategory;
                return null;
            });

        $this->mockRepo->expects($this->once())
            ->method('delete')
            ->with('AA');

        $this->mockRepo->expects($this->once())
            ->method('save');

        $result = $this->service->updateCategory('AA', [
            'cid' => 'BB',
            'cname' => 'Updated Name',
            'cdescription' => 'Updated Description',
            'cnumberof' => 20,
            'cpriceper' => 10.00
        ]);

        $this->assertInstanceOf(Category::class, $result);
    }

    /* TEST: Deleting category with foreign key constraint */
    public function testDeleteCategoryThrowsExceptionWhenForeignKeyConstraintViolated(): void {
        $mockCategory = $this->createMock(Category::class);
        $this->mockRepo->method('findById')->willReturn($mockCategory);

        $pdoException = new \PDOException('Foreign key constraint fails');
        $pdoException->errorInfo = ['23000', 1451, 'Foreign key constraint fails'];

        $reflection = new \ReflectionClass($pdoException);
        $property = $reflection->getProperty('code');
        $property->setAccessible(true);
        $property->setValue($pdoException, '23000');

        $this->mockRepo->expects($this->once())
            ->method('delete')
            ->willThrowException($pdoException);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot delete category with existing sales');

        $this->service->deleteCategory('AA');
    }

    public function testDeleteCategoryThrowsExceptionForNonExistentCategory(): void {
        $this->mockRepo->method('findById')->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Category not found');

        $this->service->deleteCategory('XX');
    }

    public function testValidateCategoryDataReturnsErrorsForEmptyFields(): void {
        $errors = $this->service->validateCategoryData([
            'cid' => '',
            'cname' => '',
            'cdescription' => '',
            'cnumberof' => -1,
            'cpriceper' => 0
        ]);

        $this->assertArrayHasKey('cid', $errors);
        $this->assertArrayHasKey('cname', $errors);
        $this->assertArrayHasKey('cdescription', $errors);
        $this->assertArrayHasKey('cnumberof', $errors);
        $this->assertArrayHasKey('cpriceper', $errors);
    }

    public function testValidateCategoryDataReturnsEmptyArrayForValidData(): void {
        $errors = $this->service->validateCategoryData([
            'cid' => 'AA',
            'cname' => 'Test Water',
            'cdescription' => 'Test Description',
            'cnumberof' => 10,
            'cpriceper' => 5.00
        ]);

        $this->assertEmpty($errors);
    }
}
