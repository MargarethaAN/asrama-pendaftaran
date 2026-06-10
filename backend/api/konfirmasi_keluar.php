<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../config/database.php';

session_start();

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$id_pendaftar = mysqli_real_escape_string($conn, $data['id_pendaftar']);
$keterangan = mysqli_real_escape_string($conn, $data['keterangan'] ?? '');
$tanggal_sekarang = date('Y-m-d');

// Dapatkan id_admin
$queryAdmin = "SELECT id_admin FROM admin WHERE id_pengguna = " . $_SESSION['id_pengguna'];
$resultAdmin = mysqli_query($conn, $queryAdmin);
$admin = mysqli_fetch_assoc($resultAdmin);
$id_admin = $admin['id_admin'];

// Update data pendaftar
$updatePendaftar = "UPDATE pendaftar SET 
                     tanggal_keluar_asrama = '$tanggal_sekarang',
                     status_keluar = 'keluar',
                     akun_diblokir = 'ya'
                     WHERE id_pendaftar = $id_pendaftar";

// Simpan ke konfirmasi_keluar
$insertKonfirmasi = "INSERT INTO konfirmasi_keluar (id_pendaftar, id_admin, tanggal_konfirmasi, keterangan) 
                     VALUES ($id_pendaftar, $id_admin, '$tanggal_sekarang', '$keterangan')";

// Update status hunian
$updateHunian = "UPDATE hunian SET 
                  tanggal_keluar = '$tanggal_sekarang',
                  status_hunian = 'selesai'
                  WHERE id_pendaftar = $id_pendaftar AND status_hunian = 'aktif'";

if (mysqli_query($conn, $updatePendaftar)) {
    mysqli_query($conn, $insertKonfirmasi);
    mysqli_query($conn, $updateHunian);
    echo json_encode(['success' => true, 'message' => 'Konfirmasi keluar asrama berhasil, akun telah diblokir']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal: ' . mysqli_error($conn)]);
}
?>