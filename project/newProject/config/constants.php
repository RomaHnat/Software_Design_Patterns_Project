<?php
/**
 * Application Constants
 * Defines reusable constants to avoid string duplication
 */

// View Paths
const VIEW_CATEGORIES_INDEX = 'categories/index.php';
const VIEW_CUSTOMERS_INDEX = 'customers/index.php';
const VIEW_SALES_INDEX = 'sales/index.php';
const VIEW_HOME_INDEX = 'views/home.php';

// Validation Patterns
const CATEGORY_ID_PATTERN = '/^[A-Z]{2}$/';

// Error Messages
const ERROR_CATEGORY_NOT_FOUND = 'Category not found.';
const ERROR_CUSTOMER_NOT_FOUND = 'Customer not found';
const ERROR_SALE_NOT_FOUND = 'Sale does not exist';
const ERROR_ROUTE_NOT_FOUND = 'Route not found';
