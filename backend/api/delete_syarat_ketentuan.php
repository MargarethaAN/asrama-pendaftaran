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

$query = "DELETE FROM syarat_ketentuan WHERE id_sk = $id_sk";

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'message' => 'Syarat & ketentuan berhasil dihapus']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal: ' . mysqli_error($conn)]);
}
?>