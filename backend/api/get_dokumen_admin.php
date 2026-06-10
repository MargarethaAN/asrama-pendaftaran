<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

session_start();

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

$id_pendaftar = isset($_GET['id_pendaftar']) ? intval($_GET['id_pendaftar']) : 0;

if ($id_pendaftar <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID pendaftar tidak valid']);
    exit;
}

$query = "SELECT * FROM dokumen WHERE id_pendaftar = $id_pendaftar ORDER BY tanggal_upload DESC";
$result = mysqli_query($conn, $query);

$dokumen = [];
while ($row = mysqli_fetch_assoc($result)) {
    $dokumen[] = $row;
}

echo json_encode(['success' => true, 'data' => $dokumen]);
?>