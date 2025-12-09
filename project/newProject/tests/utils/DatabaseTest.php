<?php
namespace Tests\Config;

use PHPUnit\Framework\TestCase;
use Database;

class DatabaseTest extends TestCase {
    /* TEST: Database Singleton pattern */
    public function testGetInstanceReturnsSameInstance(): void {
        $instance1 = Database::getInstance();
        $instance2 = Database::getInstance();

        $this->assertSame($instance1, $instance2);
    }

    public function testGetInstanceReturnsValidDatabaseObject(): void {
        $instance = Database::getInstance();

        $this->assertInstanceOf(Database::class, $instance);
    }

    public function testGetConnectionReturnsPDOInstance(): void {
        $db = Database::getInstance();
        $connection = $db->getConnection();

        $this->assertInstanceOf(\PDO::class, $connection);
    }

    public function testConnectionHasCorrectAttributes(): void {
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        $errorMode = $pdo->getAttribute(\PDO::ATTR_ERRMODE);
        $this->assertEquals(\PDO::ERRMODE_EXCEPTION, $errorMode);

        $fetchMode = $pdo->getAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE);
        $this->assertEquals(\PDO::FETCH_ASSOC, $fetchMode);
    }

    public function testConnectionIsReusable(): void {
        $db1 = Database::getInstance();
        $conn1 = $db1->getConnection();

        $db2 = Database::getInstance();
        $conn2 = $db2->getConnection();

        $this->assertSame($conn1, $conn2);
    }

    public function testMultipleGetConnectionCallsReturnSameConnection(): void {
        $db = Database::getInstance();

        $conn1 = $db->getConnection();
        $conn2 = $db->getConnection();
        $conn3 = $db->getConnection();

        $this->assertSame($conn1, $conn2);
        $this->assertSame($conn2, $conn3);
    }

    /* Verify connection works */
    public function testCanExecuteSimpleQuery(): void {
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        try {
            $result = $pdo->query("SELECT 1 as test");
            $row = $result->fetch();

            $this->assertEquals(1, $row['test']);
        } catch (\PDOException $e) {
            $this->markTestSkipped('Database connection not available: ' . $e->getMessage());
        }
    }

}
