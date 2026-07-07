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

$id_kamar = mysqli_real_escape_string($conn, $data['id_kamar']);
$status_kamar = mysqli_real_escape_string($conn, $data['status_kamar']);

$query = "UPDATE kamar SET status_kamar = '$status_kamar' WHERE id_kamar = $id_kamar";

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'message' => 'Status kamar berhasil diupdate']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal update: ' . mysqli_error($conn)]);
}
?>