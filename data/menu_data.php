<?php
// Tentukan path folder gambar menu agar mudah diubah nanti
define('MENU_IMAGE_PATH', 'assets/images/');

$menuData = [
    'makanan' => [
        [
            'id' => 1,
            'name' => 'Nasgor Biasa',
            'price' => 10000,
            'category' => 'Spesial Nasi Goreng',
            'image' => 'nasgor_biasa.png'
        ],
        [
            'id' => 2,
            'name' => 'Nasgor Ati',
            'price' => 13000,
            'category' => 'Spesial Nasi Goreng',
            'image' => 'nasgor_ati.png'
        ],
        [
            'id' => 3,
            'name' => 'Nasgor Ayam',
            'price' => 13000,
            'category' => 'Spesial Nasi Goreng',
            'image' => 'nasgor_ayam.png'
        ],
    ],
    'minuman' => [
        [
            'id' => 101,
            'name' => 'Susu Sirup',
            'price' => 10000,
            'category' => 'Susu',
            'image' => 'susu_sirup.png'
        ],
        [
            'id' => 102,
            'name' => 'Es Teh',
            'price' => 13000,
            'category' => 'Susu',
            'image' => 'es_teh.png'
        ],
    ]
];
?>