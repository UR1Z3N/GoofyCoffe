<?php
// Main Router & Layout
$page = isset($_GET['page']) ? $_GET['page'] : 'pos';

// Basic routing
$allowed_pages = ['pos', 'history', 'report'];
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
    <header class="bg-green-700 text-white p-4 flex items-center justify-between shadow-md z-10 relative">
        <div class="flex items-center space-x-4">
            <i class="fas fa-coffee text-2xl"></i>
            <span class="font-bold text-xl tracking-wide">Goofycafe</span>
        </div>
        <nav class="flex space-x-2">
            <a href="?page=pos" class="px-4 py-2 rounded-lg transition-colors <?= $page == 'pos' ? 'bg-green-800 font-bold' : 'hover:bg-green-600' ?>">
                <i class="fas fa-cash-register mr-2"></i>Kasir
            </a>
            <a href="?page=history" class="px-4 py-2 rounded-lg transition-colors <?= $page == 'history' ? 'bg-green-800 font-bold' : 'hover:bg-green-600' ?>">
                <i class="fas fa-history mr-2"></i>Riwayat
            </a>
            <a href="?page=report" class="px-4 py-2 rounded-lg transition-colors <?= $page == 'report' ? 'bg-green-800 font-bold' : 'hover:bg-green-600' ?>">
                <i class="fas fa-chart-line mr-2"></i>Laporan
            </a>
        </nav>
    </header>

    <!-- Main Content Area -->
    <div class="flex flex-1 overflow-hidden">
        <?php include "views/{$page}/index.php"; ?>
    </div>

</body>
</html>