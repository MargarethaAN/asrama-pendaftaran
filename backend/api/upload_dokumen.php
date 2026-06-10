<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

session_start();

if (!isset($_SESSION['id_pengguna'])) {
    echo json_encode(['success' => false, 'message' => 'Harap login terlebih dahulu']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Tidak ada file yang diupload']);
    exit;
}

$jenis_dokumen = mysqli_real_escape_string($conn, $_POST['jenis_dokumen']);
$file = $_FILES['file'];

if ($file['size'] > 2 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Ukuran file maksimal 2MB']);
    exit;
}

$allowed = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
if (!in_array($file['type'], $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Format file harus JPG, PNG, atau PDF']);
    exit;
}

$target_dir = "../uploads/dokumen/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$new_filename = time() . '_' . uniqid() . '.' . $extension;
$target_file = $target_dir . $new_filename;

if (move_uploaded_file($file["tmp_name"], $target_file)) {
    $id_pengguna = $_SESSION['id_pengguna'];
    $query = "SELECT id_pendaftar FROM pendaftar WHERE id_pengguna = $id_pengguna";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $id_pendaftar = $row['id_pendaftar'];
        
        // Cek apakah sudah ada dokumen dengan jenis yang sama
        $check = "SELECT id_dokumen FROM dokumen WHERE id_pendaftar = $id_pendaftar AND jenis_dokumen = '$jenis_dokumen'";
        $checkResult = mysqli_query($conn, $check);
        
        if (mysqli_num_rows($checkResult) > 0) {
            // Hapus file lama
            $old = mysqli_fetch_assoc($checkResult);
            $oldQuery = "SELECT file_path FROM dokumen WHERE id_dokumen = " . $old['id_dokumen'];
            $oldResult = mysqli_query($conn, $oldQuery);
            $oldRow = mysqli_fetch_assoc($oldResult);
            $oldFile = '../' . $oldRow['file_path'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            
            // Update
            $update = "UPDATE dokumen SET file_path = 'uploads/dokumen/$new_filename', tanggal_upload = CURDATE() WHERE id_dokumen = " . $old['id_dokumen'];
            mysqli_query($conn, $update);
        } else {
            // Insert baru
            $insert = "INSERT INTO dokumen (id_pendaftar, jenis_dokumen, file_path, tanggal_upload, status_verifikasi) 
                       VALUES ($id_pendaftar, '$jenis_dokumen', 'uploads/dokumen/$new_filename', CURDATE(), 'menunggu')";
            mysqli_query($conn, $insert);
        }
        
        echo json_encode(['success' => true, 'message' => 'Dokumen berhasil diupload', 'file_path' => 'uploads/dokumen/' . $new_filename]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data pendaftar tidak ditemukan']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan file']);
}
?>