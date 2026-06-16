<?php
require_once '../config/database.php';
require_once '../models/Order.php';
require_once '../models/Report.php';

$database = new Database();
$db = $database->getConnection();
$orderModel = new Order($db);
$reportModel = new Report($db);

$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

// Get recap data
$recap = $reportModel->getMonthlyRecap($month);
$total_revenue = $recap['total_revenue'] ?? 0;
$total_orders = $recap['total_orders'] ?? 0;

$paymentStats = $reportModel->getMonthlyPaymentMethodStats($month);
$tunai_total = 0;
$digital_total = 0;

foreach ($paymentStats as $stat) {
    if ($stat['payment_method'] == 'Tunai') {
        $tunai_total = $stat['total'];
    } else if ($stat['payment_method'] == 'Digital') {
        $digital_total = $stat['total'];
    }
}

// Get order details for this month
$start_date = $month . '-01';
$end_date = date('Y-m-t', strtotime($start_date));
$stmtHistory = $orderModel->getOrdersHistory($start_date, $end_date);
$orders = $stmtHistory->fetchAll(PDO::FETCH_ASSOC);

// Output HTML report
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Bulanan - <?= date('F Y', strtotime($start_date)) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-10 rounded-2xl shadow-lg border border-gray-200" id="printable-area">
        <!-- Header Laporan -->
        <div class="flex justify-between items-center border-b-2 border-green-600 pb-6 mb-6">
            <div class="flex items-center space-x-4">
                <div class="bg-green-700 p-3 rounded-xl">
                    <!-- Text logo fallback since we might not have the image loaded properly in print -->
                    <span class="text-white font-bold text-xl"><i class="fas fa-coffee"></i> GC</span>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-gray-800">GOOFY COFFEE</h1>
                    <p class="text-gray-500 font-medium">Laporan Pendapatan Bulanan</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500 uppercase tracking-widest font-bold mb-1">Bulan Laporan</p>
                <p class="text-xl font-bold text-green-700"><?= date('F Y', strtotime($start_date)) ?></p>
                <p class="text-xs text-gray-400 mt-1">Dicetak pada: <?= date('d M Y H:i:s') ?></p>
            </div>
        </div>

        <!-- Ringkasan Grid -->
        <div class="grid grid-cols-4 gap-4 mb-8">
            <div class="bg-green-50 p-4 rounded-xl border border-green-100 text-center">
                <p class="text-xs font-bold text-green-600 uppercase mb-1">Total Pesanan</p>
                <p class="text-2xl font-black text-gray-800"><?= $total_orders ?></p>
            </div>
            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-center">
                <p class="text-xs font-bold text-blue-600 uppercase mb-1">Tunai</p>
                <p class="text-xl font-bold text-gray-800">Rp <?= number_format($tunai_total, 0, ',', '.') ?></p>
            </div>
            <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 text-center">
                <p class="text-xs font-bold text-indigo-600 uppercase mb-1">Digital/QRIS</p>
                <p class="text-xl font-bold text-gray-800">Rp <?= number_format($digital_total, 0, ',', '.') ?></p>
            </div>
            <div class="bg-green-600 p-4 rounded-xl shadow-md text-center text-white">
                <p class="text-xs font-bold text-green-200 uppercase mb-1">Total Pendapatan</p>
                <p class="text-xl font-bold">Rp <?= number_format($total_revenue, 0, ',', '.') ?></p>
            </div>
        </div>

        <!-- Tabel Transaksi -->
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="w-2 h-6 bg-green-500 rounded mr-2"></span> Rincian Transaksi
        </h3>
        
        <?php if(count($orders) > 0): ?>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm border-b border-gray-300">
                    <th class="py-3 px-4 font-bold rounded-tl-lg">No</th>
                    <th class="py-3 px-4 font-bold">No. Order</th>
                    <th class="py-3 px-4 font-bold">Waktu</th>
                    <th class="py-3 px-4 font-bold">Metode</th>
                    <th class="py-3 px-4 font-bold text-right rounded-tr-lg">Total Belanja</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($orders as $order): ?>
                <tr class="border-b border-gray-100 text-sm <?= $no % 2 == 0 ? 'bg-gray-50' : '' ?>">
                    <td class="py-3 px-4 text-gray-500"><?= $no++ ?></td>
                    <td class="py-3 px-4 font-semibold text-gray-700"><?= $order['order_no'] ?></td>
                    <td class="py-3 px-4 text-gray-500"><?= date('H:i:s', strtotime($order['created_at'])) ?></td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded text-xs font-bold <?= $order['payment_method'] == 'Tunai' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' ?>">
                            <?= $order['payment_method'] ?>
                        </span>
                    </td>
                    <td class="py-3 px-4 font-bold text-gray-800 text-right">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="bg-gray-800 text-white">
                    <td colspan="4" class="py-4 px-4 font-bold text-right rounded-bl-lg">TOTAL KESELURUHAN</td>
                    <td class="py-4 px-4 font-black text-lg text-right rounded-br-lg">Rp <?= number_format($total_revenue, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
        <?php else: ?>
        <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300">
            <p class="text-gray-400 font-medium">Tidak ada transaksi pada bulan ini.</p>
        </div>
        <?php endif; ?>
        
        <div class="mt-10 text-center text-xs text-gray-400 pb-4 border-b border-gray-100">
            &copy; <?= date('Y') ?> Goofy Coffee POS System. Laporan digenerate secara otomatis.
        </div>
        
        <div class="mt-6 text-center no-print" id="action-buttons">
            <p class="text-gray-500 mb-2"><i class="fas fa-spinner fa-spin text-green-500 mr-2"></i> Sedang menyiapkan unduhan PDF...</p>
            <p class="text-xs text-gray-400">Jendela ini akan tertutup otomatis setelah unduhan selesai.</p>
        </div>
    </div>
    
    <!-- Load html2pdf.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        window.onload = function() {
            const element = document.getElementById('printable-area');
            const actionButtons = document.getElementById('action-buttons');
            
            // Hide the loading text from the PDF itself
            actionButtons.style.display = 'none';
            
            const opt = {
              margin:       [0.5, 0.5, 0.5, 0.5],
              filename:     'Laporan_Pendapatan_<?= $month ?>.pdf',
              image:        { type: 'jpeg', quality: 0.98 },
              html2canvas:  { scale: 2, useCORS: true },
              jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
            };
            
            // Generate PDF and download
            html2pdf().set(opt).from(element).save().then(function() {
                // Show a success message
                actionButtons.style.display = 'block';
                actionButtons.innerHTML = '<p class="text-green-600 font-bold"><i class="fas fa-check-circle mr-2"></i> Unduhan Selesai!</p><p class="text-xs text-gray-500 mt-1">Anda bisa menutup tab ini.</p>';
                
                // Auto close the tab after 2 seconds
                setTimeout(() => {
                    window.close();
                }, 2000);
            });
        };
    </script>
</body>
</html>
