<?php
// ========================================================
// TUGAS UNIT TESTING - DESAIN PERANGKAT LUNAK
// Pengujian Model Report Menggunakan Fake Database (Test Double)
// NIM Anggota : 202310370311365
// Nama      : Dianda Naufal Rahmanda
// ========================================================

// Set content-type ke text/plain agar rapi saat dibuka di browser
if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain');
}

// Import model Report yang akan diuji
require_once __DIR__ . '/models/Report.php';

/**
 * Class FakePDO
 * Digunakan sebagai Fake Object untuk menggantikan koneksi PDO asli ke MySQL.
 * Menyimpan data di memory (array) untuk menghindari operasi I/O database.
 */
class FakePDO {
    public $orders = [];

    public function __construct($orders = []) {
        $this->orders = $orders;
    }

    public function prepare($query) {
        return new FakePDOStatement($this, $query);
    }
}

/**
 * Class FakePDOStatement
 * Mensimulasikan eksekusi statement PDO secara in-memory.
 */
class FakePDOStatement {
    private $db;
    private $query;
    private $params = [];
    private $result = [];
    private $index = 0;

    public function __construct($db, $query) {
        $this->db = $db;
        $this->query = $query;
    }

    public function bindParam($param, &$var, $type = null, $length = null, $driver_options = null) {
        $this->params[$param] = &$var;
        return true;
    }

    public function execute($params = null) {
        if ($params !== null) {
            $this->params = array_merge($this->params, $params);
        }

        $date = $this->params[':date'] ?? null;

        // Simulasi query getDailyRecap()
        if (strpos($this->query, 'total_revenue') !== false) {
            $total_orders = 0;
            $total_revenue = 0;

            foreach ($this->db->orders as $order) {
                $orderDate = date('Y-m-d', strtotime($order['created_at']));
                if ($orderDate === $date && $order['status'] === 'Selesai') {
                    $total_orders++;
                    $total_revenue += $order['total_amount'];
                }
            }

            $this->result = [
                'total_orders' => $total_orders,
                'total_revenue' => $total_revenue
            ];
        }
        // Simulasi query getPaymentMethodStats()
        elseif (strpos($this->query, 'GROUP BY payment_method') !== false) {
            $groups = [];
            foreach ($this->db->orders as $order) {
                $orderDate = date('Y-m-d', strtotime($order['created_at']));
                if ($orderDate === $date && $order['status'] === 'Selesai') {
                    $method = $order['payment_method'];
                    if (!isset($groups[$method])) {
                        $groups[$method] = [
                            'payment_method' => $method,
                            'count' => 0,
                            'total' => 0
                        ];
                    }
                    $groups[$method]['count']++;
                    $groups[$method]['total'] += $order['total_amount'];
                }
            }
            $this->result = array_values($groups);
        }

        $this->index = 0;
        return true;
    }

    public function fetch($mode = null) {
        // Jika hasil berupa record tunggal (getDailyRecap)
        if (isset($this->result['total_orders'])) {
            $temp = $this->result;
            $this->result = null; // Menandakan EOF
            return $temp;
        }

        // Jika hasil berupa list (getPaymentMethodStats)
        if (is_array($this->result) && $this->index < count($this->result)) {
            return $this->result[$this->index++];
        }
        return false;
    }

    public function fetchAll($mode = null) {
        return $this->result;
    }
}

// ========================================================
// TEST CASES RUNNER
// ========================================================

$errors = [];
$tests_run = 0;

function assertEquals($expected, $actual, $testName) {
    global $errors, $tests_run;
    $tests_run++;
    if ($expected !== $actual) {
        $errors[] = "❌ FAIL: [{$testName}] - Diharapkan: " . json_encode($expected) . ", didapat: " . json_encode($actual);
    } else {
        echo "✅ PASS: [{$testName}]\n";
    }
}

