<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

$query = "SELECT pk.id_pendaftaran, p.nim, p.nama_lengkap, p.prodi, p.no_hp, pk.tanggal_daftar, pk.status_pendaftaran, pk.catatan_revisi
          FROM pendaftaran pk
          JOIN pendaftar p ON pk.id_pendaftar = p.id_pendaftar
          WHERE pk.status_pendaftaran IN ('menunggu_verifikasi', 'diterima', 'ditolak')
          ORDER BY pk.tanggal_daftar DESC";

$result = mysqli_query($conn, $query);

$pendaftaran = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pendaftaran[] = $row;
}

echo json_encode(['success' => true, 'data' => $pendaftaran]);
?>