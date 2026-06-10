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

$id_sk = intval($data['id_sk']);

if (isset($data['status']) && $data['status'] === null) {
    // Toggle status
    $query = "UPDATE syarat_ketentuan SET status = IF(status = 'aktif', 'nonaktif', 'aktif') WHERE id_sk = $id_sk";
    $message = 'Status berhasil diubah';
} else {
    // Update full data
    $judul = mysqli_real_escape_string($conn, $data['judul']);
    $versi = mysqli_real_escape_string($conn, $data['versi']);
    $tanggal = mysqli_real_escape_string($conn, $data['tanggal_berlaku']);
    $isi = mysqli_real_escape_string($conn, $data['isi']);
    $status = mysqli_real_escape_string($conn, $data['status']);
    $query = "UPDATE syarat_ketentuan SET judul='$judul', versi='$versi', tanggal_berlaku='$tanggal', isi='$isi', status='$status' WHERE id_sk = $id_sk";
    $message = 'Syarat & ketentuan berhasil diupdate';
}

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'message' => $message]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal: ' . mysqli_error($conn)]);
}
?>