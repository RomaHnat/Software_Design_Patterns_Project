<?php
class CategoryService {
    private CategoryRepository $categoryRepo;

    public function __construct(CategoryRepository $categoryRepo) {
        $this->categoryRepo = $categoryRepo;
    }

    public function getAllCategories(): array {
        return $this->categoryRepo->findAll();
    }

    public function getCategoryById(string $id): ?Category {
        return $this->categoryRepo->findById($id);
    }

    public function addCategory(array $formData): Category {

        if (!preg_match(CATEGORY_ID_PATTERN, strtoupper($formData['cid']))) {
            throw new ValidationException('Category ID must be exactly 2 uppercase letters.');
        }

        $existing = $this->categoryRepo->findById(strtoupper($formData['cid']));
        if ($existing) {
            throw new DuplicateKeyException('Primary Key has been violated. Category ID already exists.');
        }

        $errors = $this->validateCategoryData($formData);
        if (!empty($errors)) {
            throw new ValidationException('Validation failed: ' . implode(', ', $errors));
        }

        $category = Category::fromFormData($formData);

        $this->categoryRepo->save($category);

        return $this->categoryRepo->findById($category->getCategoryId());
    }

    public function updateCategory(string $oldCategoryId, array $formData): Category {

        $existingCategory = $this->categoryRepo->findById($oldCategoryId);
        if (!$existingCategory) {
            throw new NotFoundException(ERROR_CATEGORY_NOT_FOUND);
        }

        if (!preg_match('/^[A-Z]{2}$/', strtoupper($formData['cid']))) {
            throw new ValidationException('Category ID must be exactly 2 uppercase letters.');
        }

        if ($oldCategoryId !== strtoupper($formData['cid'])) {
            $newIdExists = $this->categoryRepo->findById(strtoupper($formData['cid']));
            if ($newIdExists) {
                throw new DuplicateKeyException('Primary Key has been violated. New category ID already exists.');
            }
        }

        $errors = $this->validateCategoryData($formData);
        if (!empty($errors)) {
            throw new ValidationException('Validation failed: ' . implode(', ', $errors));
        }

        $category = Category::fromFormData($formData);

        if ($oldCategoryId !== $category->getCategoryId()) {
            $this->categoryRepo->delete($oldCategoryId);
            $this->categoryRepo->save($category);
        } else {
            $this->categoryRepo->update($category);
        }

        return $this->categoryRepo->findById($category->getCategoryId());
    }

    public function deleteCategory(string $id): void {
        $category = $this->categoryRepo->findById($id);
        if (!$category) {
            throw new NotFoundException(ERROR_CATEGORY_NOT_FOUND);
        }

        try {
            $this->categoryRepo->delete($id);
        } catch (\PDOException $e) {
            // Foreign key constraint violation
            if ($e->getCode() == '23000') {
                throw new ValidationException('Cannot delete category with existing sales. Please remove all sales first.');
            }
            throw $e;
        }
    }

    public function addToWarehouse(string $categoryId, int $quantity): Category {
        $category = $this->categoryRepo->findById($categoryId);
        if (!$category) {
            throw new NotFoundException(ERROR_CATEGORY_NOT_FOUND);
        }

        if ($quantity <= 0) {
            throw new ValidationException('Quantity must be greater than zero.');
        }

        $this->categoryRepo->addStock($categoryId, $quantity);

        return $this->categoryRepo->findById($categoryId);
    }

    public function getCategoriesForSelect(): array {
        $categories = $this->categoryRepo->findAll();
        $options = [];

        foreach ($categories as $category) {
            $options[] = [
                'id' => $category->getCategoryId(),
                'label' => $category->getCategoryId() . ' - ' . $category->getName(),
                'inStock' => $category->isInStock(),
                'available' => $category->getNumberOf()
            ];
        }

        return $options;
    }

    public function validateCategoryData(array $data): array {
        $errors = [];

        if (empty($data['cid'])) {
            $errors['cid'] = 'Category ID is required';
        } elseif (!preg_match('/^[A-Z]{2}$/', strtoupper($data['cid']))) {
            $errors['cid'] = 'Category ID must be exactly 2 uppercase letters';
        }

        if (empty($data['cname'])) {
            $errors['cname'] = 'Category name is required';
        }

        if (empty($data['cdescription'])) {
            $errors['cdescription'] = 'Description is required';
        }

        if (!isset($data['cnumberof']) || $data['cnumberof'] < 0) {
            $errors['cnumberof'] = 'Number of items must be zero or greater';
        }

        if (!isset($data['cpriceper']) || $data['cpriceper'] <= 0) {
            $errors['cpriceper'] = 'Price per item must be greater than zero';
        }

        return $errors;
    }
}
