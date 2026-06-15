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

// Query ambil data pembayaran yang menunggu verifikasi
// Hapus kolom yang tidak ada di tabel
$query = "SELECT 
            pby.id_pembayaran,
            pby.nomor_va,
            pby.bank,
            pby.jumlah_tagihan,
            pby.bukti_pembayaran,
            pby.status_pembayaran,
            pby.tanggal_bayar,
            pf.nim,
            pf.nama_lengkap,
            pf.no_hp
          FROM pembayaran pby
          LEFT JOIN hunian h ON pby.id_hunian = h.id_hunian
          LEFT JOIN pendaftaran pk ON h.id_pendaftaran = pk.id_pendaftaran
          LEFT JOIN pendaftar pf ON pk.id_pendaftar = pf.id_pendaftar
          WHERE pby.status_pembayaran = 'menunggu'
          ORDER BY pby.tanggal_bayar DESC";

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