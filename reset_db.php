<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $db->exec("TRUNCATE TABLE menus;");
    $db->exec("TRUNCATE TABLE categories;");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // Insert categories
    $db->exec("INSERT INTO categories (name, type) VALUES 
        ('Coffee', 'minuman'),
        ('Non Coffee', 'minuman'),
        ('Food', 'makanan');");

    // Insert menus with new image names
    $db->exec("INSERT INTO menus (category_id, name, price, image) VALUES
        (1, 'Espresso', 10000, 'espresso.png'),
        (1, 'Americano', 12000, 'americano.png'),
        (1, 'Kopi Cream', 15000, 'kopi_cream.png'),
        (1, 'Vietnam Drip', 15000, 'vietnam_drip.png'),
        (1, 'Mocachino', 20000, 'mocachino.png'),
        (1, 'Milo Coffee Cream', 20000, 'milo_coffee.png'),
        (2, 'Tea Shake (Blackcurrant / leci)', 15000, 'es_teh.png'),
        (2, 'Milo Cream', 18000, 'milo_cream.png'),
        (2, 'Susu Coklat', 18000, 'susu_coklat.png'),
        (2, 'Josua', 8000, 'josua.png'),
        (2, 'All Sachet', 8000, 'all_sachet.png'),
        (3, 'Mie Goreng/Kuah (Mie,Telur,Nasi)', 15000, 'nasgor_biasa.png'),
        (3, 'Kentang Goreng', 20000, 'kentang_goreng.png');");

    echo "Database reset successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
