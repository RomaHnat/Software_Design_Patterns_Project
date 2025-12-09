<?php
class Sale {
    private ?int $saleId;
    private float $price;
    private string $dateSold;
    private int $customerId;
    private array $items = [];

    private function __construct(
        ?int $saleId,
        float $price,
        string $dateSold,
        int $customerId
    ) {
        $this->saleId = $saleId;
        $this->price = $price;
        $this->dateSold = $dateSold;
        $this->customerId = $customerId;
    }

    public static function fromDatabase(array $row): self {
        return new self(
            (int)$row['SaleID'],
            (float)$row['Price'],
            $row['Date_Sold'],
            (int)$row['CustomerID']
        );
    }

    public static function create(
        int $customerId,
        float $price,
        \DateTime $dateSold
    ): self {
        return new self(
            null,
            $price,
            $dateSold->format('Y-m-d'),
            $customerId
        );
    }

    public function setItems(array $items): void {
        $this->items = $items;
    }

    public function getSaleId(): ?int {
        return $this->saleId;
    }

    public function getPrice(): float {
        return $this->price;
    }

    public function getDateSold(): string {
        return $this->dateSold;
    }

    public function getCustomerId(): int {
        return $this->customerId;
    }

    public function getItems(): array {
        return $this->items;
    }
}
