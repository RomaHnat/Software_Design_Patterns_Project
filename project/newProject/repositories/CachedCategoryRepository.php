<?php
class CachedCategoryRepository extends CategoryRepository {
    private array $cacheById = [];
    private ?array $cacheAll = null;

    public function findAll(): array {
        if ($this->cacheAll === null) {
            $this->cacheAll = parent::findAll();
        }
        return $this->cacheAll;
    }

    public function findById(string $id): ?Category {
        if (!isset($this->cacheById[$id])) {
            $this->cacheById[$id] = parent::findById($id);
        }
        return $this->cacheById[$id];
    }

    public function save(Category $category): void {
        parent::save($category);
        $this->invalidateCache();
    }

    public function update(Category $category): void {
        parent::update($category);
        $this->invalidateCache();
    }

    public function delete(string $id): void {
        parent::delete($id);
        $this->invalidateCache();
    }

    public function addStock(string $id, int $quantity): void {
        parent::addStock($id, $quantity);
        $this->invalidateCache();
    }

    public function reduceStock(string $id, int $quantity): void {
        parent::reduceStock($id, $quantity);
        $this->invalidateCache();
    }

    private function invalidateCache(): void {
        $this->cacheById = [];
        $this->cacheAll = null;
    }
}
