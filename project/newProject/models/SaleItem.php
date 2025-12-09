<?php
class SaleItem {
    private int $saleId;
    private string $categoryId;
    private int $quantity;
    private ?float $pricePerUnit = null;

    private function __construct(
        int $saleId,
        string $categoryId,
        int $quantity,
        ?float $pricePerUnit = null
    ) {
        $this->saleId = $saleId;
        $this->categoryId = $categoryId;
        $this->quantity = $quantity;
        $this->pricePerUnit = $pricePerUnit;
    }

    public static function fromDatabase(array $row): self {
        return new self(
            (int)$row['SaleID'],
            $row['CategoryID'],
            (int)$row['Number_Of_Cat'],
            isset($row['Price_Per']) ? (float)$row['Price_Per'] : null
        );
    }

    public static function create(
        int $saleId,
        string $categoryId,
        int $quantity,
        float $pricePerUnit
    ): self {
        return new self(
            $saleId,
            $categoryId,
            $quantity,
            $pricePerUnit
        );
    }

    public function setPricePerUnit(float $price): void {
        $this->pricePerUnit = $price;
    }

    public function getTotalPrice(): float {
        if ($this->pricePerUnit === null) {
            return 0.0;
        }
        return $this->quantity * $this->pricePerUnit;
    }

    public function getSaleId(): int {
        return $this->saleId;
    }

    public function getCategoryId(): string {
        return $this->categoryId;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function getPricePerUnit(): ?float {
        return $this->pricePerUnit;
    }
}
