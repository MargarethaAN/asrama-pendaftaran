<?php
$judul = $_POST['judul'];
$versi = $_POST['versi'];
$tanggal = $_POST['tanggal'];
$isi = $_POST['isi'];
$status = $_POST['status'];

require_once '../config/database.php';

$query = "INSERT INTO syarat_ketentuan (judul, versi, tanggal_berlaku, isi, status) 
          VALUES ('$judul', '$versi', '$tanggal', '$isi', '$status')";

mysqli_query($conn, $query);

header('Location: ../../pages/admin-syarat-ketentuan.html');
exit;
?>