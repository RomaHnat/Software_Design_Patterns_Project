<?php
class CustomerRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM customers");
        $rows = $stmt->fetchAll();

        return array_map(
            fn($row) => Customer::fromDatabase($row),
            $rows
        );
    }

    public function findById(int $id): ?Customer {
        $stmt = $this->db->prepare(
            "SELECT * FROM customers WHERE CustomerID = :id"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? Customer::fromDatabase($row) : null;
    }

    public function findByEmail(string $email): ?Customer {
        $stmt = $this->db->prepare(
            "SELECT * FROM customers WHERE Email = :email"
        );
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();

        return $row ? Customer::fromDatabase($row) : null;
    }

    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM customers WHERE Email = :email"
        );
        $stmt->execute([':email' => $email]);

        return $stmt->fetchColumn() > 0;
    }

    public function save(Customer $customer): int {
        $stmt = $this->db->prepare("
            INSERT INTO customers
            (F_Name, S_Name, Email)
            VALUES (:fname, :sname, :email)
        ");

        $stmt->execute([
            ':fname' => $customer->getFirstName(),
            ':sname' => $customer->getSurname(),
            ':email' => $customer->getEmail()
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updateByEmail(string $oldEmail, Customer $customer): void {
        $stmt = $this->db->prepare("
            UPDATE customers
            SET F_Name = :fname,
                S_Name = :sname,
                Email = :email
            WHERE Email = :old_email
        ");

        $stmt->execute([
            ':fname' => $customer->getFirstName(),
            ':sname' => $customer->getSurname(),
            ':email' => $customer->getEmail(),
            ':old_email' => $oldEmail
        ]);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare(
            "DELETE FROM customers WHERE CustomerID = :id"
        );
        $stmt->execute([':id' => $id]);
    }
}
