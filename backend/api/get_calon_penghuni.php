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
            p.id_pendaftar,
            p.nim,
            p.nama_lengkap,
            p.prodi
          FROM pendaftar p
          INNER JOIN pendaftaran pk ON p.id_pendaftar = pk.id_pendaftar
          LEFT JOIN hunian h ON pk.id_pendaftaran = h.id_pendaftaran AND h.status_hunian = 'aktif'
          WHERE pk.status_pendaftaran = 'diterima'
            AND p.status_keluar = 'aktif'
            AND h.id_hunian IS NULL
          ORDER BY p.nama_lengkap";

$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode(['success' => true, 'data' => $data]);
?>