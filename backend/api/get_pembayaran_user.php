<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

session_start();

if (!isset($_SESSION['id_pengguna'])) {
    echo json_encode(['success' => false, 'message' => 'Harap login']);
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];

// Query melalui relasi: pengguna -> pendaftar -> pendaftaran -> hunian -> pembayaran
$query = "SELECT 
            p.id_pembayaran,
            p.nomor_va,
            p.bank,
            p.jumlah_tagihan,
            p.bukti_pembayaran,
            p.status_pembayaran,
            p.tanggal_bayar,
            p.tanggal_batas_bayar,
            pf.nama_lengkap,
            pf.nim
          FROM pembayaran p
          JOIN hunian h ON p.id_hunian = h.id_hunian
          JOIN pendaftaran pd ON h.id_pendaftaran = pd.id_pendaftaran
          JOIN pendaftar pf ON pd.id_pendaftar = pf.id_pendaftar
          JOIN pengguna pg ON pf.id_pengguna = pg.id_pengguna
          WHERE pg.id_pengguna = $id_pengguna
          ORDER BY p.id_pembayaran DESC
          LIMIT 1";

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

mysqli_close($conn);
?>