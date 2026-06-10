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

$id_kamar = intval($data['id_kamar']);
$id_pendaftar = intval($data['id_pendaftar']);

// Cek apakah sudah ada hunian
$check = "SELECT id_hunian FROM hunian WHERE id_pendaftar = $id_pendaftar";
$result = mysqli_query($conn, $check);

if (mysqli_num_rows($result) > 0) {
    // Update hunian yang sudah ada
    $query = "UPDATE hunian SET id_kamar = $id_kamar, ditetapkan_pada = NOW() WHERE id_pendaftar = $id_pendaftar";
} else {
    // Buat hunian baru
    $query = "INSERT INTO hunian (id_pendaftar, id_kamar, tanggal_masuk, status_hunian, ditetapkan_pada) VALUES ($id_pendaftar, $id_kamar, CURDATE(), 'aktif', NOW())";
}

if (mysqli_query($conn, $query)) {
    // Update status kamar menjadi terisi
    mysqli_query($conn, "UPDATE kamar SET status_kamar = 'terisi' WHERE id_kamar = $id_kamar");
    echo json_encode(['success' => true, 'message' => 'Penghuni berhasil ditempatkan']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal: ' . mysqli_error($conn)]);
}
?>