<?php
// Aktifkan semua error agar terlihat
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Header JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Koneksi database
require_once '../config/database.php';

session_start();

// Cek login
if (!isset($_SESSION['id_pengguna'])) {
    echo json_encode(['success' => false, 'message' => 'Harap login']);
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];

// Ambil id_pendaftar
$query = "SELECT id_pendaftar FROM pendaftar WHERE id_pengguna = $id_pengguna LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . mysqli_error($conn)]);
    exit;
}

if (mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Data pendaftar tidak ditemukan. Pastikan user sudah mendaftar.']);
    exit;
}

$row = mysqli_fetch_assoc($result);
$id_pendaftar = $row['id_pendaftar'];

// Cek file
if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] != 0) {
    echo json_encode(['success' => false, 'message' => 'File tidak valid']);
    exit;
}

$file = $_FILES['bukti'];

// Validasi ukuran
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

// Buat folder
$uploadDir = '../../uploads/bukti/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Simpan file
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$newName = 'bukti_' . $id_pendaftar . '_' . time() . '.' . $ext;
$filePath = $uploadDir . $newName;

if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan file']);
    exit;
}

$pathToSave = 'uploads/bukti/' . $newName;

// Cek pembayaran
$queryCheck = "SELECT id_pembayaran FROM pembayaran WHERE id_pendaftar = $id_pendaftar LIMIT 1";
$resultCheck = mysqli_query($conn, $queryCheck);

if (!$resultCheck) {
    echo json_encode(['success' => false, 'message' => 'Query check error: ' . mysqli_error($conn)]);
    exit;
}

if (mysqli_num_rows($resultCheck) > 0) {
    $rowCheck = mysqli_fetch_assoc($resultCheck);
    $id_pembayaran = $rowCheck['id_pembayaran'];
    $queryUpdate = "UPDATE pembayaran SET bukti_pembayaran = '$pathToSave', status_pembayaran = 'menunggu', tanggal_bayar = NOW() WHERE id_pembayaran = $id_pembayaran";
    if (!mysqli_query($conn, $queryUpdate)) {
        echo json_encode(['success' => false, 'message' => 'Update error: ' . mysqli_error($conn)]);
        exit;
    }
} else {
    $queryInsert = "INSERT INTO pembayaran (id_pendaftar, bukti_pembayaran, status_pembayaran, tanggal_bayar) VALUES ($id_pendaftar, '$pathToSave', 'menunggu', NOW())";
    if (!mysqli_query($conn, $queryInsert)) {
        echo json_encode(['success' => false, 'message' => 'Insert error: ' . mysqli_error($conn)]);
        exit;
    }
}

echo json_encode(['success' => true, 'message' => 'Bukti berhasil diupload']);
?>