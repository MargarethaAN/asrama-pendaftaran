-- Membuat database
CREATE DATABASE IF NOT EXISTS asrama_pendaftaran;
USE asrama_pendaftaran;

-- Tabel pengguna
CREATE TABLE pengguna (
    id_pengguna INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('pendaftar', 'admin') DEFAULT 'pendaftar',
    create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel pendaftar
CREATE TABLE pendaftar (
    id_pendaftar INT PRIMARY KEY AUTO_INCREMENT,
    id_pengguna INT NOT NULL,
    nim VARCHAR(20) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    nama_panggilan VARCHAR(50),
    prodi VARCHAR(100),
    fakultas VARCHAR(100),
    no_hp VARCHAR(15),
    alamat_asal TEXT,
    alamat_semarang TEXT,
    agama VARCHAR(20),
    asal_sekolah VARCHAR(100),
    tempat_lahir VARCHAR(50),
    tanggal_lahir DATE,
    sifat_positif TEXT,
    sifat_negatif TEXT,
    bakat VARCHAR(100),
    alasan_masuk_asrama TEXT,
    tanggal_masuk_asrama DATE,
    tanggal_keluar_asrama DATE,
    status_keluar ENUM('aktif', 'keluar') DEFAULT 'aktif',
    akun_diblokir ENUM('tidak', 'ya') DEFAULT 'tidak',
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE
);

-- Tabel data_keluarga
CREATE TABLE data_keluarga (
    id_keluarga INT PRIMARY KEY AUTO_INCREMENT,
    id_pendaftar INT NOT NULL,
    nama_ayah VARCHAR(100),
    pekerjaan_ayah VARCHAR(100),
    no_hp_ayah VARCHAR(15),
    nama_ibu VARCHAR(100),
    pekerjaan_ibu VARCHAR(100),
    no_hp_ibu VARCHAR(15),
    jumlah_saudara INT DEFAULT 0,
    FOREIGN KEY (id_pendaftar) REFERENCES pendaftar(id_pendaftar) ON DELETE CASCADE
);

-- Tabel kamar
CREATE TABLE kamar (
    id_kamar INT PRIMARY KEY AUTO_INCREMENT,
    nomor_kamar VARCHAR(10) UNIQUE NOT NULL,
    lantai INT,
    kapasitas INT DEFAULT 2,
    status_kamar ENUM('tersedia', 'terisi', 'perbaikan') DEFAULT 'tersedia'
);

-- Tabel pendaftaran
CREATE TABLE pendaftaran (
    id_pendaftaran INT PRIMARY KEY AUTO_INCREMENT,
    id_pendaftar INT NOT NULL,
    tanggal_daftar DATE,
    status_pendaftaran ENUM('draf', 'menunggu_verifikasi', 'diterima', 'ditolak') DEFAULT 'draf',
    catatan_revisi TEXT,
    verifikasi_oleh INT,
    verifikasi_pada DATETIME,
    FOREIGN KEY (id_pendaftar) REFERENCES pendaftar(id_pendaftar)
);

-- Tabel hunian
CREATE TABLE hunian (
    id_hunian INT PRIMARY KEY AUTO_INCREMENT,
    id_pendaftaran INT NOT NULL,
    id_kamar INT,
    tanggal_masuk DATE,
    tanggal_keluar DATE,
    status_hunian ENUM('aktif', 'selesai') DEFAULT 'aktif',
    ditetapkan_oleh INT,
    ditetapkan_pada DATETIME,
    FOREIGN KEY (id_pendaftaran) REFERENCES pendaftaran(id_pendaftaran),
    FOREIGN KEY (id_kamar) REFERENCES kamar(id_kamar)
);

-- Tabel pembayaran
CREATE TABLE pembayaran (
    id_pembayaran INT PRIMARY KEY AUTO_INCREMENT,
    id_hunian INT NOT NULL,
    nomor_va VARCHAR(20) UNIQUE,
    bank VARCHAR(50),
    jumlah_tagihan DECIMAL(12,2),
    tanggal_batas_bayar DATETIME,
    tanggal_bayar DATETIME,
    bukti_pembayaran VARCHAR(255),
    status_pembayaran ENUM('belum_bayar', 'menunggu', 'lunas', 'ditolak') DEFAULT 'belum_bayar',
    verifikasi_oleh INT,
    verifikasi_pada DATETIME,
    FOREIGN KEY (id_hunian) REFERENCES hunian(id_hunian)
);

-- Tabel dokumen
CREATE TABLE dokumen (
    id_dokumen INT PRIMARY KEY AUTO_INCREMENT,
    id_pendaftar INT NOT NULL,
    jenis_dokumen VARCHAR(50),
    file_path VARCHAR(255),
    tanggal_upload DATE,
    status_verifikasi ENUM('menunggu', 'diverifikasi', 'ditolak') DEFAULT 'menunggu',
    FOREIGN KEY (id_pendaftar) REFERENCES pendaftar(id_pendaftar)
);

-- Tabel admin
CREATE TABLE admin (
    id_admin INT PRIMARY KEY AUTO_INCREMENT,
    id_pengguna INT NOT NULL,
    nama_admin VARCHAR(100),
    jabatan VARCHAR(100),
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna)
);

