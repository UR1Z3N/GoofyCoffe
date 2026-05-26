<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../models/Order.php';

class OrderTest extends TestCase
{
    public function testOrderCreationWithDummyDB()
    {
        /**
         * Test Double: Dummy
         * 
         * $dummyDb is a dummy object. It is passed to the Order constructor 
         * to satisfy the parameter list, but it is never actually used or called 
         * in this specific test case.
         */
        $dummyDb = $this->createMock(PDO::class);

        $order = new Order($dummyDb);

        // Assert that the Order object is successfully created
        $this->assertInstanceOf(Order::class, $order);
    }
}
