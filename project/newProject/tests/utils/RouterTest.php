<?php
namespace Tests\Utils;

use PHPUnit\Framework\TestCase;
use Router;
use NotFoundException;

class RouterTest extends TestCase {
    private Router $router;

    protected function setUp(): void {
        $this->router = new Router();
    }

    /* TEST: Route registration and exception handling */
    public function testDispatchThrowsNotFoundExceptionForUnmatchedRoute(): void {
        $this->router->get('categories', 'CategoryController@index');

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Route not found: GET nonexistent');

        $this->router->dispatch('GET', '/nonexistent');
    }

    public function testDispatchThrowsNotFoundExceptionForWrongMethod(): void {
        $this->router->get('categories', 'CategoryController@index');

        $this->expectException(NotFoundException::class);

        $this->router->dispatch('POST', '/categories');
    }
}
