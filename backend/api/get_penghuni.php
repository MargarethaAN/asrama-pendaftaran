<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

session_start();

// Cek login admin
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

// Perbaiki query: hunian terhubung melalui pendaftaran
$query = "SELECT 
            p.id_pendaftar,
            p.nim,
            p.nama_lengkap,
            p.prodi,
            p.tanggal_masuk_asrama,
            p.status_keluar,
            p.akun_diblokir,
            pk.status_pendaftaran,
            k.nomor_kamar,
            k.lantai,
            h.status_hunian,
            h.tanggal_masuk,
            h.tanggal_keluar
          FROM pendaftar p
          LEFT JOIN pendaftaran pk ON p.id_pendaftar = pk.id_pendaftar
          LEFT JOIN hunian h ON pk.id_pendaftaran = h.id_pendaftaran
          LEFT JOIN kamar k ON h.id_kamar = k.id_kamar
          ORDER BY p.nama_lengkap";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . mysqli_error($conn)]);
    exit;
}

$penghuni = [];
while ($row = mysqli_fetch_assoc($result)) {
    $penghuni[] = $row;
}

echo json_encode(['success' => true, 'data' => $penghuni]);
?>