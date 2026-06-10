<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../config/database.php';

session_start();

if (!isset($_SESSION['id_pengguna'])) {
    echo json_encode(['success' => false, 'message' => 'Harap login']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$status = mysqli_real_escape_string($conn, $data['status'] ?? 'menunggu_verifikasi');

$id_pengguna = $_SESSION['id_pengguna'];

$query = "SELECT id_pendaftar FROM pendaftar WHERE id_pengguna = $id_pengguna";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $id_pendaftar = $row['id_pendaftar'];
    
    $check = "SELECT id_pendaftaran FROM pendaftaran WHERE id_pendaftar = $id_pendaftar";
    $checkResult = mysqli_query($conn, $check);
    
    if (mysqli_num_rows($checkResult) > 0) {
        $update = "UPDATE pendaftaran SET status_pendaftaran = '$status', tanggal_daftar = CURDATE() WHERE id_pendaftar = $id_pendaftar";
    } else {
        $update = "INSERT INTO pendaftaran (id_pendaftar, tanggal_daftar, status_pendaftaran) VALUES ($id_pendaftar, CURDATE(), '$status')";
    }
    
    if (mysqli_query($conn, $update)) {
        echo json_encode(['success' => true, 'message' => 'Status pendaftaran berhasil diupdate']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Data pendaftar tidak ditemukan']);
}
?>