-- Tabel syarat_ketentuan
CREATE TABLE syarat_ketentuan (
    id_sk INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(255),
    isi TEXT,
    versi VARCHAR(10),
    tanggal_berlaku DATE,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    ditetapkan_oleh INT
);

-- Tabel konfirmasi_keluar
CREATE TABLE konfirmasi_keluar (
    id_konfirmasi INT PRIMARY KEY AUTO_INCREMENT,
    id_pendaftar INT NOT NULL,
    id_admin INT NOT NULL,
    tanggal_konfirmasi DATE,
    waktu_konfirmasi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    keterangan TEXT,
    FOREIGN KEY (id_pendaftar) REFERENCES pendaftar(id_pendaftar),
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin)
);

-- Insert data awal
INSERT INTO pengguna (email, password, nama, role) VALUES
('admin@asrama.com', MD5('admin123'), 'Administrator', 'admin'),
('mahasiswa1@student.unika.ac.id', MD5('password123'), 'Maria Angelina', 'pendaftar');

INSERT INTO admin (id_pengguna, nama_admin, jabatan) VALUES
(1, 'Suster Asisten', 'Pengelola Asrama');

INSERT INTO kamar (nomor_kamar, lantai, kapasitas, status_kamar) VALUES
('101', 1, 2, 'tersedia'),
('102', 1, 2, 'tersedia'),
('103', 1, 2, 'terisi'),
('104', 1, 2, 'tersedia'),
('105', 1, 2, 'tersedia'),
('201', 2, 2, 'tersedia'),
('202', 2, 2, 'tersedia'),
('203', 2, 2, 'perbaikan'),
('204', 2, 2, 'tersedia'),
('205', 2, 2, 'tersedia'),
('301', 3, 2, 'tersedia'),
('302', 3, 2, 'tersedia'),
('303', 3, 2, 'terisi'),
('304', 3, 2, 'tersedia'),
('305', 3, 2, 'tersedia');

INSERT INTO syarat_ketentuan (judul, isi, versi, tanggal_berlaku, status) VALUES
('Syarat dan Ketentuan Pendaftaran Asrama Putri', 
'1. Mahasiswi aktif Universitas Katolik Soegijapranata\n2. Belum menikah\n3. Memiliki IPK minimal 2.75\n4. Melampirkan surat rekomendasi dari orang tua\n5. Bersedia mengikuti peraturan asrama\n6. Membayar biaya pendaftaran sesuai ketentuan\n7. Mengisi data dengan jujur dan benar\n8. Menyetujui seluruh kebijakan asrama', 
'v1.0', '2026-01-01', 'aktif');

-- Insert test pendaftar dengan tanggal masuk 2 tahun lalu (untuk test konfirmasi keluar)
INSERT INTO pendaftar (id_pengguna, nim, nama_lengkap, nama_panggilan, prodi, fakultas, no_hp, alamat_asal, alamat_semarang, agama, asal_sekolah, tempat_lahir, tanggal_lahir, alasan_masuk_asrama, tanggal_masuk_asrama, status_keluar, akun_diblokir) VALUES
(2, '23K10020', 'Maria Angelina', 'Angel', 'Teknik Informatika', 'Ilmu Komputer', '081234567890', 'Jl. Merdeka No.1, Jakarta', 'Jl. Setiabudi No.2, Semarang', 'Katolik', 'SMA Santo Yosef', 'Jakarta', '2005-05-15', 'Ingin fokus belajar dan dekat kampus', '2024-01-15', 'aktif', 'tidak');

-- Tambah kolom di tabel pendaftar jika belum ada
ALTER TABLE pendaftar ADD COLUMN IF NOT EXISTS foto_profil VARCHAR(255) DEFAULT NULL;

-- Tambah data admin
INSERT INTO pengguna (email, password, nama, role) VALUES 
('admin@asrama.com', MD5('admin123'), 'Administrator', 'admin')
ON DUPLICATE KEY UPDATE id_pengguna=id_pengguna;

-- Tambah data pendaftar demo
INSERT INTO pengguna (email, password, nama, role) VALUES 
('mahasiswa1@student.unika.ac.id', MD5('password123'), 'Maria Angelina', 'pendaftar')
ON DUPLICATE KEY UPDATE id_pengguna=id_pengguna;

-- Pastikan admin terdaftar di tabel admin
INSERT INTO admin (id_pengguna, nama_admin, jabatan) 
SELECT id_pengguna, nama, 'Pengelola Asrama' FROM pengguna WHERE role = 'admin'
ON DUPLICATE KEY UPDATE id_admin=id_admin;