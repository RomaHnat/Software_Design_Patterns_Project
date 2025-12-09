<?php
class CustomerController extends BaseController {
    private CustomerService $customerService;

    public function __construct() {
        $db = Database::getInstance()->getConnection();
        $customerRepo = new CustomerRepository($db);
        $this->customerService = new CustomerService($customerRepo);
    }

    public function index(): void {
        try {
            $this->customerService->getAllCustomers();
            include_once __DIR__ . '/../views/' . VIEW_CUSTOMERS_INDEX;
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    public function store(): void {
        $this->validatePostRequest();

        try {
            $customer = $this->customerService->addCustomer($_POST);

            $this->handleSuccessResponse(
                [
                    'customer' => [
                        'id' => $customer->getCustomerId(),
                        'firstName' => $customer->getFirstName(),
                        'surname' => $customer->getSurname(),
                        'email' => $customer->getEmail()
                    ]
                ],
                "Client with email {$customer->getEmail()} has been added",
                VIEW_CUSTOMERS_INDEX
            );
        } catch (Exception $e) {
            $this->handleErrorResponse($e, VIEW_CUSTOMERS_INDEX);
        }
    }

    public function update(string $email = ''): void {
        $oldEmail = !empty($email) ? urldecode($email) : ($_POST['cclemail'] ?? '');

        if (empty($oldEmail)) {
            echo "<script>window.alert('Client email is required');</script>";
            return;
        }

        try {
            $customer = $this->customerService->updateCustomer($oldEmail, $_POST);

            $this->handleSuccessResponse(
                ['customerId' => $customer->getCustomerId()],
                "Client with email {$oldEmail} has been updated",
                VIEW_CUSTOMERS_INDEX
            );
        } catch (Exception $e) {
            $this->handleErrorResponse($e, VIEW_CUSTOMERS_INDEX);
        }
    }
}
