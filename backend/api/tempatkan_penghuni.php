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

// Dapatkan id_pendaftaran
$query = "SELECT id_pendaftaran FROM pendaftaran WHERE id_pendaftar = $id_pendaftar ORDER BY id_pendaftaran DESC LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $id_pendaftaran = $row['id_pendaftaran'];
    
    // Cek apakah sudah ada hunian
    $check = "SELECT id_hunian FROM hunian WHERE id_pendaftaran = $id_pendaftaran";
    $checkResult = mysqli_query($conn, $check);
    
    if (mysqli_num_rows($checkResult) > 0) {
        $update = "UPDATE hunian SET id_kamar = $id_kamar, ditetapkan_pada = NOW() WHERE id_pendaftaran = $id_pendaftaran";
        mysqli_query($conn, $update);
    } else {
        $insert = "INSERT INTO hunian (id_pendaftaran, id_kamar, tanggal_masuk, status_hunian, ditetapkan_pada) 
                   VALUES ($id_pendaftaran, $id_kamar, CURDATE(), 'aktif', NOW())";
        mysqli_query($conn, $insert);
    }
    
    // Update status kamar
    mysqli_query($conn, "UPDATE kamar SET status_kamar = 'terisi' WHERE id_kamar = $id_kamar");
    
    echo json_encode(['success' => true, 'message' => 'Penghuni berhasil ditempatkan']);
} else {
    echo json_encode(['success' => false, 'message' => 'Data pendaftaran tidak ditemukan']);
}
?>