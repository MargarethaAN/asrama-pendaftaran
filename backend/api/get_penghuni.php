<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

$query = "SELECT p.id_pendaftar, p.nim, p.nama_lengkap, p.prodi, p.tanggal_masuk_asrama, p.status_keluar, p.akun_diblokir,
          k.nomor_kamar, k.lantai, h.status_hunian
          FROM pendaftar p
          LEFT JOIN hunian h ON p.id_pendaftar = h.id_pendaftar
          LEFT JOIN kamar k ON h.id_kamar = k.id_kamar
          WHERE p.status_keluar = 'aktif'";

$result = mysqli_query($conn, $query);

$penghuni = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Hitung masa hunian
    if ($row['tanggal_masuk_asrama']) {
        $masuk = new DateTime($row['tanggal_masuk_asrama']);
        $sekarang = new DateTime();
        $diff = $masuk->diff($sekarang);
        $row['masa_hunian_tahun'] = $diff->y;
        $row['bisa_konfirmasi'] = ($diff->y >= 2);
    } else {
        $row['masa_hunian_tahun'] = 0;
        $row['bisa_konfirmasi'] = false;
    }
    $penghuni[] = $row;
}

echo json_encode(['success' => true, 'data' => $penghuni]);
?>