<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../config/database.php';

session_start();

// Cek login admin
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

// Ambil data dari request
$data = json_decode(file_get_contents('php://input'), true);

// Validasi input
if (!isset($data['id_pembayaran']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

$id_pembayaran = intval($data['id_pembayaran']);
$status = mysqli_real_escape_string($conn, $data['status']);

// Validasi status
$allowedStatus = ['lunas', 'ditolak', 'menunggu'];
if (!in_array($status, $allowedStatus)) {
    echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
    exit;
}

// Cek apakah admin punya id_admin
$queryAdmin = "SELECT id_admin FROM admin WHERE id_pengguna = " . $_SESSION['id_pengguna'] . " LIMIT 1";
$resultAdmin = mysqli_query($conn, $queryAdmin);

if (mysqli_num_rows($resultAdmin) > 0) {
    $rowAdmin = mysqli_fetch_assoc($resultAdmin);
    $id_admin = $rowAdmin['id_admin'];
} else {
    // Jika tidak ada, gunakan NULL atau 1
    $id_admin = 'NULL';
}

// Update pembayaran
$query = "UPDATE pembayaran SET 
          status_pembayaran = '$status'";

// Tambahkan verifikasi jika status berubah
if ($status == 'lunas' || $status == 'ditolak') {
    $query .= ", verifikasi_oleh = $id_admin, verifikasi_pada = NOW()";
}

$query .= " WHERE id_pembayaran = $id_pembayaran";

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'message' => 'Verifikasi pembayaran berhasil']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>