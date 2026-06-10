<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

session_start();

if (!isset($_SESSION['id_pengguna'])) {
    echo json_encode(['success' => false, 'message' => 'Harap login terlebih dahulu']);
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];

// Perbaiki query: hunian terhubung ke pendaftaran, bukan langsung ke pendaftar
$query = "SELECT 
            p.*, 
            d.*, 
            pk.status_pendaftaran, 
            pk.tanggal_daftar, 
            pk.verifikasi_pada,
            k.nomor_kamar, 
            h.status_hunian, 
            h.tanggal_masuk, 
            h.tanggal_keluar
          FROM pendaftar p 
          LEFT JOIN data_keluarga d ON p.id_pendaftar = d.id_pendaftar
          LEFT JOIN pendaftaran pk ON p.id_pendaftar = pk.id_pendaftar
          LEFT JOIN hunian h ON pk.id_pendaftaran = h.id_pendaftaran
          LEFT JOIN kamar k ON h.id_kamar = k.id_kamar
          WHERE p.id_pengguna = $id_pengguna
          ORDER BY pk.id_pendaftaran DESC LIMIT 1";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . mysqli_error($conn)]);
    exit;
}

if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => true, 'data' => null]);
}
?>