<?php
class CategoryRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM categories");
        $rows = $stmt->fetchAll();

        return array_map(
            fn($row) => Category::fromDatabase($row),
            $rows
        );
    }

    public function findById(string $id): ?Category {
        $stmt = $this->db->prepare(
            "SELECT * FROM categories WHERE CategoryID = :id"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? Category::fromDatabase($row) : null;
    }

    public function save(Category $category): void {
        $stmt = $this->db->prepare("
            INSERT INTO categories
            (CategoryID, Name, Description, Number_Of, Price_Per)
            VALUES (:id, :name, :desc, :num, :price)
        ");

        $stmt->execute([
            ':id' => $category->getCategoryId(),
            ':name' => $category->getName(),
            ':desc' => $category->getDescription(),
            ':num' => $category->getNumberOf(),
            ':price' => $category->getPricePer()
        ]);
    }

    public function update(Category $category): void {
        $stmt = $this->db->prepare("
            UPDATE categories
            SET Name = :name,
                Description = :desc,
                Number_Of = :num,
                Price_Per = :price
            WHERE CategoryID = :id
        ");

        $stmt->execute([
            ':id' => $category->getCategoryId(),
            ':name' => $category->getName(),
            ':desc' => $category->getDescription(),
            ':num' => $category->getNumberOf(),
            ':price' => $category->getPricePer()
        ]);
    }

    public function delete(string $id): void {
        $stmt = $this->db->prepare(
            "DELETE FROM categories WHERE CategoryID = :id"
        );
        $stmt->execute([':id' => $id]);
    }

    public function addStock(string $id, int $quantity): void {
        $stmt = $this->db->prepare("
            UPDATE categories
            SET Number_Of = Number_Of + :qty
            WHERE CategoryID = :id
        ");
        $stmt->execute([':id' => $id, ':qty' => $quantity]);
    }

    public function reduceStock(string $id, int $quantity): void {
        $stmt = $this->db->prepare("
            UPDATE categories
            SET Number_Of = Number_Of - :qty
            WHERE CategoryID = :id
        ");
        $stmt->execute([':id' => $id, ':qty' => $quantity]);
    }
}
