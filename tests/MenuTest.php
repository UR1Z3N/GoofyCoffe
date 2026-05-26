<?php
/**
 * MenuTest.php - Unit Test untuk Menu Model
 * 
 * Menggunakan Fake test double (FakePDO) untuk mengisolasi Menu class
 * dari database connection yang sesungguhnya.
 */

require_once __DIR__ . '/../models/Menu.php';
require_once __DIR__ . '/Doubles/FakePDO.php';

class MenuTest {
    private $fakeDb;
    private $menu;

    /**
     * Setup - Dijalankan sebelum setiap test
     */
    public function setUp() {
        $this->fakeDb = new FakePDO();
        $this->menu = new Menu($this->fakeDb);
    }

    /**
     * Test 1: getAllMenus() harus mengembalikan semua menu
     */
    public function testGetAllMenusShouldReturnAllMenus() {
        $result = $this->menu->getAllMenus();
        $menus = $result->fetchAll(PDO::FETCH_ASSOC);

        // Assertions
        $this->assertTrue(count($menus) > 0, "getAllMenus harus mengembalikan minimal 1 menu");
        $this->assertEquals(3, count($menus), "getAllMenus harus mengembalikan 3 menu");
        $this->assertArrayHasKey('id', $menus[0], "Menu harus memiliki id");
        $this->assertArrayHasKey('name', $menus[0], "Menu harus memiliki name");
        $this->assertArrayHasKey('price', $menus[0], "Menu harus memiliki price");
        $this->assertArrayHasKey('category_name', $menus[0], "Menu harus memiliki category_name");

        echo "✓ Test 1 Passed: getAllMenus() mengembalikan semua menu dengan benar\n";
    }

    /**
     * Test 2: getAllMenus() harus mengembalikan menu dengan kategori
     */
    public function testGetAllMenusShouldIncludeCategory() {
        $result = $this->menu->getAllMenus();
        $menus = $result->fetchAll(PDO::FETCH_ASSOC);

        $hasMakanan = false;
        $hasMinuman = false;

        foreach ($menus as $menu) {
            if ($menu['category_name'] === 'Makanan') {
                $hasMakanan = true;
            }
            if ($menu['category_name'] === 'Minuman') {
                $hasMinuman = true;
            }
        }

        $this->assertTrue($hasMakanan, "Menu harus memiliki kategori Makanan");
        $this->assertTrue($hasMinuman, "Menu harus memiliki kategori Minuman");

        echo "✓ Test 2 Passed: getAllMenus() mengembalikan menu dengan kategori yang benar\n";
    }

    /**
     * Test 3: getAllMenus() harus mengembalikan menu dengan harga yang valid
     */
    public function testGetAllMenusShouldHaveValidPrice() {
        $result = $this->menu->getAllMenus();
        $menus = $result->fetchAll(PDO::FETCH_ASSOC);

        foreach ($menus as $menu) {
            $this->assertGreaterThan(0, $menu['price'], "Harga menu harus lebih besar dari 0");
            $this->assertTrue(is_numeric($menu['price']), "Harga menu harus berupa angka");
        }

        echo "✓ Test 3 Passed: Semua menu memiliki harga yang valid (> 0)\n";
    }

    /**
     * Test 4: getCategories() harus mengembalikan semua kategori
     */
    public function testGetCategoriesShouldReturnAllCategories() {
        $result = $this->menu->getCategories();
        $categories = $result->fetchAll(PDO::FETCH_ASSOC);

        $this->assertTrue(count($categories) > 0, "getCategories harus mengembalikan minimal 1 kategori");
        $this->assertEquals(2, count($categories), "getCategories harus mengembalikan 2 kategori");

        echo "✓ Test 4 Passed: getCategories() mengembalikan semua kategori dengan benar\n";
    }

    /**
     * Test 5: getCategories() harus mengembalikan kategori dengan struktur yang benar
     */
    public function testGetCategoriesShouldHaveCorrectStructure() {
        $result = $this->menu->getCategories();
        $categories = $result->fetchAll(PDO::FETCH_ASSOC);

        foreach ($categories as $category) {
            $this->assertArrayHasKey('id', $category, "Kategori harus memiliki id");
            $this->assertArrayHasKey('name', $category, "Kategori harus memiliki name");
            $this->assertArrayHasKey('type', $category, "Kategori harus memiliki type");
        }

        echo "✓ Test 5 Passed: Semua kategori memiliki struktur yang benar\n";
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

    private function assertGreaterThan($expected, $actual, $message) {
        if ($actual <= $expected) {
            throw new Exception("FAILED: $message. Expected > $expected, Got: $actual");
        }
    }

    private function assertArrayHasKey($key, $array, $message) {
        if (!isset($array[$key])) {
            throw new Exception("FAILED: $message. Key '$key' not found");
        }
    }

    /**
     * Run semua test
     */
    public function runAllTests() {
        echo "\n========== MENU TEST SUITE ==========\n";
        
        try {
            $this->setUp();
            $this->testGetAllMenusShouldReturnAllMenus();

            $this->setUp();
            $this->testGetAllMenusShouldIncludeCategory();

            $this->setUp();
            $this->testGetAllMenusShouldHaveValidPrice();

            $this->setUp();
            $this->testGetCategoriesShouldReturnAllCategories();

            $this->setUp();
            $this->testGetCategoriesShouldHaveCorrectStructure();

            echo "\n✓ SEMUA TEST MENU PASSED!\n";
            echo "====================================\n\n";
        } catch (Exception $e) {
            echo "\n✗ TEST FAILED: " . $e->getMessage() . "\n";
            echo "====================================\n\n";
        }
    }
}

// Jalankan test
if (php_sapi_name() === 'cli') {
    $test = new MenuTest();
    $test->runAllTests();
}
