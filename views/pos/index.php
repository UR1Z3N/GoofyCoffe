<?php
require_once 'config/database.php';
require_once 'models/Menu.php';

$database = new Database();
$db = $database->getConnection();

$menuModel = new Menu($db);
$stmtMenus = $menuModel->getAllMenus();
$menus = $stmtMenus->fetchAll(PDO::FETCH_ASSOC);

$stmtCategories = $menuModel->getCategories();
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

// Pass menu data to JS
$menuJson = json_encode($menus);
?>
<?php
$makanan = [];
$minuman = [];
foreach ($categories as $cat) {
    if (strtolower($cat['type']) == 'makanan') $makanan[] = $cat;
    else $minuman[] = $cat;
}
?>

<!-- Sidebar Categories -->
<aside id="tour-category" class="w-full lg:w-64 bg-[#f9f9f9] border-b lg:border-b-0 lg:border-r border-gray-200 overflow-x-auto lg:overflow-y-auto flex-shrink-0 flex flex-row lg:flex-col z-10 relative">
    <div class="py-2 lg:py-4 flex flex-row lg:flex-col overflow-x-auto lg:overflow-visible items-center lg:items-stretch w-full">
        <!-- MAKANAN -->
        <?php if(!empty($makanan)): ?>
        <div class="flex lg:block items-center mx-2 lg:mx-0 shrink-0">
            <h2 class="text-[13px] font-black text-gray-800 px-2 lg:px-4 mb-0 lg:mb-1 mt-0 lg:mt-2 uppercase flex items-center tracking-wider mr-2 lg:mr-0">
                <i class="fas fa-hamburger text-green-600 mr-2 text-sm"></i> MAKANAN
            </h2>
            <ul class="flex flex-row lg:flex-col lg:mb-4 space-x-2 lg:space-x-0">
                <?php foreach($makanan as $cat): ?>
                <li class="category-item shrink-0 cursor-pointer text-[13px] text-gray-700 hover:bg-gray-200 transition py-1.5 lg:py-2 px-3 lg:px-4 lg:pl-10 relative rounded-full lg:rounded-none border border-gray-200 lg:border-none" data-category="<?= $cat['name'] ?>" onclick="filterCategory('<?= $cat['name'] ?>', this)">
                    <div class="active-indicator absolute left-0 top-0 bottom-0 w-2 bg-green-600 hidden lg:block"></div>
                    <span class="category-text whitespace-nowrap"><?= $cat['name'] ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- MINUMAN -->
        <?php if(!empty($minuman)): ?>
        <div class="flex lg:block items-center mx-2 lg:mx-0 shrink-0">
            <h2 class="text-[13px] font-black text-gray-800 px-2 lg:px-4 mb-0 lg:mb-1 mt-0 lg:mt-4 uppercase flex items-center tracking-wider mr-2 lg:mr-0 ml-4 lg:ml-0 border-l border-gray-300 lg:border-none pl-4 lg:pl-4">
                <i class="fas fa-coffee text-green-600 mr-2 text-sm"></i> MINUMAN
            </h2>
            <ul class="flex flex-row lg:flex-col lg:mb-4 space-x-2 lg:space-x-0">
                <?php foreach($minuman as $cat): ?>
                <li class="category-item shrink-0 cursor-pointer text-[13px] text-gray-700 hover:bg-gray-200 transition py-1.5 lg:py-2 px-3 lg:px-4 lg:pl-10 relative rounded-full lg:rounded-none border border-gray-200 lg:border-none" data-category="<?= $cat['name'] ?>" onclick="filterCategory('<?= $cat['name'] ?>', this)">
                    <div class="active-indicator absolute left-0 top-0 bottom-0 w-2 bg-green-600 hidden lg:block"></div>
                    <span class="category-text whitespace-nowrap"><?= $cat['name'] ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="mt-auto p-4 border-t lg:border-l border-gray-200 bg-white min-w-[150px] lg:min-w-0 flex items-center justify-center">
        <button onclick="filterCategory('all', null)" class="w-full text-center py-2.5 rounded text-sm text-green-700 font-bold bg-green-50 hover:bg-green-100 transition border border-green-200">
            Tampilkan Semua
        </button>
    </div>
