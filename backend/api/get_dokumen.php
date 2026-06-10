<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

session_start();
if (!isset($_SESSION['id_pengguna'])) {
    echo json_encode(['success' => false, 'message' => 'Harap login']);
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];

// Dapatkan id_pendaftar
$queryPendaftar = "SELECT id_pendaftar FROM pendaftar WHERE id_pengguna = $id_pengguna";
$result = mysqli_query($conn, $queryPendaftar);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $id_pendaftar = $row['id_pendaftar'];
    
    $query = "SELECT * FROM dokumen WHERE id_pendaftar = $id_pendaftar ORDER BY tanggal_upload DESC";
    $resultDokumen = mysqli_query($conn, $query);
    
    $dokumen = [];
    while ($dok = mysqli_fetch_assoc($resultDokumen)) {
        $dokumen[] = $dok;
    }
    
    echo json_encode(['success' => true, 'data' => $dokumen]);
} else {
    echo json_encode(['success' => true, 'data' => []]);
}
?>