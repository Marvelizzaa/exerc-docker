CREATE DATABASE IF NOT EXISTS db_sederhanaok;
USE db_sederhanaok;

CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    kategori ENUM('makanan', 'minuman'),
    harga INT
);

CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelanggan VARCHAR(100),
    menu_id INT,
    jumlah INT
);

INSERT INTO menu (nama, kategori, harga) VALUES
('Rendang Daging', 'makanan', 22000),
('Ayam Pop', 'makanan', 20000),
('Dendeng Balado', 'makanan', 21000),
('Gulai Cincang', 'makanan', 23000),
('Telor Dadar', 'makanan', 12000),
('Es Teh Manis', 'minuman', 5000),
('Es Jeruk', 'minuman', 7000),
('Jus Alpukat', 'minuman', 12000),
('Teh Talua', 'minuman', 10000),
('Air Mineral', 'minuman', 4000);
