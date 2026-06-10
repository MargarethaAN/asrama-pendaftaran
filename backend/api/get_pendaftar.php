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

$query = "SELECT p.*, d.*, pk.* FROM pendaftar p 
          LEFT JOIN data_keluarga d ON p.id_pendaftar = d.id_pendaftar
          LEFT JOIN pendaftaran pk ON p.id_pendaftar = pk.id_pendaftar
          WHERE p.id_pengguna = $id_pengguna";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => true, 'data' => null]);
}
?>