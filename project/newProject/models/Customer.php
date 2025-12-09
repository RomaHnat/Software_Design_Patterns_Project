<?php
class Customer {
    private ?int $customerId;
    private string $firstName;
    private string $surname;
    private string $email;

    private function __construct(
        ?int $customerId,
        string $firstName,
        string $surname,
        string $email
    ) {
        $this->customerId = $customerId;
        $this->firstName = $firstName;
        $this->surname = $surname;
        $this->email = $email;
    }

    public static function fromDatabase(array $row): self {
        return new self(
            (int)$row['CustomerID'],
            $row['F_Name'],
            $row['S_Name'],
            $row['Email']
        );
    }

    public static function fromFormData(array $data): self {
        return new self(
            null,
            ucfirst($data['cfname']),
            ucfirst($data['csname']),
            strtolower(trim($data['cemail']))
        );
    }

    public function getCustomerId(): ?int {
        return $this->customerId;
    }

    public function getFirstName(): string {
        return $this->firstName;
    }

    public function getSurname(): string {
        return $this->surname;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setFirstName(string $firstName): void {
        $this->firstName = ucfirst($firstName);
    }

    public function setSurname(string $surname): void {
        $this->surname = ucfirst($surname);
    }

    public function setEmail(string $email): void {
        $this->email = strtolower(trim($email));
    }
}
