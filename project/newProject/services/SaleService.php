<?php
class SaleService {
    private SaleRepository $saleRepo;
    private CategoryRepository $categoryRepo;
    private CustomerRepository $customerRepo;

    public function __construct(
        SaleRepository $saleRepo,
        CategoryRepository $categoryRepo,
        CustomerRepository $customerRepo
    ) {
        $this->saleRepo = $saleRepo;
        $this->categoryRepo = $categoryRepo;
        $this->customerRepo = $customerRepo;
    }

    public function placeSale(
        string $customerEmail,
        array $cartItems
    ): Sale {
        $customer = $this->customerRepo->findByEmail($customerEmail);
        if (!$customer) {
            throw new NotFoundException("Customer not found");
        }

        $this->validateStock($cartItems);

        $totalPrice = $this->calculateTotalPrice($cartItems);

        try {
            $this->saleRepo->beginTransaction();

            $sale = Sale::create(
                $customer->getCustomerId(),
                $totalPrice,
                new DateTime()
            );

            $saleId = $this->saleRepo->save($sale);

            foreach ($cartItems as $item) {
                $this->categoryRepo->findById($item['categoryId']);

                $this->saleRepo->addSaleItem(
                    $saleId,
                    $item['categoryId'],
                    $item['quantity']
                );

                $this->categoryRepo->reduceStock(
                    $item['categoryId'],
                    $item['quantity']
                );
            }

            $this->saleRepo->commit();

            return $sale;

        } catch (\Exception $e) {
            $this->saleRepo->rollback();
            throw $e;
        }
    }

    public function cancelSale(int $saleId): void {
        $sale = $this->saleRepo->findById($saleId);
        if (!$sale) {
            throw new NotFoundException("Sale does not exist");
        }

        $saleItems = $this->saleRepo->findSaleItems($saleId);

        try {
            $this->saleRepo->beginTransaction();

            foreach ($saleItems as $item) {
                $this->categoryRepo->addStock(
                    $item->getCategoryId(),
                    $item->getQuantity()
                );
            }

            $this->saleRepo->deleteSaleItems($saleId);
            $this->saleRepo->delete($saleId);

            $this->saleRepo->commit();

        } catch (\Exception $e) {
            $this->saleRepo->rollback();
            throw $e;
        }
    }

    private function validateStock(array $cartItems): void {
        foreach ($cartItems as $item) {
            $category = $this->categoryRepo->findById($item['categoryId']);

            if (!$category) {
                throw new NotFoundException(
                    "Category {$item['categoryId']} not found"
                );
            }

            if (!$category->canFulfillOrder($item['quantity'])) {
                throw new InsufficientStockException(
                    "Insufficient stock for {$category->getName()}"
                );
            }
        }
    }

    private function calculateTotalPrice(array $cartItems): float {
        $total = 0.0;

        foreach ($cartItems as $item) {
            $category = $this->categoryRepo->findById($item['categoryId']);
            $total += $category->getPricePer() * $item['quantity'];
        }

        return $total;
    }
}
