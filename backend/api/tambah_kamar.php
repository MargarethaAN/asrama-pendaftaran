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

$nomor_kamar = mysqli_real_escape_string($conn, $data['nomor_kamar']);
$lantai = intval($data['lantai']);
$kapasitas = intval($data['kapasitas']);
$status_kamar = mysqli_real_escape_string($conn, $data['status_kamar']);

$query = "INSERT INTO kamar (nomor_kamar, lantai, kapasitas, status_kamar) VALUES ('$nomor_kamar', $lantai, $kapasitas, '$status_kamar')";

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'message' => 'Kamar berhasil ditambahkan']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal: ' . mysqli_error($conn)]);
}
?>