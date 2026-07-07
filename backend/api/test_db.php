<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$query = "SELECT COUNT(*) as total FROM kamar";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

echo json_encode([
    'success' => true, 
    'total_kamar' => $row['total'],
    'message' => 'Koneksi database berhasil'
]);
?>