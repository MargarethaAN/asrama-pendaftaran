<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

$query = "SELECT * FROM kamar ORDER BY lantai, nomor_kamar";
$result = mysqli_query($conn, $query);

$kamar = [];
while ($row = mysqli_fetch_assoc($result)) {
    $kamar[] = $row;
}

echo json_encode(['success' => true, 'data' => $kamar]);
?>