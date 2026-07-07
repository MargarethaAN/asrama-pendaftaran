<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../config/database.php';

session_start();

if (!isset($_SESSION['id_pengguna'])) {
    echo json_encode(['success' => false, 'message' => 'Harap login']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id_dokumen = intval($data['id_dokumen']);

// Ambil file_path sebelum delete
$query = "SELECT file_path FROM dokumen WHERE id_dokumen = $id_dokumen";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $file_path = '../' . $row['file_path'];
    
    // Hapus file dari folder
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    
    // Hapus dari database
    $delete = "DELETE FROM dokumen WHERE id_dokumen = $id_dokumen";
    
    if (mysqli_query($conn, $delete)) {
        echo json_encode(['success' => true, 'message' => 'Dokumen berhasil dihapus']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus dari database']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dokumen tidak ditemukan']);
}
?>