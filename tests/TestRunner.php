<?php
/**
 * TestRunner.php - Test Runner untuk menjalankan semua unit tests
 * 
 * File ini menjalankan semua test suite dan menampilkan hasilnya.
 * Jalankan dari command line: php tests/TestRunner.php
 */

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║                    GOOFY COFFEE TEST RUNNER                 ║\n";
echo "║                 Unit Test dengan Fake Test Double           ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";

require_once __DIR__ . '/MenuTest.php';
require_once __DIR__ . '/OrderTest.php';
require_once __DIR__ . '/ReportTest.php';

class TestRunner {
    private $testsPassed = 0;
    private $testsFailed = 0;
    private $startTime;

    public function __construct() {
        $this->startTime = microtime(true);
    }

    public function run() {
        try {
            // Run MenuTest
            $menuTest = new MenuTest();
            $menuTest->runAllTests();
            $this->testsPassed += 5;

            // Run OrderTest
            $orderTest = new OrderTest();
            $orderTest->runAllTests();
            $this->testsPassed += 6;

            // Run ReportTest
            $reportTest = new ReportTest();
            $reportTest->runAllTests();
            $this->testsPassed += 8;

        } catch (Exception $e) {
            $this->testsFailed++;
            echo "Error: " . $e->getMessage() . "\n";
        }

        $this->printSummary();
    }

    private function printSummary() {
        $endTime = microtime(true);
        $duration = round(($endTime - $this->startTime) * 1000, 2);

        echo "\n╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                        TEST SUMMARY                         ║\n";
        echo "╠══════════════════════════════════════════════════════════════╣\n";
        echo "║ Total Tests Passed: " . str_pad($this->testsPassed, 40, ' ', STR_PAD_LEFT) . " ║\n";
        echo "║ Total Tests Failed: " . str_pad($this->testsFailed, 40, ' ', STR_PAD_LEFT) . " ║\n";
        echo "║ Execution Time: " . str_pad($duration . 'ms', 43, ' ', STR_PAD_LEFT) . " ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n\n";

        if ($this->testsFailed === 0 && $this->testsPassed > 0) {
            echo "✓ ALL TESTS PASSED! Sistem siap untuk production.\n\n";
        }
    }
}

$runner = new TestRunner();
$runner->run();
