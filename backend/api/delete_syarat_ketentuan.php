<?php
error_reporting(0);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../config/database.php';

session_start();

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'admin') {
    $response['message'] = 'Akses ditolak';
    echo json_encode($response);
    exit;
}

$raw_input = file_get_contents('php://input');
$data = json_decode($raw_input, true);

if (!$data) {
    $response['message'] = 'Data tidak valid';
    echo json_encode($response);
    exit;
}

$id_sk = isset($data['id_sk']) ? intval($data['id_sk']) : 0;

if ($id_sk <= 0) {
    $response['message'] = 'ID tidak valid';
    echo json_encode($response);
    exit;
}

$query = "DELETE FROM syarat_ketentuan WHERE id_sk = $id_sk";

if (mysqli_query($conn, $query)) {
    $response['success'] = true;
    $response['message'] = 'Syarat & ketentuan berhasil dihapus';
} else {
    $response['message'] = 'Database error: ' . mysqli_error($conn);
}

echo json_encode($response);
?>