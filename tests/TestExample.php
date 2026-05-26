<?php
/**
 * TestExample.php - Contoh Implementasi Test dengan Fake Test Double
 * 
 * File ini menunjukkan berbagai teknik dan pattern untuk membuat unit tests
 * menggunakan Fake test double. Dapat digunakan sebagai referensi.
 * 
 * CATATAN: File ini untuk pembelajaran/referensi dan tidak dijalankan 
 * oleh TestRunner, tapi bisa dijalankan secara terpisah dengan:
 * php tests/TestExample.php
 */

require_once __DIR__ . '/Doubles/FakePDO.php';

class TestExample {
    
    /**
     * === PATTERN 1: Setup dan Teardown ===
     */
    private $fakeDb;
    
    public function setUp() {
        // Dijalankan SEBELUM setiap test
        echo "   → Setup: Inisialisasi Fake Database\n";
        $this->fakeDb = new FakePDO();
    }
    
    public function tearDown() {
        // Dijalankan SETELAH setiap test
        echo "   → Teardown: Cleanup resources\n";
        $this->fakeDb = null;
    }
    
    /**
     * === PATTERN 2: Simple Assertion ===
     */
    public function exampleSimpleAssertion() {
        echo "\n[PATTERN 2] Simple Assertion:\n";
        $this->setUp();
        
        $value = 10;
        $this->assertTrue($value > 0, "Value should be positive");
        $this->assertEquals($value, 10, "Value should be 10");
        
        echo "   ✓ Simple assertion test passed\n";
        $this->tearDown();
    }
    
    /**
     * === PATTERN 3: Array Assertion ===
     */
    public function exampleArrayAssertion() {
        echo "\n[PATTERN 3] Array Assertion:\n";
        $this->setUp();
        
        $data = ['id' => 1, 'name' => 'Test', 'price' => 50000];
        
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('price', $data);
        $this->assertTrue(is_array($data), "Data should be array");
        
        echo "   ✓ Array assertion test passed\n";
        $this->tearDown();
    }
    
    /**
     * === PATTERN 4: String Assertion ===
     */
    public function exampleStringAssertion() {
        echo "\n[PATTERN 4] String Assertion:\n";
        $this->setUp();
        
        $orderNo = "ORD-20260101120000-123";
        
        $this->assertTrue(strpos($orderNo, 'ORD-') === 0, "Should start with ORD-");
        $this->assertNotEmpty($orderNo, "Order number should not be empty");
        $this->assertTrue(strlen($orderNo) > 10, "Order number should be long enough");
        
        echo "   ✓ String assertion test passed\n";
        $this->tearDown();
    }
    
    /**
     * === PATTERN 5: Numeric Assertion ===
     */
    public function exampleNumericAssertion() {
        echo "\n[PATTERN 5] Numeric Assertion:\n";
        $this->setUp();
        
        $price = 25000;
        $quantity = 2;
        
        $this->assertTrue(is_numeric($price), "Price should be numeric");
        $this->assertGreaterThan(0, $price, "Price should > 0");
        $this->assertGreaterThanOrEqual(1, $quantity, "Quantity should >= 1");
        
        $total = $price * $quantity;
        $this->assertEquals(50000, $total, "Total should be 50000");
        
        echo "   ✓ Numeric assertion test passed\n";
        $this->tearDown();
    }
    
    /**
     * === PATTERN 6: Loop Testing (Multiple Scenarios) ===
     */
    public function exampleLoopTesting() {
        echo "\n[PATTERN 6] Loop Testing:\n";
        $this->setUp();
        
        $testValues = [10, 20, 30, 40, 50];
        
        foreach ($testValues as $value) {
            $this->assertTrue($value > 0, "Value $value should be positive");
        }
        
        echo "   ✓ Tested " . count($testValues) . " scenarios successfully\n";
        $this->tearDown();
    }
    
    /**
     * === PATTERN 7: Exception Testing ===
     */
    public function exampleExceptionTesting() {
        echo "\n[PATTERN 7] Exception Testing:\n";
        $this->setUp();
        
        try {
            // Simulasi error condition
            $data = null;
            if ($data === null) {
                throw new Exception("Data is required");
            }
        } catch (Exception $e) {
            $this->assertTrue(
                strpos($e->getMessage(), 'required') !== false,
                "Exception message should contain 'required'"
            );
            echo "   ✓ Exception caught and validated correctly\n";
        }
        
        $this->tearDown();
    }
    
