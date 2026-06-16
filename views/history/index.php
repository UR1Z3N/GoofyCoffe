<?php
require_once 'config/database.php';
require_once 'models/Order.php';

$database = new Database();
$db = $database->getConnection();
$orderModel = new Order($db);

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$stmtHistory = $orderModel->getOrdersHistory($start_date, $end_date);
$orders = $stmtHistory->fetchAll(PDO::FETCH_ASSOC);

function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}
?>

<main class="flex-1 p-8 overflow-y-auto bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 space-y-4 md:space-y-0">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Riwayat Transaksi</h1>
                <p class="text-gray-500 text-sm mt-1">Daftar transaksi yang telah selesai</p>
            </div>
            
            <form action="" method="GET" class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-3 items-start md:items-end bg-white p-4 rounded-xl shadow-sm border border-gray-100 w-full md:w-auto">
                <input type="hidden" name="page" value="history">
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" class="border border-gray-300 rounded-lg p-2 text-sm focus:outline-none focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" class="border border-gray-300 rounded-lg p-2 text-sm focus:outline-none focus:border-green-500">
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors text-sm h-[38px]">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="?page=history" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg transition-colors text-sm h-[38px] flex items-center">
                    Reset
                </a>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b border-gray-200 text-sm">
                        <th class="p-4 font-bold">No. Order</th>
                        <th class="p-4 font-bold">Waktu</th>
                        <th class="p-4 font-bold">Metode Pembayaran</th>
                        <th class="p-4 font-bold">Total Belanja</th>
                        <th class="p-4 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="p-4 font-semibold text-gray-800"><?= $order['order_no'] ?></td>
                                <td class="p-4 text-gray-600 text-sm"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></td>
                                <td class="p-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold <?= $order['payment_method'] == 'Tunai' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' ?>">
                                        <?= $order['payment_method'] ?>
                                    </span>
                                </td>
                                <td class="p-4 font-bold text-gray-800"><?= formatRupiah($order['total_amount']) ?></td>
                                <td class="p-4 text-center">
                                    <button onclick="viewDetails(<?= $order['id'] ?>, '<?= $order['order_no'] ?>', '<?= $order['total_amount'] ?>')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-green-600 text-sm py-1.5 px-3 rounded-lg transition-colors shadow-sm">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-300 block"></i>
                                Tidak ada transaksi ditemukan
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Order Details Modal -->
<div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-2xl w-full max-w-[500px] mx-4 shadow-2xl overflow-hidden transform transition-all flex flex-col max-h-[90vh]">
        <div class="bg-gray-800 p-4 text-white flex justify-between items-center shrink-0">
            <div>
                <h3 class="font-bold text-lg">Detail Transaksi</h3>
                <p class="text-xs text-gray-300" id="modal-order-no"></p>
            </div>
            <button onclick="closeDetailModal()" class="text-gray-300 hover:text-white"><i class="fas fa-times text-xl"></i></button>
        </div>
        
        <div class="p-0 overflow-y-auto flex-1">
            <div id="detail-loading" class="p-10 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin text-3xl mb-2 text-green-500"></i>
                <p>Memuat detail...</p>
            </div>
            <ul id="detail-items" class="divide-y divide-gray-100 hidden">
                <!-- Items rendered here by JS -->
            </ul>
        </div>
        
        <div class="p-5 bg-gray-50 border-t border-gray-200 shrink-0 flex justify-between items-center">
            <span class="text-gray-600 font-bold">Total Pembayaran</span>
            <span class="text-2xl font-bold text-green-600" id="modal-order-total"></span>
        </div>
    </div>
</div>

<script>
function viewDetails(orderId, orderNo, totalAmount) {
    document.getElementById('modal-order-no').textContent = orderNo;
    document.getElementById('modal-order-total').textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalAmount);
    
    document.getElementById('detail-modal').classList.remove('hidden');
    document.getElementById('detail-loading').classList.remove('hidden');
    document.getElementById('detail-items').classList.add('hidden');
    
    fetch(`controllers/get_order_details.php?order_id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            const list = document.getElementById('detail-items');
            list.innerHTML = '';
            
            if (data.success && data.data.length > 0) {
                data.data.forEach(item => {
                    const priceFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.price);
                    const subTotalFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.price * item.quantity);
                    
                    let notesHtml = '';
                    if (item.notes && item.notes.trim() !== '') {
                        notesHtml = `<p class="text-xs text-gray-500 mt-1"><i class="fas fa-sticky-note mr-1 text-yellow-500"></i>${item.notes}</p>`;
                    }
                    
                    list.innerHTML += `
                        <li class="p-4 hover:bg-gray-50 transition">
                            <div class="flex justify-between items-start mb-1">
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">${item.menu_name}</h4>
                                    <p class="text-xs text-gray-500">${item.quantity} x ${priceFormatted}</p>
                                    ${notesHtml}
                                </div>
                                <span class="font-bold text-gray-800 text-sm">${subTotalFormatted}</span>
                            </div>
                        </li>
                    `;
                });
            } else {
                list.innerHTML = `<li class="p-4 text-center text-sm text-gray-500">Tidak ada detail item.</li>`;
            }
            
            document.getElementById('detail-loading').classList.add('hidden');
            document.getElementById('detail-items').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching details:', error);
            document.getElementById('detail-loading').innerHTML = '<p class="text-red-500 text-sm">Gagal memuat data.</p>';
        });
}

function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
}
</script>
