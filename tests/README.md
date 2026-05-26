# Unit Test Documentation - GoofyCoffe Project

## 📋 Daftar Isi
1. [Pengenalan](#pengenalan)
2. [Struktur Test](#struktur-test)
3. [Fake Test Double Pattern](#fake-test-double-pattern)
4. [Menjalankan Tests](#menjalankan-tests)
5. [Penjelasan Setiap Test](#penjelasan-setiap-test)
6. [Tips dan Trik](#tips-dan-trik)

---

## 🎯 Pengenalan

Dokumentasi ini menjelaskan implementasi **Unit Testing** dengan **Fake Test Double** untuk project GoofyCoffe. Semua source code model (Menu, Order, Report) telah dibuatkan unit tests yang komprehensif.

### Tujuan Testing:
- ✓ Mengisolasi kelas/method dari dependency eksternal (database)
- ✓ Membuat test lebih cepat dan stabil
- ✓ Mencegah perubahan logika yang tidak diinginkan
- ✓ Dokumentasi kode yang hidup

---

## 📂 Struktur Test

```
tests/
├── Doubles/
│   └── FakePDO.php              # Fake test double untuk PDO
├── MenuTest.php                 # Unit test untuk Menu model
├── OrderTest.php                # Unit test untuk Order model
├── ReportTest.php               # Unit test untuk Report model
├── TestRunner.php               # Test runner untuk menjalankan semua test
└── README.md                    # Dokumentasi ini
```

---

## 🔍 Fake Test Double Pattern

### Apa itu Fake Test Double?

**Fake** adalah test double yang:
- **Mensimulasikan** fungsionalitas kelas asli
- **Menghindari** operasi kompleks/mahal (I/O database, file system)
- **Mempercepat** eksekusi test
- **Meningkatkan** stabilitas test

### Contoh dalam Project:

```php
// Real Database Connection (SLOW)
$db = new PDO("mysql:host=localhost;dbname=goofycafe_db", "root", "");
$menu = new Menu($db);
$menus = $menu->getAllMenus(); // Terhubung ke database real

// Dengan Fake Test Double (FAST)
$fakeDb = new FakePDO();  // Tidak perlu koneksi database
$menu = new Menu($fakeDb);
$menus = $menu->getAllMenus(); // Menggunakan data fake yang sudah didefinisikan
```

### Keuntungan Menggunakan Fake:
- ⚡ Test berjalan **1000x lebih cepat**
- 🔒 Tidak memerlukan **database nyata**
- 🧪 **Isolasi sempurna** dari dependency eksternal
- 📊 Dapat **mereproduksi** kondisi apapun
- 💪 Lebih stabil dan tidak terpengaruh **koneksi network**

---

## 🚀 Menjalankan Tests

### Menggunakan Command Line (PHP CLI)

```bash
# Jalankan semua tests
php tests/TestRunner.php

# Jalankan test tertentu
php tests/MenuTest.php
php tests/OrderTest.php
php tests/ReportTest.php
```

### Output Contoh:
```
╔══════════════════════════════════════════════════════════════╗
║                    GOOFY COFFEE TEST RUNNER                 ║
║                 Unit Test dengan Fake Test Double           ║
╚══════════════════════════════════════════════════════════════╝

========== MENU TEST SUITE ==========
✓ Test 1 Passed: getAllMenus() mengembalikan semua menu dengan benar
✓ Test 2 Passed: getAllMenus() mengembalikan menu dengan kategori yang benar
✓ Test 3 Passed: Semua menu memiliki harga yang valid (> 0)
✓ Test 4 Passed: getCategories() mengembalikan semua kategori dengan benar
✓ Test 5 Passed: Semua kategori memiliki struktur yang benar

✓ SEMUA TEST MENU PASSED!
====================================

[... tests lainnya ...]

╔══════════════════════════════════════════════════════════════╗
║                        TEST SUMMARY                         ║
╠══════════════════════════════════════════════════════════════╣
║ Total Tests Passed:                                       19 ║
║ Total Tests Failed:                                        0 ║
║ Execution Time:                                       12.5ms ║
╚══════════════════════════════════════════════════════════════╝

✓ ALL TESTS PASSED! Sistem siap untuk production.
```

---

## 📝 Penjelasan Setiap Test

### 1. MenuTest.php (5 Test Cases)

**Test 1: getAllMenus() Mengembalikan Semua Menu**
```php
public function testGetAllMenusShouldReturnAllMenus()
```
- ✓ Mengecek apakah method mengembalikan array
- ✓ Memverifikasi jumlah menu = 3
- ✓ Memastikan setiap menu memiliki id, name, price, category_name

**Test 2: getAllMenus() Include Kategori**
```php
public function testGetAllMenusShouldIncludeCategory()
```
- ✓ Memverifikasi setiap menu memiliki category_name
- ✓ Mengecek ketersediaan kategori "Makanan" dan "Minuman"

**Test 3: Harga Menu Valid**
```php
public function testGetAllMenusShouldHaveValidPrice()
```
- ✓ Memastikan harga > 0
- ✓ Memvalidasi harga adalah numeric

**Test 4: getCategories() Mengembalikan Semua Kategori**
```php
public function testGetCategoriesShouldReturnAllCategories()
```
- ✓ Mengecek minimal 1 kategori
- ✓ Memverifikasi jumlah kategori = 2

**Test 5: Struktur Kategori Benar**
```php
public function testGetCategoriesShouldHaveCorrectStructure()
```
- ✓ Setiap kategori memiliki id
- ✓ Setiap kategori memiliki name
- ✓ Setiap kategori memiliki type

---

### 2. OrderTest.php (6 Test Cases)

**Test 1: createOrder() Berhasil**
```php
public function testCreateOrderShouldSucceed()
```
- ✓ Order berhasil dibuat
- ✓ Mengembalikan order_no yang unique

**Test 2: Order Number Format Benar**
```php
public function testCreateOrderShouldGenerateCorrectFormat()
```
- ✓ Order number dimulai dengan "ORD-"
- ✓ Format: ORD-{YmdHis}-{random}

**Test 3: Berbagai Total Amount**
```php
public function testCreateOrderWithDifferentAmounts()
```
- ✓ Order berhasil dengan jumlah: 50000, 100000, 150000
- ✓ Tidak ada batasan amount tertentu

**Test 4: Berbagai Payment Method**
```php
public function testCreateOrderWithDifferentPaymentMethods()
```
- ✓ Mendukung: cash, card, transfer
- ✓ Setiap method berhasil diprosesnya

**Test 5: Multiple Items**
```php
public function testCreateOrderWithMultipleItems()
```
- ✓ Order berhasil dengan 3 items berbeda
- ✓ Setiap item diproses dengan benar

**Test 6: Error Handling**
```php
public function testCreateOrderShouldFailOnException()
```
- ✓ Menangani edge case (empty items)
- ✓ Error handling bekerja dengan baik

---

### 3. ReportTest.php (8 Test Cases)

**Test 1-3: getDailyRecap() - Summary**
```php
public function testGetDailyRecapShouldReturnSummary()
public function testGetDailyRecapShouldCountOrdersCorrectly()
public function testGetDailyRecapShouldCalculateRevenueCorrectly()
```
- ✓ Mengembalikan total_orders dan total_revenue
- ✓ Total order dan revenue adalah numeric
- ✓ Nilai-nilai valid (>= 0)

**Test 4: Berbagai Tanggal**
```php
public function testGetDailyRecapWithDifferentDates()
```
- ✓ getDailyRecap() berfungsi untuk berbagai tanggal
- ✓ Flexibility dalam date filtering

**Test 5-8: getPaymentMethodStats() - Statistics**
```php
public function testGetPaymentMethodStatsShouldReturnStats()
public function testGetPaymentMethodStatsShouldGroupCorrectly()
public function testGetPaymentMethodStatsShouldCountCorrectly()
public function testGetPaymentMethodStatsShouldSumCorrectly()
```
- ✓ Mengembalikan statistik per payment method
- ✓ Tidak ada duplikat payment method
- ✓ Count dan total valid
- ✓ Aggregation bekerja dengan benar

---

## 💡 Tips dan Trik

### 1. Menambah Test Baru

Untuk menambah test baru, ikuti pattern ini:

```php
public function testSomethingShouldHappenCorrectly() {
    // Arrange - Setup data dan object
    $this->setUp();
    
    // Act - Jalankan method yang ditest
    $result = $this->menu->getAllMenus();
    
    // Assert - Verifikasi hasil
    $this->assertTrue(count($result) > 0, "Message jika fail");
}
```

### 2. Mengubah Fake Data

Data fake berada di `FakePDO.php` dalam method `initializeFakeData()`:

```php
private function initializeFakeData() {
    $this->data['menus'] = [
        [
            'id' => 1,
            'name' => 'Produk Baru',
            'price' => 30000,
            // ...
        ]
    ];
}
```

### 3. Debugging Test

Tambahkan `echo` atau `var_dump()` untuk debugging:

```php
public function testSomething() {
    $result = $this->menu->getAllMenus();
    var_dump($result->fetchAll()); // Lihat data yang dikembalikan
    $this->assertTrue(true, "Test");
}
```

### 4. Best Practices

✅ **DO:**
- Setiap test harus independen
- Gunakan setUp() untuk inisialisasi
- Test satu behavior per method
- Gunakan assertion yang jelas dan deskriptif
- Beri nama test yang menjelaskan apa yang ditest

❌ **DON'T:**
- Tidak boleh test bergantung pada test lain
- Tidak boleh mengakses database real dalam test
- Tidak boleh membuat side effects
- Tidak boleh skip test tanpa alasan
- Tidak boleh test internal implementation, test behavior

---

## 📚 Referensi Unit Test Doubles

### 5 Tipe Test Double:

| Tipe | Fungsi | Contoh |
|------|--------|--------|
| **Dummy** | Placeholder yang tidak digunakan | null parameter |
| **Fake** | Implementasi simulasi, avoid I/O | FakePDO di project ini |
| **Stub** | Return fixed value | Mock dengan return value tetap |
| **Spy** | Record pemanggilan | Track method calls |
| **Mock** | Verify behavior dan urutan | Assert method called with params |

Project ini menggunakan **FAKE** karena:
- ⚡ Performa terbaik
- 🎯 Simulasi akurat
- 📊 Data predictable
- 🚀 Easy to extend

---

## 🎓 Kesimpulan

Dengan menggunakan Fake Test Double, project GoofyCoffe memiliki:

- ✓ **19 Unit Tests** yang komprehensif
- ✓ **Isolasi sempurna** dari database
- ✓ **Eksekusi super cepat** (milliseconds)
- ✓ **Kualitas kode** yang terjamin
- ✓ **Dokumentasi hidup** dari behavior kode

Setiap developer dapat menjalankan test kapan saja tanpa setup database kompleks, dan yakin bahwa perubahan kode tidak akan break existing functionality.

---

**Created by:** Kelompok Developers  
**Date:** 2026  
**Pattern:** Fake Test Double  
**Framework:** Native PHP Unit Testing  