</aside>

<!-- Main Menu Area -->
<main id="tour-menu" class="flex-1 p-4 lg:p-6 overflow-y-auto bg-gray-50 relative min-h-[50vh] lg:min-h-0">
    <div id="menu-container" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 pb-24">
        <!-- Rendered by JS -->
    </div>
</main>

<!-- Cart Sidebar -->
<aside id="tour-cart" class="w-full lg:w-96 bg-white flex flex-col lg:border-l border-t lg:border-t-0 border-gray-200 shadow-[-4px_0_15px_rgba(0,0,0,0.02)] h-auto lg:h-full">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white">
        <h3 class="font-bold text-gray-800 text-lg">Keranjang Pesanan</h3>
        <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded-full" id="cart-count">0 item</span>
    </div>
    
    <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50" id="cart-items">
        <!-- Cart Items -->
        <div class="h-full flex flex-col items-center justify-center text-gray-400 space-y-2">
            <i class="fas fa-shopping-basket text-4xl mb-2"></i>
            <p>Keranjang masih kosong</p>
        </div>
    </div>

    <!-- Cart Footer -->
    <div class="p-5 bg-white border-t border-gray-200 shadow-[0_-4px_15px_rgba(0,0,0,0.02)]">
        <div class="flex justify-between items-center mb-4">
            <span class="text-gray-600 font-medium">Total Tagihan</span>
            <span class="text-2xl font-bold text-gray-900" id="cart-total">Rp 0</span>
        </div>
        <button onclick="openPaymentModal()" id="btn-process" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-green-200" disabled>
            Proses Pembayaran
        </button>
    </div>
</aside>

<!-- Payment Modal -->
<div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-2xl w-[400px] shadow-2xl overflow-hidden transform transition-all">
        <div class="bg-green-600 p-4 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg">Konfirmasi Pembayaran</h3>
            <button onclick="closePaymentModal()" class="text-green-100 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6">
            <div class="mb-6 text-center">
                <p class="text-sm text-gray-500 mb-1">Total Tagihan</p>
                <p class="text-3xl font-bold text-gray-900" id="modal-total">Rp 0</p>
            </div>
            
            <div class="space-y-4">
                <label class="block">
                    <span class="text-gray-700 text-sm font-bold mb-2 block">Metode Pembayaran</span>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="Tunai" class="peer sr-only" checked>
                            <div class="rounded-lg border-2 border-gray-200 p-3 text-center peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50 transition">
                                <i class="fas fa-money-bill-wave text-green-600 mb-1"></i>
                                <div class="font-semibold text-gray-700">Tunai</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="Digital" class="peer sr-only">
                            <div class="rounded-lg border-2 border-gray-200 p-3 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 transition">
                                <i class="fas fa-qrcode text-blue-600 mb-1"></i>
                                <div class="font-semibold text-gray-700">QRIS/Digital</div>
                            </div>
                        </label>
                    </div>
                </label>
            </div>

            <button onclick="submitOrder()" id="btn-submit-order" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl mt-6 transition-colors flex items-center justify-center">
                <span id="submit-text">Selesaikan Pesanan</span>
                <i class="fas fa-spinner fa-spin hidden ml-2" id="submit-spinner"></i>
            </button>
        </div>
    </div>
</div>

<script>
const menus = <?= $menuJson ?>;
let cart = [];
let currentFilter = 'all';

function formatPriceK(number) {
    return (number / 1000) + 'k';
}

function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
}

function filterCategory(cat, element) {
    currentFilter = cat;
    
    // Reset all items
    document.querySelectorAll('.category-item').forEach(el => {
        el.classList.remove('bg-[#e0e0e0]', 'text-green-600', 'font-semibold');
        el.classList.add('text-gray-700', 'hover:bg-gray-200');
        el.querySelector('.active-indicator').classList.add('hidden');
    });
    
    // Set active item
    if (cat !== 'all' && element) {
        element.classList.add('bg-[#e0e0e0]', 'text-green-600', 'font-semibold');
        element.classList.remove('text-gray-700', 'hover:bg-gray-200');
        element.querySelector('.active-indicator').classList.remove('hidden');
    }
    
    renderMenus();
}

