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
$id_kamar = mysqli_real_escape_string($conn, $data['id_kamar']);

// Update hunian dengan kamar baru
$query = "UPDATE hunian SET id_kamar = $id_kamar, ditetapkan_pada = NOW() WHERE id_pendaftar = $id_pendaftar";

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'message' => 'Penghuni berhasil dipindahkan']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal: ' . mysqli_error($conn)]);
}
?>