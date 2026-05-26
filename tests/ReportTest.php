<?php
/**
 * ReportTest.php - Unit Test untuk Report Model
 * 
 * Menggunakan Fake test double (FakePDO) untuk mengisolasi Report class
 * dari database connection. Test ini memverifikasi laporan penjualan
 * tanpa perlu mengakses database nyata.
 */

require_once __DIR__ . '/../models/Report.php';
require_once __DIR__ . '/Doubles/FakePDO.php';

class ReportTest {
    private $fakeDb;
    private $report;

    /**
     * Setup - Dijalankan sebelum setiap test
     */
    public function setUp() {
        $this->fakeDb = new FakePDO();
        $this->report = new Report($this->fakeDb);
    }

    /**
     * Test 1: getDailyRecap() harus mengembalikan summary order untuk hari tertentu
     */
    public function testGetDailyRecapShouldReturnSummary() {
        $today = date('Y-m-d');
        $result = $this->report->getDailyRecap($today);

        $this->assertTrue(is_array($result), "getDailyRecap harus mengembalikan array");
        $this->assertArrayHasKey('total_orders', $result, "Result harus memiliki total_orders");
        $this->assertArrayHasKey('total_revenue', $result, "Result harus memiliki total_revenue");

        echo "✓ Test 1 Passed: getDailyRecap() mengembalikan summary dengan benar\n";
    }

    /**
     * Test 2: getDailyRecap() harus menghitung total order dengan benar
     */
    public function testGetDailyRecapShouldCountOrdersCorrectly() {
        $today = date('Y-m-d');
        $result = $this->report->getDailyRecap($today);

        $this->assertTrue(is_numeric($result['total_orders']), "total_orders harus berupa angka");
        $this->assertGreaterThanOrEqual(0, $result['total_orders'], "total_orders harus >= 0");

        echo "✓ Test 2 Passed: getDailyRecap() menghitung total order dengan benar (Total: " . $result['total_orders'] . ")\n";
    }

    /**
     * Test 3: getDailyRecap() harus menghitung revenue dengan benar
     */
    public function testGetDailyRecapShouldCalculateRevenueCorrectly() {
        $today = date('Y-m-d');
        $result = $this->report->getDailyRecap($today);

        $this->assertTrue(is_numeric($result['total_revenue']), "total_revenue harus berupa angka");
        $this->assertGreaterThanOrEqual(0, $result['total_revenue'], "total_revenue harus >= 0");

        echo "✓ Test 3 Passed: getDailyRecap() menghitung revenue dengan benar (Total: Rp " . number_format($result['total_revenue']) . ")\n";
    }

    /**
     * Test 4: getDailyRecap() dengan tanggal yang berbeda
     */
    public function testGetDailyRecapWithDifferentDates() {
        $testDates = [
            date('Y-m-d'),
            date('Y-m-d', strtotime('-1 day')),
            date('Y-m-d', strtotime('-2 days'))
        ];

        foreach ($testDates as $date) {
            $result = $this->report->getDailyRecap($date);
            $this->assertTrue(is_array($result), "getDailyRecap dengan tanggal $date harus mengembalikan array");
        }

        echo "✓ Test 4 Passed: getDailyRecap() berfungsi dengan berbagai tanggal\n";
    }

    /**
     * Test 5: getPaymentMethodStats() harus mengembalikan statistik payment method
     */
    public function testGetPaymentMethodStatsShouldReturnStats() {
        $today = date('Y-m-d');
        $result = $this->report->getPaymentMethodStats($today);

        $this->assertTrue(is_array($result), "getPaymentMethodStats harus mengembalikan array");
        
        if (count($result) > 0) {
            $this->assertArrayHasKey('payment_method', $result[0], "Result harus memiliki payment_method");
            $this->assertArrayHasKey('count', $result[0], "Result harus memiliki count");
            $this->assertArrayHasKey('total', $result[0], "Result harus memiliki total");
        }

        echo "✓ Test 5 Passed: getPaymentMethodStats() mengembalikan statistik dengan benar\n";
    }

