<?php
header('Content-Type: application/json');
session_start();

if (isset($_SESSION['id_pengguna'])) {
    echo json_encode([
        'logged_in' => true,
        'role' => $_SESSION['role'],
        'nama' => $_SESSION['nama']
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>