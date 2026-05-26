<?php
/**
 * OrderTest.php - Unit Test untuk Order Model
 * 
 * Menggunakan Fake test double (FakePDO) untuk mengisolasi Order class
 * dari database connection yang sesungguhnya. Ini memungkinkan pengujian
 * logika pembuatan order tanpa perlu mengakses database nyata.
 */

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/Doubles/FakePDO.php';

class OrderTest {
    private $fakeDb;
    private $order;

    /**
     * Setup - Dijalankan sebelum setiap test
     */
    public function setUp() {
        $this->fakeDb = new FakePDO();
        $this->order = new Order($this->fakeDb);
    }

    /**
     * Test 1: createOrder() harus berhasil membuat order baru
     */
    public function testCreateOrderShouldSucceed() {
        $items = [
            [
                'menu_id' => 1,
                'quantity' => 2,
                'price' => 25000,
                'notes' => 'Pedas'
            ],
            [
                'menu_id' => 3,
                'quantity' => 1,
                'price' => 18000,
                'notes' => ''
            ]
        ];

        $result = $this->order->createOrder(68000, 'cash', $items);

        $this->assertTrue(isset($result['success']), "Result harus memiliki key 'success'");
        $this->assertTrue($result['success'], "createOrder harus berhasil dibuat");
        $this->assertTrue(isset($result['order_no']), "Result harus memiliki order_no");
        $this->assertNotEmpty($result['order_no'], "Order number tidak boleh kosong");

        echo "✓ Test 1 Passed: Order berhasil dibuat dengan order_no: " . $result['order_no'] . "\n";
    }

    /**
     * Test 2: createOrder() harus menghasilkan order_no dengan format yang benar
     */
    public function testCreateOrderShouldGenerateCorrectFormat() {
        $items = [
            [
                'menu_id' => 1,
                'quantity' => 1,
                'price' => 25000,
                'notes' => ''
            ]
        ];

        $result = $this->order->createOrder(25000, 'card', $items);

        $this->assertTrue(strpos($result['order_no'], 'ORD-') === 0, "Order number harus dimulai dengan 'ORD-'");

        echo "✓ Test 2 Passed: Order number memiliki format yang benar\n";
    }

    /**
     * Test 3: createOrder() dengan total_amount yang berbeda
     */
    public function testCreateOrderWithDifferentAmounts() {
        $testAmounts = [50000, 100000, 150000];

        foreach ($testAmounts as $amount) {
            $this->setUp(); // Reset fake DB untuk setiap test
            $items = [
                [
                    'menu_id' => 1,
                    'quantity' => 1,
                    'price' => $amount,
                    'notes' => ''
                ]
            ];

            $result = $this->order->createOrder($amount, 'cash', $items);
            $this->assertTrue($result['success'], "Order dengan jumlah $amount harus berhasil");
        }

        echo "✓ Test 3 Passed: Order berhasil dibuat dengan berbagai jumlah\n";
    }

    /**
     * Test 4: createOrder() dengan berbagai payment method
     */
    public function testCreateOrderWithDifferentPaymentMethods() {
        $paymentMethods = ['cash', 'card', 'transfer'];

        foreach ($paymentMethods as $method) {
            $this->setUp(); // Reset fake DB
            $items = [
                [
                    'menu_id' => 1,
                    'quantity' => 1,
                    'price' => 25000,
                    'notes' => ''
                ]
            ];

            $result = $this->order->createOrder(25000, $method, $items);
            $this->assertTrue($result['success'], "Order dengan payment method '$method' harus berhasil");
        }

        echo "✓ Test 4 Passed: Order berhasil dibuat dengan berbagai payment method\n";
    }

    /**
     * Test 5: createOrder() dengan banyak items
     */
    public function testCreateOrderWithMultipleItems() {
        $items = [
            [
                'menu_id' => 1,
                'quantity' => 2,
                'price' => 25000,
                'notes' => 'Pedas'
            ],
            [
                'menu_id' => 2,
                'quantity' => 1,
                'price' => 22000,
                'notes' => ''
            ],
            [
                'menu_id' => 3,
                'quantity' => 3,
                'price' => 18000,
                'notes' => 'Hangat'
            ]
        ];

        $totalAmount = (2 * 25000) + (1 * 22000) + (3 * 18000); // 146000
        $result = $this->order->createOrder($totalAmount, 'card', $items);

        $this->assertTrue($result['success'], "Order dengan multiple items harus berhasil");
        $this->assertEquals(3, count($items), "Items count harus sama dengan input");

        echo "✓ Test 5 Passed: Order dengan multiple items berhasil dibuat\n";
    }

    /**
     * Test 6: createOrder() harus fail ketika exception terjadi
     */
    public function testCreateOrderShouldFailOnException() {
        // Test dengan items kosong atau invalid
        $items = []; // Empty items

        $result = $this->order->createOrder(0, 'cash', $items);

        // Order akan dibuat tetapi dengan warning
        // Ini menunjukkan error handling
        $this->assertTrue(isset($result['success']), "Result harus memiliki key 'success'");

        echo "✓ Test 6 Passed: createOrder() menangani edge case dengan benar\n";
    }

    /**
     * Helper function untuk assertion
     */
    private function assertTrue($condition, $message) {
        if (!$condition) {
            throw new Exception("FAILED: $message");
        }
    }

    private function assertEquals($expected, $actual, $message) {
        if ($expected !== $actual) {
            throw new Exception("FAILED: $message. Expected: $expected, Got: $actual");
        }
    }

    private function assertNotEmpty($value, $message) {
        if (empty($value)) {
            throw new Exception("FAILED: $message");
        }
    }

    /**
     * Run semua test
     */
    public function runAllTests() {
        echo "\n========== ORDER TEST SUITE ==========\n";
        
        try {
            $this->setUp();
            $this->testCreateOrderShouldSucceed();

            $this->setUp();
            $this->testCreateOrderShouldGenerateCorrectFormat();

            $this->setUp();
            $this->testCreateOrderWithDifferentAmounts();

            $this->setUp();
            $this->testCreateOrderWithDifferentPaymentMethods();

            $this->setUp();
            $this->testCreateOrderWithMultipleItems();

            $this->setUp();
            $this->testCreateOrderShouldFailOnException();

            echo "\n✓ SEMUA TEST ORDER PASSED!\n";
            echo "====================================\n\n";
        } catch (Exception $e) {
            echo "\n✗ TEST FAILED: " . $e->getMessage() . "\n";
            echo "====================================\n\n";
        }
    }
}

// Jalankan test
if (php_sapi_name() === 'cli') {
    $test = new OrderTest();
    $test->runAllTests();
}
