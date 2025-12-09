<?php
require_once __DIR__ . '/../config/bootstrap.php';

$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/categories', 'CategoryController@index');
$router->post('/categories', 'CategoryController@store');
$router->post('/categories/warehouse', 'CategoryController@addToWarehouse');
$router->post('/categories/update', 'CategoryController@update');

$router->get('/customers', 'CustomerController@index');
$router->post('/customers', 'CustomerController@store');
$router->post('/customers/update', 'CustomerController@update');

$router->get('/sales', 'SaleController@index');
$router->post('/sales', 'SaleController@store');
$router->post('/sales/cancel', 'SaleController@cancel');

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (NotFoundException $e) {
    http_response_code(404);
    // Debug: show what went wrong
    if (isset($_GET['debug'])) {
        echo "<pre>";
        echo "NotFoundException: " . $e->getMessage() . "\n";
        echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
        echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
        echo "</pre>";
    }
    include '../views/errors/404.php';
} catch (\Exception $e) {
    http_response_code(500);
    // Debug: show what went wrong
    if (isset($_GET['debug'])) {
        echo "<pre>";
        echo "Exception: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . "\n";
        echo "Line: " . $e->getLine() . "\n";
        echo "Trace:\n" . $e->getTraceAsString() . "\n";
        echo "</pre>";
    }
    include '../views/errors/500.php';
    error_log($e->getMessage());
}
