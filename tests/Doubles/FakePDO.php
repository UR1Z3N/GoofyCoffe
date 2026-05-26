<?php
/**
 * FakePDO - Fake Test Double untuk PDO
 * 
 * Fake adalah test double yang mensimulasikan fungsionalitas kelas asli 
 * tetapi menghindari operasi kompleks seperti I/O database nyata,
 * sehingga pengujian lebih cepat dan stabil.
 * 
 * Kelas ini meniru perilaku PDO tanpa benar-benar terhubung ke database.
 */
class FakePDO {
    private $data = [];
    private $lastInsertId = 0;
    private $inTransaction = false;

    public function __construct() {
        // Inisialisasi data fake
        $this->initializeFakeData();
    }

    /**
     * Inisialisasi data dummy untuk simulasi database
     */
    private function initializeFakeData() {
        // Fake menus
        $this->data['menus'] = [
            [
                'id' => 1,
                'name' => 'Mie Goreng Spesial',
                'price' => 25000,
                'image' => 'mie_goreng.jpg',
                'category_id' => 1,
                'category_name' => 'Makanan'
            ],
            [
                'id' => 2,
                'name' => 'Nasi Goreng',
                'price' => 22000,
                'image' => 'nasi_goreng.jpg',
                'category_id' => 1,
                'category_name' => 'Makanan'
            ],
            [
                'id' => 3,
                'name' => 'Kopi Espresso',
                'price' => 18000,
                'image' => 'espresso.jpg',
                'category_id' => 2,
                'category_name' => 'Minuman'
            ]
        ];

        // Fake categories
        $this->data['categories'] = [
            ['id' => 1, 'name' => 'Makanan', 'type' => 'food'],
            ['id' => 2, 'name' => 'Minuman', 'type' => 'drink']
        ];

        // Fake orders
        $this->data['orders'] = [
            [
                'id' => 1,
                'order_no' => 'ORD-20260101120000-123',
                'total_amount' => 50000,
                'payment_method' => 'cash',
                'status' => 'Selesai',
                'created_at' => date('Y-m-d', strtotime('today'))
            ],
            [
                'id' => 2,
                'order_no' => 'ORD-20260101130000-456',
                'total_amount' => 75000,
                'payment_method' => 'card',
                'status' => 'Selesai',
                'created_at' => date('Y-m-d', strtotime('today'))
            ]
        ];

        $this->lastInsertId = 3;
    }

    /**
     * Simulasi prepare method dari PDO
     */
    public function prepare($query) {
        return new FakePDOStatement($this, $query, $this->data);
    }

    /**
     * Mulai transaksi
     */
    public function beginTransaction() {
        $this->inTransaction = true;
        return true;
    }

    /**
     * Commit transaksi
     */
    public function commit() {
        $this->inTransaction = false;
        return true;
    }

    /**
     * Rollback transaksi
     */
    public function rollBack() {
        $this->inTransaction = false;
        return true;
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        return $this->lastInsertId;
    }

    /**
     * Set attribute (simulasi)
     */
    public function setAttribute($attribute, $value) {
        return true;
    }

    /**
     * Get data untuk testing
     */
    public function getTestData($table) {
        return isset($this->data[$table]) ? $this->data[$table] : [];
    }

    /**
     * Tambah data untuk testing
     */
    public function addTestData($table, $data) {
        if (!isset($this->data[$table])) {
            $this->data[$table] = [];
        }
        $this->data[$table][] = $data;
        return true;
    }

    /**
     * Increment last insert ID
     */
    public function incrementLastInsertId() {
        $this->lastInsertId++;
    }
}

/**
 * FakePDOStatement - Fake Test Double untuk PDOStatement
 */
class FakePDOStatement {
    private $pdo;
    private $query;
    private $data;
    private $params = [];
    private $currentResult = [];
    private $resultIndex = 0;

    public function __construct($pdo, $query, $data) {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->data = $data;
    }

    /**
     * Bind parameter
     */
    public function bindParam($param, &$value, $type = null) {
        $this->params[$param] = $value;
        return true;
    }

    /**
     * Execute statement
     */
    public function execute($params = null) {
        if ($params) {
            $this->params = $params;
        }

        // Simulasi query berbeda berdasarkan tipe
        if (strpos(strtoupper($this->query), 'SELECT') !== false) {
            $this->processSelectQuery();
        } elseif (strpos(strtoupper($this->query), 'INSERT') !== false) {
            $this->processInsertQuery();
        }

        return true;
    }

    /**
     * Proses SELECT query
     */
    private function processSelectQuery() {
        if (strpos($this->query, 'menus') !== false && 
            strpos($this->query, 'categories') !== false) {
            // Query getAllMenus
            $this->currentResult = array_map(function($menu) {
                foreach ($this->data['categories'] as $cat) {
                    if ($cat['id'] == $menu['category_id']) {
                        $menu['category_name'] = $cat['name'];
                        break;
                    }
                }
                return $menu;
            }, $this->data['menus']);
        } elseif (strpos($this->query, 'categories') !== false) {
            // Query getCategories
            $this->currentResult = $this->data['categories'];
        } elseif (strpos($this->query, 'orders') !== false) {
            // Query orders dengan filter date
            if (isset($this->params[':date'])) {
                $filteredDate = $this->params[':date'];
                $this->currentResult = array_filter(
                    $this->data['orders'],
                    function($order) use ($filteredDate) {
                        return strpos($order['created_at'], $filteredDate) === 0 && 
                               $order['status'] === 'Selesai';
                    }
                );
                // Hitung summary jika ada COUNT
                if (strpos($this->query, 'COUNT') !== false) {
                    $this->currentResult = [[
                        'total_orders' => count($this->currentResult),
                        'total_revenue' => array_sum(array_column($this->currentResult, 'total_amount'))
                    ]];
                }
                // Hitung stats jika ada GROUP BY
                if (strpos($this->query, 'GROUP BY') !== false) {
                    $stats = [];
                    foreach ($this->currentResult as $order) {
                        $method = $order['payment_method'];
                        if (!isset($stats[$method])) {
                            $stats[$method] = [
                                'payment_method' => $method,
                                'count' => 0,
                                'total' => 0
                            ];
                        }
                        $stats[$method]['count']++;
                        $stats[$method]['total'] += $order['total_amount'];
                    }
                    $this->currentResult = array_values($stats);
                }
            }
        }
    }

    /**
     * Proses INSERT query
     */
    private function processInsertQuery() {
        if (strpos($this->query, 'orders') !== false && 
            strpos($this->query, 'order_items') === false) {
            // Insert order
            $order_id = $this->pdo->lastInsertId();
            $newOrder = [
                'id' => $order_id,
                'order_no' => $this->params[':order_no'],
                'total_amount' => $this->params[':total_amount'],
                'payment_method' => $this->params[':payment_method'],
                'status' => 'Pending',
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->pdo->addTestData('orders', $newOrder);
            $this->pdo->incrementLastInsertId();
        }
    }

    /**
     * Fetch single result
     */
    public function fetch($fetchMode = PDO::FETCH_ASSOC) {
        if (is_array($this->currentResult) && !empty($this->currentResult)) {
            if ($this->resultIndex < count($this->currentResult)) {
                return $this->currentResult[$this->resultIndex++];
            }
        }
        return false;
    }

    /**
     * Fetch all results
     */
    public function fetchAll($fetchMode = PDO::FETCH_ASSOC) {
        return $this->currentResult;
    }

    /**
     * Get rowCount
     */
    public function rowCount() {
        return count($this->currentResult);
    }
}
