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
$query_pendaftar = "SELECT id_pendaftar FROM pendaftar WHERE id_pengguna = $id_pengguna";
$result_pendaftar = mysqli_query($conn, $query_pendaftar);

if (!$result_pendaftar) {
    echo json_encode(['success' => false, 'message' => 'Error query: ' . mysqli_error($conn)]);
    exit;
}

if (mysqli_num_rows($result_pendaftar) > 0) {
    $row = mysqli_fetch_assoc($result_pendaftar);
    $id_pendaftar = $row['id_pendaftar'];
    
    $query = "SELECT * FROM dokumen WHERE id_pendaftar = $id_pendaftar ORDER BY tanggal_upload DESC";
    $result = mysqli_query($conn, $query);
    
    $dokumen = [];
    while ($dok = mysqli_fetch_assoc($result)) {
        $dokumen[] = $dok;
    }
    
    echo json_encode(['success' => true, 'data' => $dokumen]);
} else {
    echo json_encode(['success' => true, 'data' => []]);
}
?>