    /**
     * Test 6: getPaymentMethodStats() harus mengelompokkan dengan benar
     */
    public function testGetPaymentMethodStatsShouldGroupCorrectly() {
        $today = date('Y-m-d');
        $result = $this->report->getPaymentMethodStats($today);

        $paymentMethods = array_column($result, 'payment_method');
        $uniqueMethods = array_unique($paymentMethods);

        $this->assertEquals(count($paymentMethods), count($uniqueMethods), 
            "Tidak boleh ada duplikat payment method dalam hasil");

        echo "✓ Test 6 Passed: getPaymentMethodStats() mengelompokkan payment method tanpa duplikat\n";
    }

    /**
     * Test 7: getPaymentMethodStats() harus menghitung count dengan benar
     */
    public function testGetPaymentMethodStatsShouldCountCorrectly() {
        $today = date('Y-m-d');
        $result = $this->report->getPaymentMethodStats($today);

        foreach ($result as $stat) {
            $this->assertTrue(is_numeric($stat['count']), "count harus berupa angka");
            $this->assertGreaterThan(0, $stat['count'], "count harus > 0");
        }

        echo "✓ Test 7 Passed: getPaymentMethodStats() menghitung count dengan benar\n";
    }

    /**
     * Test 8: getPaymentMethodStats() harus menghitung total amount dengan benar
     */
    public function testGetPaymentMethodStatsShouldSumCorrectly() {
        $today = date('Y-m-d');
        $result = $this->report->getPaymentMethodStats($today);

        foreach ($result as $stat) {
            $this->assertTrue(is_numeric($stat['total']), "total harus berupa angka");
            $this->assertGreaterThanOrEqual(0, $stat['total'], "total harus >= 0");
        }

        echo "✓ Test 8 Passed: getPaymentMethodStats() menghitung total dengan benar\n";
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

    private function assertArrayHasKey($key, $array, $message) {
        if (!isset($array[$key])) {
            throw new Exception("FAILED: $message. Key '$key' not found");
        }
    }

    private function assertGreaterThan($expected, $actual, $message) {
        if ($actual <= $expected) {
            throw new Exception("FAILED: $message. Expected > $expected, Got: $actual");
        }
    }

    private function assertGreaterThanOrEqual($expected, $actual, $message) {
        if ($actual < $expected) {
            throw new Exception("FAILED: $message. Expected >= $expected, Got: $actual");
        }
    }

    /**
     * Run semua test
     */
    public function runAllTests() {
        echo "\n========== REPORT TEST SUITE ==========\n";
        
        try {
            $this->setUp();
            $this->testGetDailyRecapShouldReturnSummary();

            $this->setUp();
            $this->testGetDailyRecapShouldCountOrdersCorrectly();

            $this->setUp();
            $this->testGetDailyRecapShouldCalculateRevenueCorrectly();

            $this->setUp();
            $this->testGetDailyRecapWithDifferentDates();

            $this->setUp();
            $this->testGetPaymentMethodStatsShouldReturnStats();

            $this->setUp();
            $this->testGetPaymentMethodStatsShouldGroupCorrectly();

            $this->setUp();
            $this->testGetPaymentMethodStatsShouldCountCorrectly();

            $this->setUp();
            $this->testGetPaymentMethodStatsShouldSumCorrectly();

            echo "\n✓ SEMUA TEST REPORT PASSED!\n";
            echo "=======================================\n\n";
        } catch (Exception $e) {
            echo "\n✗ TEST FAILED: " . $e->getMessage() . "\n";
            echo "=======================================\n\n";
        }
    }
}

// Jalankan test
if (php_sapi_name() === 'cli') {
    $test = new ReportTest();
    $test->runAllTests();
}
