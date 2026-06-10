<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'asrama_pendaftaran';

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>