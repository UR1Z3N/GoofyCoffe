<?php
class Report {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getDailyRecap($date) {
        $query = "SELECT 
                    COUNT(id) as total_orders,
                    SUM(total_amount) as total_revenue
                  FROM orders 
                  WHERE DATE(created_at) = :date AND status = 'Selesai'";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPaymentMethodStats($date) {
        $query = "SELECT 
                    payment_method,
                    COUNT(id) as count,
                    SUM(total_amount) as total
                  FROM orders 
                  WHERE DATE(created_at) = :date AND status = 'Selesai'
                  GROUP BY payment_method";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
