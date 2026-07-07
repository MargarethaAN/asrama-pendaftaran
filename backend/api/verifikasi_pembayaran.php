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

$id_pembayaran = intval($data['id_pembayaran']);
$status = mysqli_real_escape_string($conn, $data['status']);

$query = "UPDATE pembayaran SET 
          status_pembayaran = '$status', 
          verifikasi_oleh = (SELECT id_admin FROM admin WHERE id_pengguna = " . $_SESSION['id_pengguna'] . "),
          verifikasi_pada = NOW() 
          WHERE id_pembayaran = $id_pembayaran";

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'message' => 'Verifikasi pembayaran berhasil']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal: ' . mysqli_error($conn)]);
}
?>