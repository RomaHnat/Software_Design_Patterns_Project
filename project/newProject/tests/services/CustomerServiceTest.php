<?php
namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use CustomerService;
use CustomerRepository;
use Customer;

class CustomerServiceTest extends TestCase {
    private CustomerService $service;
    private CustomerRepository|MockObject $mockRepo;

    protected function setUp(): void {
        $this->mockRepo = $this->createMock(CustomerRepository::class);
        $this->service = new CustomerService($this->mockRepo);
    }

    /* TEST: Email validation */
    public function testAddCustomerThrowsExceptionForInvalidEmail(): void {
        $this->mockRepo->method('emailExists')->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid email format');

        $this->service->addCustomer([
            'cfname' => 'John',
            'csname' => 'Doe',
            'cemail' => 'notanemail'
        ]);
    }

    public function testAddCustomerThrowsExceptionForEmailWithoutDomain(): void {
        $this->mockRepo->method('emailExists')->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid email format');

        $this->service->addCustomer([
            'cfname' => 'John',
            'csname' => 'Doe',
            'cemail' => 'test@'
        ]);
    }

    public function testAddCustomerThrowsExceptionForEmailWithoutUsername(): void {
        $this->mockRepo->method('emailExists')->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid email format');

        $this->service->addCustomer([
            'cfname' => 'John',
            'csname' => 'Doe',
            'cemail' => '@test.com'
        ]);
    }

    public function testAddCustomerThrowsExceptionForEmptyFields(): void {
        $this->mockRepo->method('emailExists')->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('All fields are required');

        $this->service->addCustomer([
            'cfname' => '',
            'csname' => 'Doe',
            'cemail' => 'test@example.com'
        ]);
    }

    /* TEST: Duplicate email prevention */
    public function testAddCustomerThrowsExceptionForDuplicateEmail(): void {
        $this->mockRepo->method('emailExists')->willReturn(true);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Email already exists in the system');

        $this->service->addCustomer([
            'cfname' => 'John',
            'csname' => 'Doe',
            'cemail' => 'existing@example.com'
        ]);
    }

    public function testAddCustomerSucceedsWithValidData(): void {
        $this->mockRepo->method('emailExists')->willReturn(false);
        $this->mockRepo->method('save')->willReturn(1);

        $mockCustomer = $this->createMock(Customer::class);
        $mockCustomer->method('getEmail')->willReturn('test@example.com');

        $this->mockRepo->method('findById')->willReturn($mockCustomer);

        $result = $this->service->addCustomer([
            'cfname' => 'John',
            'csname' => 'Doe',
            'cemail' => 'test@example.com'
        ]);

        $this->assertInstanceOf(Customer::class, $result);
    }

    /* Update customer tests */
    public function testUpdateCustomerThrowsExceptionForNonExistentCustomer(): void {
        $this->mockRepo->method('findByEmail')->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Customer not found');

        $this->service->updateCustomer('nonexistent@example.com', [
            'cfname' => 'John',
            'csname' => 'Doe',
            'cemail' => 'test@example.com'
        ]);
    }

    public function testUpdateCustomerThrowsExceptionWhenChangingToExistingEmail(): void {
        $existingCustomer = $this->createMock(Customer::class);

        $this->mockRepo->expects($this->once())
            ->method('findByEmail')
            ->with('old@example.com')
            ->willReturn($existingCustomer);

        $this->mockRepo->expects($this->once())
            ->method('emailExists')
            ->with('new@example.com')
            ->willReturn(true);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Email already exists in the system');

        $this->service->updateCustomer('old@example.com', [
            'cfname' => 'John',
            'csname' => 'Doe',
            'cemail' => 'new@example.com'
        ]);
    }

    public function testUpdateCustomerSucceedsWhenKeepingSameEmail(): void {
        $existingCustomer = $this->createMock(Customer::class);
        $updatedCustomer = $this->createMock(Customer::class);

        $this->mockRepo->expects($this->exactly(2))
            ->method('findByEmail')
            ->willReturnOnConsecutiveCalls($existingCustomer, $updatedCustomer);

        $result = $this->service->updateCustomer('test@example.com', [
            'cfname' => 'Jane',
            'csname' => 'Smith',
            'cemail' => 'test@example.com'
        ]);

        $this->assertInstanceOf(Customer::class, $result);
    }


    /* Validation tests */
    public function testValidateCustomerDataReturnsErrorsForEmptyFields(): void {
        $errors = $this->service->validateCustomerData([
            'cfname' => '',
            'csname' => '',
            'cemail' => ''
        ]);

        $this->assertArrayHasKey('cfname', $errors);
        $this->assertArrayHasKey('csname', $errors);
        $this->assertArrayHasKey('cemail', $errors);
    }

    public function testValidateCustomerDataReturnsErrorForInvalidEmail(): void {
        $errors = $this->service->validateCustomerData([
            'cfname' => 'John',
            'csname' => 'Doe',
            'cemail' => 'invalidemail'
        ]);

        $this->assertArrayHasKey('cemail', $errors);
        $this->assertEquals('Invalid email format', $errors['cemail']);
    }

    public function testValidateCustomerDataReturnsEmptyArrayForValidData(): void {
        $errors = $this->service->validateCustomerData([
            'cfname' => 'John',
            'csname' => 'Doe',
            'cemail' => 'john.doe@example.com'
        ]);

        $this->assertEmpty($errors);
    }
}