function renderMenus() {
    const container = document.getElementById('menu-container');
    container.innerHTML = '';
    
    const filteredMenus = currentFilter === 'all' ? menus : menus.filter(m => m.category_name === currentFilter);
    
    filteredMenus.forEach(menu => {
        const imagePath = `assets/images/${menu.image}`;
        
        container.innerHTML += `
            <div class="bg-white p-4 rounded-2xl shadow-[0_2px_10px_rgba(0,0,0,0.04)] text-center cursor-pointer transition-all border-2 border-transparent hover:border-blue-500 focus:border-blue-500 active:border-blue-500 group flex flex-col justify-between" onclick="addToCart(${menu.id})">
                <div class="flex justify-center mb-3 h-28 items-center">
                    <img src="${imagePath}" onerror="this.src='assets/images/placeholder.png'" alt="${menu.name}" class="max-h-full object-contain group-hover:scale-105 transition-transform duration-300">
                </div>
                <div>
                    <h3 class="text-[15px] font-bold text-[#1e1b4b] leading-tight mb-1 line-clamp-2">${menu.name}</h3>
                    <p class="text-gray-500 text-[13px]">${formatRupiah(menu.price)}</p>
                </div>
            </div>
        `;
    });
}

function addToCart(menuId) {
    const menu = menus.find(m => m.id == menuId);
    if (!menu) return;

    const existingItem = cart.find(item => item.menu_id == menuId && (item.notes || '') === '');
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            menu_id: menu.id,
            name: menu.name,
            price: menu.price,
            image: menu.image,
            quantity: 1,
            notes: ''
        });
    }
    renderCart();
}

function updateQuantity(index, delta) {
    cart[index].quantity += delta;
    if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
    }
    renderCart();
}

function updateNotes(index, notes) {
    cart[index].notes = notes;
}

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
}

function renderCart() {
    const cartContainer = document.getElementById('cart-items');
    const cartCount = document.getElementById('cart-count');
    const cartTotal = document.getElementById('cart-total');
    const btnProcess = document.getElementById('btn-process');

    if (cart.length === 0) {
        cartContainer.innerHTML = `
            <div class="h-full flex flex-col items-center justify-center text-gray-400 space-y-2">
                <i class="fas fa-shopping-basket text-4xl mb-2 text-gray-300"></i>
                <p>Keranjang kosong</p>
            </div>`;
        cartCount.textContent = '0 item';
        cartTotal.textContent = 'Rp 0';
        btnProcess.disabled = true;
        return;
    }

    cartContainer.innerHTML = '';
    let totalItems = 0;
    let totalPrice = 0;

    cart.forEach((item, index) => {
        totalItems += item.quantity;
        totalPrice += (item.price * item.quantity);
        const imagePath = `assets/images/${item.image}`;

        cartContainer.innerHTML += `
            <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 flex flex-col animate-fade-in">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center space-x-3">
                        <img src="${imagePath}" onerror="this.src='assets/images/placeholder.png'" class="w-12 h-12 object-cover rounded-lg border border-gray-100">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 leading-tight">${item.name}</h4>
                            <p class="text-xs text-green-600 font-semibold">${formatRupiah(item.price)}</p>
                        </div>
                    </div>
                    <button onclick="removeFromCart(${index})" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <input type="text" placeholder="Catatan (Opsional)" value="${item.notes}" onchange="updateNotes(${index}, this.value)" class="text-xs w-full bg-gray-50 border border-gray-200 rounded p-1.5 mb-2 focus:outline-none focus:border-green-400 transition-colors">
                
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-gray-800">${formatRupiah(item.price * item.quantity)}</span>
                    <div class="flex items-center space-x-1 bg-gray-50 rounded-lg p-0.5 border border-gray-200">
                        <button onclick="updateQuantity(${index}, -1)" class="w-6 h-6 flex items-center justify-center bg-white text-gray-600 rounded shadow-sm hover:bg-gray-100 transition-colors text-xs font-bold">-</button>
                        <span class="w-6 text-center text-sm font-semibold">${item.quantity}</span>
                        <button onclick="updateQuantity(${index}, 1)" class="w-6 h-6 flex items-center justify-center bg-green-100 text-green-700 rounded shadow-sm hover:bg-green-200 transition-colors text-xs font-bold">+</button>
                    </div>
                </div>
            </div>
        `;
    });

    cartCount.textContent = `${totalItems} item`;
    cartTotal.textContent = formatRupiah(totalPrice);
    document.getElementById('modal-total').textContent = formatRupiah(totalPrice);
    btnProcess.disabled = false;
}

