<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

// Query sederhana
$query = "SELECT 
            p.id_pendaftar,
            p.nim,
            p.nama_lengkap,
            p.prodi,
            p.status_keluar,
            pk.status_pendaftaran,
            pk.tanggal_daftar
          FROM pendaftar p
          LEFT JOIN pendaftaran pk ON p.id_pendaftar = pk.id_pendaftar
          ORDER BY pk.tanggal_daftar DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . mysqli_error($conn)]);
    exit;
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode(['success' => true, 'data' => $data]);
?>