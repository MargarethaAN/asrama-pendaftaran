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
            pby.id_pembayaran,
            pby.nomor_va,
            pby.bank,
            pby.jumlah_tagihan,
            pby.bukti_pembayaran,
            pby.status_pembayaran,
            pby.tanggal_bayar,
            pf.nim,
            pf.nama_lengkap,
            pf.no_hp,
            pby.no_rekening,
            pby.atas_nama,
            pby.catatan
          FROM pembayaran pby
          JOIN hunian h ON pby.id_hunian = h.id_hunian
          JOIN pendaftar pf ON h.id_pendaftar = pf.id_pendaftar
          WHERE pby.status_pembayaran = 'menunggu'
          ORDER BY pby.tanggal_bayar DESC";

$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode(['success' => true, 'data' => $data]);
?>