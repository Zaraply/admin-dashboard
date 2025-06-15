
CREATE TABLE handphone (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    brand VARCHAR(50),
    spesifikasi VARCHAR(100),
    grade CHAR(1),
    warna VARCHAR(100),
    jumlah INT,
    harga DECIMAL(15,2),
    status ENUM('Tersedia', 'Sedikit', 'Habis')
);
CREATE TABLE penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_handphone INT,
    jumlah INT,
    tanggal DATE
);
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255)
);
