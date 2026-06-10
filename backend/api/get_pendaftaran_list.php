<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

session_start();

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

$query = "SELECT 
            pk.id_pendaftaran,
            pf.id_pendaftar,
            pf.nim,
            pf.nama_lengkap,
            pf.prodi,
            pk.tanggal_daftar,
            pk.status_pendaftaran
          FROM pendaftaran pk
          JOIN pendaftar pf ON pk.id_pendaftar = pf.id_pendaftar
          WHERE pk.status_pendaftaran = 'menunggu_verifikasi'
          ORDER BY pk.tanggal_daftar DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($conn)]);
    exit;
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode(['success' => true, 'data' => $data]);
?>