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
$nama_penghuni = mysqli_real_escape_string($conn, $data['nama_penghuni']);

// Dapatkan id_pendaftar dari nama
$queryPendaftar = "SELECT id_pendaftar FROM pendaftar WHERE nama_lengkap = '$nama_penghuni'";
$result = mysqli_query($conn, $queryPendaftar);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $id_pendaftar = $row['id_pendaftar'];
    
    // Update hunian
    $query = "UPDATE hunian SET id_kamar = NULL, status_hunian = 'aktif' WHERE id_pendaftar = $id_pendaftar";
    mysqli_query($conn, $query);
    
    // Update status kamar menjadi tersedia
    mysqli_query($conn, "UPDATE kamar SET status_kamar = 'tersedia' WHERE id_kamar = $id_kamar");
    
    echo json_encode(['success' => true, 'message' => 'Penghuni berhasil dihapus dari kamar']);
} else {
    echo json_encode(['success' => false, 'message' => 'Penghuni tidak ditemukan']);
}
?>