function openPaymentModal() {
    if (cart.length > 0) {
        document.getElementById('payment-modal').classList.remove('hidden');
    }
}

function closePaymentModal() {
    document.getElementById('payment-modal').classList.add('hidden');
}

async function submitOrder() {
    if (cart.length === 0) return;

    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    const btn = document.getElementById('btn-submit-order');
    const spinner = document.getElementById('submit-spinner');
    const text = document.getElementById('submit-text');
    
    // Calculate total
    const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    const payload = {
        total_amount: totalAmount,
        payment_method: paymentMethod,
        items: cart
    };

    btn.disabled = true;
    spinner.classList.remove('hidden');
    text.textContent = 'Memproses...';

    try {
        const response = await fetch('controllers/process_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            alert('Pesanan berhasil dibuat! No Order: ' + result.order_no);
            cart = [];
            renderCart();
            closePaymentModal();
        } else {
            alert('Gagal memproses pesanan: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan jaringan.');
    } finally {
        btn.disabled = false;
        spinner.classList.add('hidden');
        text.textContent = 'Selesaikan Pesanan';
    }
}

// Custom style for fade-in
document.head.insertAdjacentHTML('beforeend', '<style>@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } } .animate-fade-in { animation: fadeIn 0.2s ease-out forwards; }</style>');

// Initial render
renderMenus();

// --- Interactive Tour (Driver.js) ---
const driver = window.driver.js.driver;
const driverObj = driver({
  showProgress: true,
  animate: true,
  nextBtnText: 'Lanjut',
  prevBtnText: 'Kembali',
  doneBtnText: 'Selesai',
  steps: [
    { popover: { title: 'Selamat Datang!', description: 'Mari ikuti panduan singkat untuk menggunakan fitur kasir Goofycafe POS.' } },
    { element: '#main-header', popover: { title: 'Navigasi Utama', description: 'Di sini Anda dapat berpindah antara halaman Kasir, Riwayat Transaksi, dan Laporan Penjualan.', side: "bottom", align: 'start' } },
    { element: '#tour-category', popover: { title: 'Kategori Menu', description: 'Gunakan panel ini untuk memfilter menu berdasarkan kategori makanan atau minuman.', side: "right", align: 'start' } },
    { element: '#tour-menu', popover: { title: 'Daftar Menu', description: 'Klik pada menu yang diinginkan untuk menambahkannya ke dalam keranjang pesanan.', side: "top", align: 'start' } },
    { element: '#tour-cart', popover: { title: 'Keranjang Pesanan', description: 'Semua pesanan yang dipilih akan masuk ke sini. Anda bisa mengubah jumlah pesanan atau menambahkan catatan khusus.', side: "left", align: 'start' } },
    { element: '#btn-process', popover: { title: 'Proses Pembayaran', description: 'Jika pesanan sudah sesuai, klik tombol ini untuk memilih metode pembayaran dan menyelesaikan transaksi.', side: "top", align: 'center' } }
  ]
});

function startTour() {
    driverObj.drive();
}

// Auto-start tour on first visit
document.addEventListener('DOMContentLoaded', () => {
    if (!localStorage.getItem('goofycafe_tour_done')) {
        setTimeout(() => {
            startTour();
            localStorage.setItem('goofycafe_tour_done', 'true');
        }, 500);
    }
});
</script>
