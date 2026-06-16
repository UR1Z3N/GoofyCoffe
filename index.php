<?php
// Main Router & Layout
$page = isset($_GET['page']) ? $_GET['page'] : 'pos';

// Basic routing
$allowed_pages = ['pos', 'history', 'report', 'faq'];
if (!in_array($page, $allowed_pages)) {
    $page = 'pos';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goofycafe POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/driver.css"/>
    <script src="assets/js/driver.js"></script>
    <style>
        .menu-image-container {
            width: 100%;
            padding-top: 100%;
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
            background-color: #f3f4f6;
        }
        .menu-image-container img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #888; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
</head>
<body class="bg-gray-50 h-screen flex flex-col font-sans">

    <!-- Header -->
    <header id="main-header" class="bg-green-700 text-white p-4 flex flex-col md:flex-row items-center justify-between shadow-md z-10 relative">
        <div class="flex items-center space-x-3 mb-3 md:mb-0">
            <!-- Tempat untuk Logo Brand (Ikon) -->
            <div class="relative w-12 h-10 flex items-center">
                <!-- Sesuaikan h-... dan -top-... jika ikon brand ingin lebih besar/kecil -->
                <img src="assets/images/brand_logo.png" alt="Brand Logo" class="absolute -left-0 -top-1 h-50 w-auto object-contain">
            </div>
            
            <!-- Tempat untuk Logo Tulisan (Teks) -->
            <div class="relative w-40 h-10 flex items-center">
                <img src="assets/images/logo.png" alt="Text Logo" class="absolute left-0 -top-16 h-40 w-auto object-contain">
            </div>
        </div>
        <nav class="flex flex-wrap justify-center gap-2 mt-2 md:mt-0">
            <a href="?page=pos" class="px-4 py-2 rounded-lg transition-colors <?= $page == 'pos' ? 'bg-green-800 font-bold' : 'hover:bg-green-600' ?>">
                <i class="fas fa-cash-register mr-2"></i>Kasir
            </a>
            <a href="?page=history" class="px-4 py-2 rounded-lg transition-colors <?= $page == 'history' ? 'bg-green-800 font-bold' : 'hover:bg-green-600' ?>">
                <i class="fas fa-history mr-2"></i>Riwayat
            </a>
            <a href="?page=report" class="px-4 py-2 rounded-lg transition-colors <?= $page == 'report' ? 'bg-green-800 font-bold' : 'hover:bg-green-600' ?>">
                <i class="fas fa-chart-line mr-2"></i>Laporan
            </a>
            <?php if ($page == 'pos'): ?>
            <button onclick="if(typeof startTour === 'function') startTour();" id="tour-help-btn" class="px-4 py-2 rounded-lg transition-colors hover:bg-green-600 border border-green-500 ml-2">
                <i class="fas fa-question-circle mr-2"></i>Panduan
            </button>
            <?php endif; ?>
            <a href="?page=faq" class="px-4 py-2 rounded-lg transition-colors <?= $page == 'faq' ? 'bg-green-800 font-bold' : 'hover:bg-green-600' ?> border border-green-500 ml-2">
                <i class="fas fa-info-circle mr-2"></i>FAQ
            </a>
        </nav>
    </header>

    <!-- Main Content Area -->
    <div class="flex flex-col lg:flex-row flex-1 overflow-y-auto lg:overflow-hidden">
        <?php include "views/{$page}/index.php"; ?>
    </div>

</body>
</html>