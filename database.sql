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
('Spesial Nasi Goreng', 'makanan'),
('Spesial Magelangan', 'makanan'),
('Susu', 'minuman'),
('Kopi', 'minuman');

-- Insert dummy menus
INSERT INTO menus (category_id, name, price, image) VALUES
(1, 'Nasgor Biasa', 10000, 'nasgor_biasa.png'),
(1, 'Nasgor Ati', 13000, 'nasgor_ati.png'),
(1, 'Nasgor Ayam', 13000, 'nasgor_ayam.png'),
(2, 'Magelangan Biasa', 11000, 'placeholder.png'),
(3, 'Susu Sirup', 10000, 'susu_sirup.png'),
(3, 'Es Teh', 4000, 'es_teh.png'),
(4, 'Kopi Hitam', 5000, 'placeholder.png');
