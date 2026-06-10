<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Data tidak diterima']);
    exit;
}

$email = mysqli_real_escape_string($conn, $data['email']);
$password = md5(mysqli_real_escape_string($conn, $data['password']));
$nama = mysqli_real_escape_string($conn, $data['nama']);
$role = 'pendaftar';

// Cek email sudah terdaftar
$check = "SELECT id_pengguna FROM pengguna WHERE email = '$email'";
$result = mysqli_query($conn, $check);

if (mysqli_num_rows($result) > 0) {
    echo json_encode(['success' => false, 'message' => 'Email sudah terdaftar!']);
    exit;
}

$query = "INSERT INTO pengguna (email, password, nama, role) VALUES ('$email', '$password', '$nama', '$role')";

if (mysqli_query($conn, $query)) {
    $id_pengguna = mysqli_insert_id($conn);
    
    $insertPendaftar = "INSERT INTO pendaftar (id_pengguna, nim, nama_lengkap) VALUES ($id_pengguna, '', '$nama')";
    mysqli_query($conn, $insertPendaftar);
    
    echo json_encode(['success' => true, 'message' => 'Registrasi berhasil! Silakan login.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Registrasi gagal: ' . mysqli_error($conn)]);
}
?>