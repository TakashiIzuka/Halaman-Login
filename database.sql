CREATE DATABASE IF NOT EXISTS registrasi_db
USE registrasi_db;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    jenis_kelamin ENUM('laki-laki', 'perempuan') NOT NULL,
    bday DATE,
    agama VARCHAR(50),
    biografi TEXT,
    filecode_path VARCHAR(255),
    filephoto_path VARCHAR(255),
    favcolor VARCHAR(7),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);