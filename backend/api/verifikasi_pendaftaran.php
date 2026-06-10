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

$id_pendaftaran = mysqli_real_escape_string($conn, $data['id_pendaftaran']);
$status = mysqli_real_escape_string($conn, $data['status']);
$catatan = mysqli_real_escape_string($conn, $data['catatan']);

$query = "UPDATE pendaftaran SET 
          status_pendaftaran = '$status',
          catatan_revisi = '$catatan',
          verifikasi_oleh = " . $_SESSION['id_pengguna'] . ",
          verifikasi_pada = NOW()
          WHERE id_pendaftaran = $id_pendaftaran";

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'message' => 'Verifikasi berhasil']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal: ' . mysqli_error($conn)]);
}
?>