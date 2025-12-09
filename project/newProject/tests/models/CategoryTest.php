<?php
namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use Category;

class CategoryTest extends TestCase {
    /* TEST: Category business logic methods */
    public function testCanFulfillOrderReturnsTrueWhenStockSufficient(): void {
        $categoryData = [
            'CategoryID' => 'AA',
            'Name' => 'Large Bottle',
            'Description' => '5L bottle',
            'Number_Of' => 50,
            'Price_Per' => 5.00
        ];

        $category = Category::fromDatabase($categoryData);

        $this->assertTrue($category->canFulfillOrder(10));
        $this->assertTrue($category->canFulfillOrder(50));
    }

    public function testCanFulfillOrderReturnsFalseWhenStockInsufficient(): void {
        $categoryData = [
            'CategoryID' => 'AA',
            'Name' => 'Large Bottle',
            'Description' => '5L bottle',
            'Number_Of' => 5,
            'Price_Per' => 5.00
        ];

        $category = Category::fromDatabase($categoryData);

        $this->assertFalse($category->canFulfillOrder(10));
        $this->assertFalse($category->canFulfillOrder(100));
    }

    public function testIsInStockReturnsTrueWhenStockAvailable(): void {
        $categoryData = [
            'CategoryID' => 'AA',
            'Name' => 'Large Bottle',
            'Description' => '5L bottle',
            'Number_Of' => 1,
            'Price_Per' => 5.00
        ];

        $category = Category::fromDatabase($categoryData);

        $this->assertTrue($category->isInStock());
    }

    public function testIsInStockReturnsFalseWhenNoStock(): void {
        $categoryData = [
            'CategoryID' => 'AA',
            'Name' => 'Large Bottle',
            'Description' => '5L bottle',
            'Number_Of' => 0,
            'Price_Per' => 5.00
        ];

        $category = Category::fromDatabase($categoryData);

        $this->assertFalse($category->isInStock());
    }

    public function testGetTotalValueCalculatesCorrectly(): void {
        $categoryData = [
            'CategoryID' => 'AA',
            'Name' => 'Large Bottle',
            'Description' => '5L bottle',
            'Number_Of' => 10,
            'Price_Per' => 5.50
        ];

        $category = Category::fromDatabase($categoryData);

        $this->assertEquals(55.0, $category->getTotalValue());
    }

    public function testGetTotalValueReturnsZeroWhenNoStock(): void {
        $categoryData = [
            'CategoryID' => 'AA',
            'Name' => 'Large Bottle',
            'Description' => '5L bottle',
            'Number_Of' => 0,
            'Price_Per' => 5.00
        ];

        $category = Category::fromDatabase($categoryData);

        $this->assertEquals(0.0, $category->getTotalValue());
    }

    /* Test factory methods */
    public function testFromDatabaseCreatesValidCategory(): void {
        $data = [
            'CategoryID' => 'AB',
            'Name' => 'Medium Bottle',
            'Description' => '3L bottle',
            'Number_Of' => 75,
            'Price_Per' => 3.75
        ];

        $category = Category::fromDatabase($data);

        $this->assertEquals('AB', $category->getCategoryId());
        $this->assertEquals('Medium Bottle', $category->getName());
        $this->assertEquals('3L bottle', $category->getDescription());
        $this->assertEquals(75, $category->getNumberOf());
        $this->assertEquals(3.75, $category->getPricePer());
    }

    public function testFromFormDataCreatesValidCategory(): void {
        $formData = [
            'cid' => 'cd',
            'cname' => 'small bottle',
            'cdescription' => 'Very small bottle',
            'cnumberof' => '25',
            'cpriceper' => '1.99'
        ];

        $category = Category::fromFormData($formData);

        $this->assertEquals('CD', $category->getCategoryId());
        $this->assertEquals('Small Bottle', $category->getName());
        $this->assertEquals('Very small bottle', $category->getDescription());
        $this->assertEquals(25, $category->getNumberOf());
        $this->assertEquals(1.99, $category->getPricePer());
    }

    /* Test getters */
    public function testGettersReturnCorrectValues(): void {
        $data = [
            'CategoryID' => 'XY',
            'Name' => 'Test Category',
            'Description' => 'Test Description',
            'Number_Of' => 42,
            'Price_Per' => 9.99
        ];

        $category = Category::fromDatabase($data);

        $this->assertIsString($category->getCategoryId());
        $this->assertIsString($category->getName());
        $this->assertIsString($category->getDescription());
        $this->assertIsInt($category->getNumberOf());
        $this->assertIsFloat($category->getPricePer());
    }
}
