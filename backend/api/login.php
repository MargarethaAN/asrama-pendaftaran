<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Data tidak diterima']);
    exit;
}

$email = mysqli_real_escape_string($conn, $data['email']);
$password = md5(mysqli_real_escape_string($conn, $data['password']));
$role = mysqli_real_escape_string($conn, $data['role']);

$query = "SELECT * FROM pengguna WHERE email = '$email' AND password = '$password' AND role = '$role'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    
    $_SESSION['id_pengguna'] = $user['id_pengguna'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    
    echo json_encode([
        'success' => true,
        'role' => $user['role'],
        'nama' => $user['nama'],
        'message' => 'Login berhasil'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Email, password, atau role salah'
    ]);
}
?>