    /**
     * === PATTERN 8: Data Grouping ===
     */
    public function exampleDataGrouping() {
        echo "\n[PATTERN 8] Data Grouping:\n";
        $this->setUp();
        
        $items = [
            ['id' => 1, 'name' => 'Mie Goreng', 'price' => 25000],
            ['id' => 2, 'name' => 'Nasi Goreng', 'price' => 22000],
            ['id' => 3, 'name' => 'Kopi', 'price' => 18000],
        ];
        
        $totalPrice = 0;
        foreach ($items as $item) {
            $this->assertGreaterThan(0, $item['price'], "Price should be greater than 0");
            $totalPrice += $item['price'];
        }
        
        $this->assertEquals(65000, $totalPrice, "Total should be 65000");
        echo "   ✓ Data grouping test passed\n";
        $this->tearDown();
    }
    
    /**
     * === PATTERN 9: State Verification ===
     */
    public function exampleStateVerification() {
        echo "\n[PATTERN 9] State Verification:\n";
        $this->setUp();
        
        // Simulasi state perubahan
        $order = [
            'status' => 'Pending',
            'items_count' => 0
        ];
        
        $this->assertEquals('Pending', $order['status'], "Status should be Pending");
        
        // Simulasi item ditambah
        $order['items_count'] = 3;
        $this->assertEquals(3, $order['items_count'], "Items count should be 3");
        
        // Simulasi status berubah
        $order['status'] = 'Completed';
        $this->assertEquals('Completed', $order['status'], "Status should be Completed");
        
        echo "   ✓ State verification test passed\n";
        $this->tearDown();
    }
    
    /**
     * === PATTERN 10: Database Fake Testing ===
     */
    public function exampleFakeDatabaseTesting() {
        echo "\n[PATTERN 10] Database Fake Testing:\n";
        $this->setUp();
        
        // Menggunakan FakePDO - tidak perlu database nyata
        $menus = $this->fakeDb->getTestData('menus');
        
        $this->assertTrue(count($menus) > 0, "Should have menus");
        
        // Tambah data baru ke fake database
        $this->fakeDb->addTestData('menus', [
            'id' => 10,
            'name' => 'Menu Baru',
            'price' => 35000,
            'category_name' => 'Makanan'
        ]);
        
        $menusAfterAdd = $this->fakeDb->getTestData('menus');
        $this->assertTrue(
            count($menusAfterAdd) > count($menus),
            "Menu count should increase after adding"
        );
        
        echo "   ✓ Database fake testing passed\n";
        $this->tearDown();
    }
    
    /**
     * =============================================
     * HELPER ASSERTION METHODS (dari MenuTest.php)
     * =============================================
     */
    
    private function assertTrue($condition, $message) {
        if (!$condition) {
            throw new Exception("ASSERT TRUE FAILED: $message");
        }
    }
    
    private function assertEquals($expected, $actual, $message) {
        if ($expected !== $actual) {
            throw new Exception(
                "ASSERT EQUAL FAILED: $message\n" .
                "Expected: $expected\n" .
                "Got: $actual"
            );
        }
    }
    
    private function assertGreaterThan($expected, $actual, $message) {
        if ($actual <= $expected) {
            throw new Exception("ASSERT GREATER THAN FAILED: $message. Got: $actual");
        }
    }
    
    private function assertGreaterThanOrEqual($expected, $actual, $message) {
        if ($actual < $expected) {
            throw new Exception("ASSERT >= FAILED: $message. Got: $actual");
        }
    }
    
    private function assertArrayHasKey($key, $array, $message = null) {
        if (!isset($array[$key])) {
            throw new Exception("ARRAY KEY NOT FOUND: " . ($message ?? $key));
        }
    }
    
    private function assertNotEmpty($value, $message) {
        if (empty($value)) {
            throw new Exception("ASSERT NOT EMPTY FAILED: $message");
        }
    }
    
    /**
     * Run semua contoh
     */
    public function runAllExamples() {
        echo "\n╔════════════════════════════════════════════════════════╗\n";
        echo "║         UNIT TEST PATTERNS & EXAMPLES                ║\n";
        echo "║     dengan Fake Test Double (FakePDO)               ║\n";
        echo "╚════════════════════════════════════════════════════════╝\n";
        
        try {
            $this->exampleSimpleAssertion();
            $this->exampleArrayAssertion();
            $this->exampleStringAssertion();
            $this->exampleNumericAssertion();
            $this->exampleLoopTesting();
            $this->exampleExceptionTesting();
            $this->exampleDataGrouping();
            $this->exampleStateVerification();
            $this->exampleFakeDatabaseTesting();
            
            echo "\n╔════════════════════════════════════════════════════════╗\n";
            echo "║        ✓ SEMUA CONTOH BERHASIL DIJALANKAN!           ║\n";
            echo "╚════════════════════════════════════════════════════════╝\n\n";
            
        } catch (Exception $e) {
            echo "\n✗ ERROR: " . $e->getMessage() . "\n\n";
        }
    }
}

// Jalankan contoh
if (php_sapi_name() === 'cli') {
    $example = new TestExample();
    $example->runAllExamples();
}
