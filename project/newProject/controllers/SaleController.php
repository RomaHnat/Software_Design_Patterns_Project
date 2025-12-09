<?php
class SaleController extends BaseController {
    private SaleService $saleService;

    public function __construct() {
        $db = Database::getInstance()->getConnection();
        $saleRepo = new SaleRepository($db);
        $categoryRepo = new CategoryRepository($db);
        $customerRepo = new CustomerRepository($db);

        $this->saleService = new SaleService($saleRepo, $categoryRepo, $customerRepo);
    }

    public function index(): void {
        try {
            $db = Database::getInstance()->getConnection();
            $saleRepo = new SaleRepository($db);
            $saleRepo->findAll();

            include_once __DIR__ . '/../views/' . VIEW_SALES_INDEX;
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    public function store(): void {
        $this->validatePostRequest();

        try {
            $customerEmail = $_POST['cclientsemail'] ?? '';
            $cartData = $_POST['cart'] ?? '';

            $cartLines = array_filter(explode("\n", $cartData));
            $cartItems = [];

            foreach ($cartLines as $line) {
                $parts = explode(" - ", trim($line));
                if (count($parts) >= 2) {
                    $categoryId = substr($parts[0], 0, 2);
                    $quantity = (int)$parts[1];

                    $cartItems[] = [
                        'categoryId' => $categoryId,
                        'quantity' => $quantity
                    ];
                }
            }

            if (empty($cartItems)) {
                throw new ValidationException("Cart is empty");
            }

            $sale = $this->saleService->placeSale($customerEmail, $cartItems);

            $this->handleSuccessResponse(
                ['saleId' => $sale->getSaleId()],
                'Sale has been placed',
                VIEW_SALES_INDEX
            );
        } catch (Exception $e) {
            $this->handleErrorResponse($e, VIEW_SALES_INDEX);
        }
    }

    public function cancel(string $id = ''): void {
        $saleId = !empty($id) ? (int)$id : (int)($_POST['csaleid'] ?? 0);

        try {
            if ($saleId <= 0) {
                throw new NotFoundException(ERROR_SALE_NOT_FOUND);
            }

            $this->saleService->cancelSale($saleId);

            $this->handleSuccessResponse(
                ['saleId' => $saleId],
                'Sale has been canceled',
                VIEW_SALES_INDEX
            );
        } catch (Exception $e) {
            $this->handleErrorResponse($e, VIEW_SALES_INDEX);
        }
    }
}
