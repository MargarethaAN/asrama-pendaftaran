<?php
header('Content-Type: application/json');
session_start();

if (isset($_SESSION['id_pengguna'])) {
    echo json_encode([
        'logged_in' => true,
        'id_pengguna' => $_SESSION['id_pengguna'],
        'nama' => $_SESSION['nama'],
        'email' => $_SESSION['email'],
        'role' => $_SESSION['role']
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>