<?php
require_once __DIR__ . '/interfaces/OrderRepositoryInterface.php';

class Order implements OrderRepositoryInterface {
    private $conn;
    private $table_name = "orders";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function createOrder($total_amount, $payment_method, $items) {
        try {
            $this->conn->beginTransaction();

            // Create Order No
            $order_no = "ORD-" . date('YmdHis') . "-" . rand(100, 999);

            // Insert into orders
            $query = "INSERT INTO " . $this->table_name . " 
                      (order_no, total_amount, payment_method) 
                      VALUES (:order_no, :total_amount, :payment_method)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":order_no", $order_no);
            $stmt->bindParam(":total_amount", $total_amount);
            $stmt->bindParam(":payment_method", $payment_method);
            $stmt->execute();

            $order_id = $this->conn->lastInsertId();

            // Insert into order_items
            $queryItem = "INSERT INTO order_items 
                          (order_id, menu_id, quantity, price, notes) 
                          VALUES (:order_id, :menu_id, :quantity, :price, :notes)";
            $stmtItem = $this->conn->prepare($queryItem);

            foreach ($items as $item) {
                $stmtItem->bindParam(":order_id", $order_id);
                $stmtItem->bindParam(":menu_id", $item['menu_id']);
                $stmtItem->bindParam(":quantity", $item['quantity']);
                $stmtItem->bindParam(":price", $item['price']);
                $stmtItem->bindParam(":notes", $item['notes']);
                $stmtItem->execute();
            }

            $this->conn->commit();
            return ["success" => true, "order_no" => $order_no];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getOrdersHistory($start_date = null, $end_date = null) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'Selesai'";
        
        if ($start_date && $end_date) {
            $query .= " AND DATE(created_at) BETWEEN :start_date AND :end_date";
        }
        
        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        
        if ($start_date && $end_date) {
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
        }
        
        $stmt->execute();
        return $stmt;
    }

    public function getOrderDetails($order_id) {
        $query = "SELECT oi.*, m.name as menu_name 
                  FROM order_items oi
                  JOIN menus m ON oi.menu_id = m.id
                  WHERE oi.order_id = :order_id";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        
        return $stmt;
    }
}
?>
