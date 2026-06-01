CREATE DATABASE IF NOT EXISTS goofycafe_db;
USE goofycafe_db;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('makanan', 'minuman') NOT NULL
);

CREATE TABLE IF NOT EXISTS menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT 'placeholder.png',
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_no VARCHAR(50) NOT NULL UNIQUE,
    total_amount DECIMAL(12,2) NOT NULL,
    payment_method ENUM('Tunai', 'Digital') NOT NULL DEFAULT 'Tunai',
    status ENUM('Selesai', 'Dibatalkan') NOT NULL DEFAULT 'Selesai',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE
);

-- Insert dummy categories
INSERT INTO categories (name, type) VALUES 
('Coffee', 'minuman'),
('Non Coffee', 'minuman'),
('Food', 'makanan');

-- Insert dummy menus
INSERT INTO menus (category_id, name, price, image) VALUES
(1, 'Espresso', 10000, 'kopi_hitam.png'),
(1, 'Americano', 12000, 'kopi_hitam.png'),
(1, 'Kopi Cream', 15000, 'kopi_hitam.png'),
(1, 'Vietnam Drip', 15000, 'kopi_hitam.png'),
(1, 'Mocachino', 20000, 'kopi_hitam.png'),
(1, 'Milo Coffee Cream', 20000, 'kopi_hitam.png'),
(2, 'Tea Shake (Blackcurrant / leci)', 15000, 'es_teh.png'),
(2, 'Milo Cream', 18000, 'susu_sirup.png'),
(2, 'Susu Coklat', 18000, 'susu_sirup.png'),
(2, 'Josua', 8000, 'es_teh.png'),
(2, 'All Sachet', 8000, 'placeholder.png'),
(3, 'Mie Goreng/Kuah (Mie,Telur,Nasi)', 15000, 'nasgor_biasa.png'),
(3, 'Kentang Goreng', 20000, 'placeholder.png');
