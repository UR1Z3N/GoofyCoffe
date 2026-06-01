<?php
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../models/Order.php';

if (!isset($_GET['order_id'])) {
    echo json_encode(["success" => false, "message" => "Missing order ID"]);
    exit;
}

$order_id = $_GET['order_id'];

$database = new Database();
$db = $database->getConnection();
$orderModel = new Order($db);

try {
    $stmt = $orderModel->getOrderDetails($order_id);
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(["success" => true, "data" => $details]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
