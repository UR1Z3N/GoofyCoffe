<?php
require_once 'config/database.php';
require_once 'models/Report.php';

$database = new Database();
$db = $database->getConnection();
$reportModel = new Report($db);

$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$recap = $reportModel->getDailyRecap($selected_date);
$total_revenue = $recap['total_revenue'] ?? 0;
$total_orders = $recap['total_orders'] ?? 0;

$paymentStats = $reportModel->getPaymentMethodStats($selected_date);
$tunai_total = 0;
$digital_total = 0;

foreach ($paymentStats as $stat) {
    if ($stat['payment_method'] == 'Tunai') {
        $tunai_total = $stat['total'];
    } else if ($stat['payment_method'] == 'Digital') {
        $digital_total = $stat['total'];
    }
}

function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}
?>

<main class="flex-1 p-8 overflow-y-auto bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Laporan Keuangan</h1>
                <p class="text-gray-500 text-sm mt-1">Rekapitulasi pendapatan harian</p>
            </div>
            
            <form action="" method="GET" class="flex space-x-3 items-end bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <input type="hidden" name="page" value="report">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Tanggal</label>
                    <input type="date" name="date" value="<?= htmlspecialchars($selected_date) ?>" class="border border-gray-300 rounded-lg p-2 text-sm focus:outline-none focus:border-green-500">
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors text-sm h-[38px]">
                    <i class="fas fa-search mr-1"></i> Tampilkan
                </button>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Revenue -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg shadow-green-200 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute -right-4 -top-4 opacity-20">
                    <i class="fas fa-wallet text-8xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-green-100 text-sm font-semibold mb-1">Total Pendapatan</p>
                    <h3 class="text-3xl font-bold"><?= formatRupiah($total_revenue) ?></h3>
                </div>
                <div class="mt-4 relative z-10 flex items-center text-sm text-green-100">
                    <i class="fas fa-calendar-day mr-2"></i> <?= date('d M Y', strtotime($selected_date)) ?>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="bg-white rounded-2xl p-6 text-gray-800 shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-3">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <p class="text-gray-500 text-sm font-semibold mb-1">Jumlah Transaksi</p>
                    <h3 class="text-2xl font-bold"><?= $total_orders ?> <span class="text-sm font-normal text-gray-400">order</span></h3>
                </div>
            </div>

            <!-- Cash Total -->
            <div class="bg-white rounded-2xl p-6 text-gray-800 shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <p class="text-gray-500 text-sm font-semibold mb-1">Pembayaran Tunai</p>
                    <h3 class="text-2xl font-bold"><?= formatRupiah($tunai_total) ?></h3>
                </div>
            </div>

            <!-- Digital Total -->
            <div class="bg-white rounded-2xl p-6 text-gray-800 shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mb-3">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <p class="text-gray-500 text-sm font-semibold mb-1">Pembayaran Digital</p>
                    <h3 class="text-2xl font-bold"><?= formatRupiah($digital_total) ?></h3>
                </div>
            </div>
        </div>

        <!-- Detail Breakdown Chart / Table area -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Rincian Metode Pembayaran</h3>
            
            <div class="flex items-center justify-center py-6">
                <!-- Simple CSS Progress Bar representation -->
                <?php
                $total = $tunai_total + $digital_total;
                $pct_tunai = $total > 0 ? round(($tunai_total / $total) * 100) : 0;
                $pct_digital = $total > 0 ? round(($digital_total / $total) * 100) : 0;
                ?>
                
                <div class="w-full max-w-2xl">
                    <div class="flex justify-between mb-2 text-sm font-bold">
                        <span class="text-emerald-600">Tunai (<?= $pct_tunai ?>%)</span>
                        <span class="text-indigo-600">Digital/QRIS (<?= $pct_digital ?>%)</span>
                    </div>
                    <div class="h-4 w-full bg-gray-100 rounded-full flex overflow-hidden">
                        <div class="h-full bg-emerald-500 transition-all duration-1000" style="width: <?= $pct_tunai ?>%"></div>
                        <div class="h-full bg-indigo-500 transition-all duration-1000" style="width: <?= $pct_digital ?>%"></div>
                    </div>
                </div>
            </div>
            
            <?php if ($total == 0): ?>
            <div class="text-center text-gray-400 py-8">
                <i class="fas fa-chart-pie text-4xl mb-3 text-gray-300 block"></i>
                Belum ada data untuk tanggal ini
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>
