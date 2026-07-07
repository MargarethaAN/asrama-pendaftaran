<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../config/database.php';

session_start();

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$id_kamar = intval($data['id_kamar']);
$nama_penghuni = mysqli_real_escape_string($conn, $data['nama_penghuni']);

// Cari id_pendaftar berdasarkan nama
$queryPendaftar = "SELECT id_pendaftar FROM pendaftar WHERE nama_lengkap = '$nama_penghuni'";
$result = mysqli_query($conn, $queryPendaftar);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $id_pendaftar = $row['id_pendaftar'];
    
    // Dapatkan id_pendaftaran
    $queryPendaftaran = "SELECT id_pendaftaran FROM pendaftaran WHERE id_pendaftar = $id_pendaftar ORDER BY id_pendaftaran DESC LIMIT 1";
    $resultPendaftaran = mysqli_query($conn, $queryPendaftaran);
    
    if (mysqli_num_rows($resultPendaftaran) > 0) {
        $row2 = mysqli_fetch_assoc($resultPendaftaran);
        $id_pendaftaran = $row2['id_pendaftaran'];
        
        // Hapus atau update hunian
        $update = "UPDATE hunian SET id_kamar = NULL, status_hunian = 'aktif' WHERE id_pendaftaran = $id_pendaftaran";
        mysqli_query($conn, $update);
        
        // Update status kamar menjadi tersedia jika kosong
        $check = "SELECT COUNT(*) as total FROM hunian WHERE id_kamar = $id_kamar AND id_kamar IS NOT NULL";
        $checkResult = mysqli_query($conn, $check);
        $count = mysqli_fetch_assoc($checkResult);
        
        if ($count['total'] == 0) {
            mysqli_query($conn, "UPDATE kamar SET status_kamar = 'tersedia' WHERE id_kamar = $id_kamar");
        }
        
        echo json_encode(['success' => true, 'message' => 'Penghuni berhasil dihapus dari kamar']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data pendaftaran tidak ditemukan']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Penghuni tidak ditemukan']);
}
?>