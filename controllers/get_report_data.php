<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../models/Report.php';

$database = new Database();
$db = $database->getConnection();
$reportModel = new Report($db);

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$recap = $reportModel->getDailyRecap($date);
$total_revenue = $recap['total_revenue'] ?? 0;
$total_orders = $recap['total_orders'] ?? 0;

$paymentStats = $reportModel->getPaymentMethodStats($date);
$tunai_total = 0;
$digital_total = 0;

foreach ($paymentStats as $stat) {
    if ($stat['payment_method'] == 'Tunai') {
        $tunai_total = $stat['total'];
    } else if ($stat['payment_method'] == 'Digital') {
        $digital_total = $stat['total'];
    }
}

$total = $tunai_total + $digital_total;
$pct_tunai = $total > 0 ? round(($tunai_total / $total) * 100) : 0;
$pct_digital = $total > 0 ? round(($digital_total / $total) * 100) : 0;

echo json_encode([
    'success' => true,
    'total_revenue' => $total_revenue,
    'total_orders' => $total_orders,
    'tunai_total' => $tunai_total,
    'digital_total' => $digital_total,
    'pct_tunai' => $pct_tunai,
    'pct_digital' => $pct_digital,
    'total' => $total
]);
?>
