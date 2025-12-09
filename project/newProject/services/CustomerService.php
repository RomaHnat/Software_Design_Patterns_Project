<?php
class CustomerService {
    private CustomerRepository $customerRepo;

    public function __construct(CustomerRepository $customerRepo) {
        $this->customerRepo = $customerRepo;
    }

    public function getAllCustomers(): array {
        return $this->customerRepo->findAll();
    }

    public function getCustomerByEmail(string $email): ?Customer {
        return $this->customerRepo->findByEmail($email);
    }

    public function addCustomer(array $formData): Customer {

        if ($this->customerRepo->emailExists($formData['cemail'])) {
            throw new DuplicateKeyException('Email already exists in the system. Client was not registered.');
        }

        if (!filter_var($formData['cemail'], FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Invalid email format.');
        }

        if (empty($formData['cfname']) || empty($formData['csname']) || empty($formData['cemail'])) {
            throw new ValidationException('All fields are required.');
        }

        $customer = Customer::fromFormData($formData);

        $customerId = $this->customerRepo->save($customer);

        return $this->customerRepo->findById($customerId);
    }

    public function updateCustomer(string $oldEmail, array $formData): Customer {

        $existingCustomer = $this->customerRepo->findByEmail($oldEmail);
        if (!$existingCustomer) {
            throw new NotFoundException('Customer not found.');
        }

        if ($oldEmail !== $formData['cemail'] && $this->customerRepo->emailExists($formData['cemail'])) {
            throw new DuplicateKeyException('Email already exists in the system. Client was not updated.');
        }

        if (!filter_var($formData['cemail'], FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Invalid email format.');
        }

        if (empty($formData['cfname']) || empty($formData['csname']) || empty($formData['cemail'])) {
            throw new ValidationException('All fields are required.');
        }

        $customer = Customer::fromFormData($formData);

        $this->customerRepo->updateByEmail($oldEmail, $customer);

        return $this->customerRepo->findByEmail($formData['cemail']);
    }

    public function deleteCustomer(int $id): void {
        $customer = $this->customerRepo->findById($id);
        if (!$customer) {
            throw new NotFoundException('Customer not found.');
        }

        try {
            $this->customerRepo->delete($id);
        } catch (\PDOException $e) {
            // Foreign key constraint violation
            if ($e->getCode() == '23000') {
                throw new ValidationException('Cannot delete customer with existing sales. Please cancel all sales first.');
            }
            throw $e;
        }
    }

    public function validateCustomerData(array $data): array {
        $errors = [];

        if (empty($data['cfname'])) {
            $errors['cfname'] = 'First name is required';
        }

        if (empty($data['csname'])) {
            $errors['csname'] = 'Surname is required';
        }

        if (empty($data['cemail'])) {
            $errors['cemail'] = 'Email is required';
        } elseif (!filter_var($data['cemail'], FILTER_VALIDATE_EMAIL)) {
            $errors['cemail'] = 'Invalid email format';
        }

        return $errors;
    }
}
