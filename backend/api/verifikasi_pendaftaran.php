<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

session_start();

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$id_pendaftaran = mysqli_real_escape_string($conn, $data['id_pendaftaran']);
$status = mysqli_real_escape_string($conn, $data['status']);
$catatan = mysqli_real_escape_string($conn, $data['catatan'] ?? '');

// Dapatkan id_admin
$queryAdmin = "SELECT id_admin FROM admin WHERE id_pengguna = " . $_SESSION['id_pengguna'];
$resultAdmin = mysqli_query($conn, $queryAdmin);
$admin = mysqli_fetch_assoc($resultAdmin);
$id_admin = $admin['id_admin'] ?? 0;

$query = "UPDATE pendaftaran SET 
          status_pendaftaran = '$status',
          catatan_revisi = '$catatan',
          verifikasi_oleh = $id_admin,
          verifikasi_pada = NOW()
          WHERE id_pendaftaran = $id_pendaftaran";

if (mysqli_query($conn, $query)) {
    if ($status == 'diterima') {
        $getPendaftar = "SELECT id_pendaftar FROM pendaftaran WHERE id_pendaftaran = $id_pendaftaran";
        $result = mysqli_query($conn, $getPendaftar);
        $row = mysqli_fetch_assoc($result);
        $id_pendaftar = $row['id_pendaftar'];
        
        $checkHunian = "SELECT id_hunian FROM hunian WHERE id_pendaftar = $id_pendaftar";
        $checkResult = mysqli_query($conn, $checkHunian);
        
        if (mysqli_num_rows($checkResult) == 0) {
            $insertHunian = "INSERT INTO hunian (id_pendaftar, status_hunian) VALUES ($id_pendaftar, 'aktif')";
            mysqli_query($conn, $insertHunian);
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'Verifikasi berhasil']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal: ' . mysqli_error($conn)]);
}
?>