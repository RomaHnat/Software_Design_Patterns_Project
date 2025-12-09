<?php
class SaleRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findAll(): array {
        $stmt = $this->db->query("
            SELECT s.*, c.F_Name, c.S_Name, c.Email
            FROM sales s
            JOIN customers c ON s.CustomerID = c.CustomerID
            ORDER BY s.Date_Sold DESC
        ");
        $rows = $stmt->fetchAll();

        return array_map(
            fn($row) => Sale::fromDatabase($row),
            $rows
        );
    }

    public function findById(int $id): ?Sale {
        $stmt = $this->db->prepare("
            SELECT * FROM sales WHERE SaleID = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $sale = Sale::fromDatabase($row);

        $items = $this->findSaleItems($id);
        $sale->setItems($items);

        return $sale;
    }

    public function findSaleItems(int $saleId): array {
        $stmt = $this->db->prepare("
            SELECT si.*, c.Price_Per
            FROM salesitems si
            JOIN categories c ON si.CategoryID = c.CategoryID
            WHERE si.SaleID = :sale_id
        ");
        $stmt->execute([':sale_id' => $saleId]);
        $rows = $stmt->fetchAll();

        return array_map(
            fn($row) => SaleItem::fromDatabase($row),
            $rows
        );
    }

    public function save(Sale $sale): int {
        $stmt = $this->db->prepare("
            INSERT INTO sales
            (Price, Date_Sold, CustomerID)
            VALUES (:price, :date, :customer_id)
        ");

        $stmt->execute([
            ':price' => $sale->getPrice(),
            ':date' => $sale->getDateSold(),
            ':customer_id' => $sale->getCustomerId()
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function addSaleItem(int $saleId, string $categoryId, int $quantity): void {
        $stmt = $this->db->prepare("
            INSERT INTO salesitems
            (SaleID, CategoryID, Number_Of_Cat)
            VALUES (:sale_id, :category_id, :quantity)
        ");

        $stmt->execute([
            ':sale_id' => $saleId,
            ':category_id' => $categoryId,
            ':quantity' => $quantity
        ]);
    }

    public function deleteSaleItems(int $saleId): void {
        $stmt = $this->db->prepare(
            "DELETE FROM salesitems WHERE SaleID = :sale_id"
        );
        $stmt->execute([':sale_id' => $saleId]);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare(
            "DELETE FROM sales WHERE SaleID = :id"
        );
        $stmt->execute([':id' => $id]);
    }

    public function beginTransaction(): void {
        $this->db->beginTransaction();
    }

    public function commit(): void {
        $this->db->commit();
    }

    public function rollback(): void {
        $this->db->rollBack();
    }
}
