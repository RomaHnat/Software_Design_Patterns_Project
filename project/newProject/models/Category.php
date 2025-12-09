<?php
class Category {
    private string $categoryId;
    private string $name;
    private string $description;
    private int $numberOf;
    private float $pricePer;

    private function __construct(
        string $categoryId,
        string $name,
        string $description,
        int $numberOf,
        float $pricePer
    ) {
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->description = $description;
        $this->numberOf = $numberOf;
        $this->pricePer = $pricePer;
    }

    public static function fromDatabase(array $row): self {
        return new self(
            $row['CategoryID'],
            $row['Name'],
            $row['Description'],
            (int)$row['Number_Of'],
            (float)$row['Price_Per']
        );
    }

    public static function fromFormData(array $data): self {
        return new self(
            strtoupper($data['cid']),
            ucwords($data['cname']),
            $data['cdescription'],
            (int)$data['cnumberof'],
            (float)$data['cpriceper']
        );
    }

    public function getTotalValue(): float {
        return $this->numberOf * $this->pricePer;
    }

    public function isInStock(): bool {
        return $this->numberOf > 0;
    }

    public function canFulfillOrder(int $quantity): bool {
        return $this->numberOf >= $quantity;
    }

    public function getCategoryId(): string { return $this->categoryId; }
    public function getName(): string { return $this->name; }
    public function getDescription(): string { return $this->description; }
    public function getNumberOf(): int { return $this->numberOf; }
    public function getPricePer(): float { return $this->pricePer; }
}
