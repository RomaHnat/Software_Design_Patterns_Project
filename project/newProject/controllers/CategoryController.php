<?php
class CategoryController extends BaseController {
    private CategoryService $categoryService;

    public function __construct() {
        $db = Database::getInstance()->getConnection();
        $categoryRepo = new CachedCategoryRepository($db);
        $this->categoryService = new CategoryService($categoryRepo);
    }

    public function index(): void {
        try {
            $this->categoryService->getAllCategories();
            include_once __DIR__ . '/../views/' . VIEW_CATEGORIES_INDEX;
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    public function store(): void {
        $this->validatePostRequest();

        try {
            $category = $this->categoryService->addCategory($_POST);

            $this->handleSuccessResponse(
                [
                    'category' => [
                        'id' => $category->getCategoryId(),
                        'name' => $category->getName(),
                        'description' => $category->getDescription(),
                        'numberOf' => $category->getNumberOf(),
                        'pricePer' => $category->getPricePer()
                    ]
                ],
                "Category with ID {$category->getCategoryId()} has been added",
                VIEW_CATEGORIES_INDEX
            );
        } catch (Exception $e) {
            $this->handleErrorResponse($e, VIEW_CATEGORIES_INDEX);
        }
    }

    public function update(string $id = ''): void {
        $oldId = !empty($id) ? $id : ($_POST['category'] ?? '');

        if (empty($oldId)) {
            echo "<script>window.alert('Category ID is required');</script>";
            return;
        }

        try {
            $category = $this->categoryService->updateCategory($oldId, $_POST);

            $this->handleSuccessResponse(
                ['categoryId' => $category->getCategoryId()],
                "Category with ID {$oldId} has been updated",
                VIEW_CATEGORIES_INDEX
            );
        } catch (Exception $e) {
            $this->handleErrorResponse($e, VIEW_CATEGORIES_INDEX);
        }
    }

    public function addToWarehouse(): void {
        $this->validatePostRequest();

        try {
            $categoryId = $_POST['category'] ?? '';
            $quantity = (int)($_POST['ctoadd'] ?? 0);

            $category = $this->categoryService->addToWarehouse($categoryId, $quantity);

            $this->handleSuccessResponse(
                ['newQuantity' => $category->getNumberOf()],
                'Stock added successfully',
                VIEW_CATEGORIES_INDEX
            );
        } catch (Exception $e) {
            $this->handleErrorResponse($e, VIEW_CATEGORIES_INDEX);
        }
    }

    public function getForSelect(): void {
        try {
            $options = $this->categoryService->getCategoriesForSelect();

            foreach ($options as $option) {
                echo "<option value='{$option['id']}'>{$option['label']}</option>";
            }
        } catch (Exception $e) {
            echo "<option value=''>Error loading categories</option>";
        }
    }
}
