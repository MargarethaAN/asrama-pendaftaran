<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

session_start();

if (!isset($_SESSION['id_pengguna'])) {
    echo json_encode(['success' => false, 'message' => 'Harap login']);
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];

// Dapatkan id_pendaftar dan id_hunian
$query = "SELECT p.id_pendaftar, h.id_hunian 
          FROM pendaftar p 
          LEFT JOIN hunian h ON p.id_pendaftar = h.id_pendaftar 
          WHERE p.id_pengguna = $id_pengguna 
          ORDER BY h.id_hunian DESC LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $id_hunian = $row['id_hunian'];
    
    if ($id_hunian) {
        $queryPembayaran = "SELECT * FROM pembayaran WHERE id_hunian = $id_hunian ORDER BY id_pembayaran DESC LIMIT 1";
        $resultPembayaran = mysqli_query($conn, $queryPembayaran);
        
        if (mysqli_num_rows($resultPembayaran) > 0) {
            $data = mysqli_fetch_assoc($resultPembayaran);
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => true, 'data' => null]);
        }
    } else {
        echo json_encode(['success' => true, 'data' => null]);
    }
} else {
    echo json_encode(['success' => true, 'data' => null]);
}
?>