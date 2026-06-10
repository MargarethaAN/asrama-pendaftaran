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
            p.prodi,
            p.fakultas,
            p.no_hp,
            p.alamat_asal,
            p.alamat_semarang,
            p.status_keluar,
            p.akun_diblokir,
            p.tanggal_masuk_asrama,
            p.tanggal_keluar_asrama,
            pk.status_pendaftaran,
            pk.tanggal_daftar,
            k.nomor_kamar,
            h.status_hunian
          FROM pendaftar p
          LEFT JOIN pendaftaran pk ON p.id_pendaftar = pk.id_pendaftar
          LEFT JOIN hunian h ON p.id_pendaftar = h.id_pendaftar
          LEFT JOIN kamar k ON h.id_kamar = k.id_kamar
          ORDER BY pk.tanggal_daftar DESC";

$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode(['success' => true, 'data' => $data]);
?>