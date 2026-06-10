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

$judul = mysqli_real_escape_string($conn, $data['judul']);
$versi = mysqli_real_escape_string($conn, $data['versi']);
$tanggal = mysqli_real_escape_string($conn, $data['tanggal_berlaku']);
$isi = mysqli_real_escape_string($conn, $data['isi']);
$status = mysqli_real_escape_string($conn, $data['status']);

$query = "INSERT INTO syarat_ketentuan (judul, versi, tanggal_berlaku, isi, status, ditetapkan_oleh) 
          VALUES ('$judul', '$versi', '$tanggal', '$isi', '$status', 1)";

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'message' => 'Syarat & ketentuan berhasil ditambahkan']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal: ' . mysqli_error($conn)]);
}
?>