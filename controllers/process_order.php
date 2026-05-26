<?php
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../models/Order.php';

// Get POST data
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!$input) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$orderModel = new Order($db);

$total_amount = $input['total_amount'];
$payment_method = $input['payment_method'];
$items = $input['items'];

if (empty($items)) {
    echo json_encode(["success" => false, "message" => "Cart is empty"]);
    exit;
}

// Security: Note that PDO statements are used in Order.php to prevent SQL injection.
$result = $orderModel->createOrder($total_amount, $payment_method, $items);

echo json_encode($result);
?>
