<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

session_start();
if (!isset($_SESSION['id_pengguna'])) {
    echo json_encode(['success' => false, 'message' => 'Harap login terlebih dahulu']);
    exit;
}

// Folder upload
$target_dir = "../uploads/dokumen/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

$jenis_dokumen = mysqli_real_escape_string($conn, $_POST['jenis_dokumen']);
$file = $_FILES['file'];

// Validasi file
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Gagal upload file: ' . $file['error']]);
    exit;
}

// Validasi ukuran (max 2MB)
if ($file['size'] > 2 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Ukuran file maksimal 2MB']);
    exit;
}

// Validasi tipe
$allowed = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
if (!in_array($file['type'], $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Format file harus JPG, PNG, atau PDF']);
    exit;
}

// Buat nama file unik
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$new_filename = time() . '_' . uniqid() . '.' . $extension;
$target_file = $target_dir . $new_filename;

if (move_uploaded_file($file["tmp_name"], $target_file)) {
    // Dapatkan id_pendaftar
    $id_pengguna = $_SESSION['id_pengguna'];
    $queryPendaftar = "SELECT id_pendaftar FROM pendaftar WHERE id_pengguna = $id_pengguna";
    $result = mysqli_query($conn, $queryPendaftar);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $id_pendaftar = $row['id_pendaftar'];
        
        // Simpan ke database
        $file_path_db = 'uploads/dokumen/' . $new_filename;
        $insert = "INSERT INTO dokumen (id_pendaftar, jenis_dokumen, file_path, tanggal_upload, status_verifikasi) 
                   VALUES ($id_pendaftar, '$jenis_dokumen', '$file_path_db', CURDATE(), 'menunggu')";
        
        if (mysqli_query($conn, $insert)) {
            echo json_encode(['success' => true, 'message' => 'Dokumen berhasil diupload', 'file_path' => $file_path_db]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan ke database']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Data pendaftar tidak ditemukan']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan file']);
}
?>