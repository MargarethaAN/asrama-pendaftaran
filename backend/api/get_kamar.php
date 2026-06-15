<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

$query = "SELECT id_kamar, nomor_kamar, lantai, kapasitas, status_kamar FROM kamar ORDER BY lantai, nomor_kamar";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . mysqli_error($conn)]);
    exit;
}

$kamar = [];
while ($row = mysqli_fetch_assoc($result)) {
    $kamar[] = $row;
}

echo json_encode(['success' => true, 'data' => $kamar]);
?>