// Setup Dummy Data untuk pengujian
$dummyOrders = [
    ['id' => 1, 'order_no' => 'ORD-001', 'total_amount' => 50000, 'payment_method' => 'Tunai', 'status' => 'Selesai', 'created_at' => '2026-05-26 09:00:00'],
    ['id' => 2, 'order_no' => 'ORD-002', 'total_amount' => 30000, 'payment_method' => 'Digital', 'status' => 'Selesai', 'created_at' => '2026-05-26 10:30:00'],
    ['id' => 3, 'order_no' => 'ORD-003', 'total_amount' => 25000, 'payment_method' => 'Tunai', 'status' => 'Dibatalkan', 'created_at' => '2026-05-26 11:00:00'], // Harus diabaikan
    ['id' => 4, 'order_no' => 'ORD-004', 'total_amount' => 40000, 'payment_method' => 'Tunai', 'status' => 'Selesai', 'created_at' => '2026-05-25 15:00:00'],   // Beda tanggal, harus diabaikan
];

// Instansiasi Fake Database & Model Report
$fakeDb = new FakePDO($dummyOrders);
$report = new Report($fakeDb);

echo "========================================================\n";
echo "MULAI RUNNING UNIT TEST (TIPE TEST DOUBLE: FAKE)\n";
echo "========================================================\n\n";

// 1. Pengujian getDailyRecap() dengan transaksi valid
echo "--- Uji getDailyRecap() ---\n";
$recap = $report->getDailyRecap('2026-05-26');
assertEquals(2, (int)$recap['total_orders'], "Jumlah pesanan pada 2026-05-26 harusnya 2");
assertEquals(80000, (int)$recap['total_revenue'], "Total pendapatan pada 2026-05-26 harusnya 80000");

// 2. Pengujian getDailyRecap() pada tanggal kosong (tanpa transaksi)
$emptyRecap = $report->getDailyRecap('2026-05-24');
assertEquals(0, (int)$emptyRecap['total_orders'], "Jumlah pesanan pada tanggal kosong harusnya 0");
assertEquals(0, (int)$emptyRecap['total_revenue'], "Total pendapatan pada tanggal kosong harusnya 0");

// 3. Pengujian getPaymentMethodStats()
echo "\n--- Uji getPaymentMethodStats() ---\n";
$stats = $report->getPaymentMethodStats('2026-05-26');

// Map hasil array untuk mempermudah assert
$mappedStats = [];
foreach ($stats as $row) {
    $mappedStats[$row['payment_method']] = $row;
}

assertEquals(true, isset($mappedStats['Tunai']), "Statistik metode 'Tunai' harus ada");
if (isset($mappedStats['Tunai'])) {
    assertEquals(1, (int)$mappedStats['Tunai']['count'], "Jumlah transaksi Tunai harusnya 1");
    assertEquals(50000, (int)$mappedStats['Tunai']['total'], "Total pendapatan Tunai harusnya 50000");
}

assertEquals(true, isset($mappedStats['Digital']), "Statistik metode 'Digital' harus ada");
if (isset($mappedStats['Digital'])) {
    assertEquals(1, (int)$mappedStats['Digital']['count'], "Jumlah transaksi Digital harusnya 1");
    assertEquals(30000, (int)$mappedStats['Digital']['total'], "Total pendapatan Digital harusnya 30000");
}

// 4. Pengujian getPaymentMethodStats() pada tanggal kosong
$emptyStats = $report->getPaymentMethodStats('2026-05-24');
assertEquals(0, count($emptyStats), "Hasil statistik metode pembayaran tanggal kosong harus array kosong");

// ========================================================
// KESIMPULAN HASIL PENGUJIAN
// ========================================================
echo "\n========================================================\n";
echo "RINGKASAN HASIL UJI:\n";
echo "Total Pengujian dijalankan : {$tests_run}\n";
echo "Jumlah Sukses              : " . ($tests_run - count($errors)) . "\n";
echo "Jumlah Gagal               : " . count($errors) . "\n";
echo "========================================================\n";

if (count($errors) > 0) {
    echo "Detail Kesalahan:\n";
    foreach ($errors as $err) {
        echo $err . "\n";
    }
} else {
    echo "👉 KESIMPULAN: SEMUA UNIT TEST SUKSES DIJALANKAN (100% PASSED)!\n";
}
echo "========================================================\n";
