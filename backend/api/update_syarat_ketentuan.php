<?php
// Ambil data dari FormData (bukan POST biasa)
$id_sk = $_POST['id_sk'];
$judul = $_POST['judul'];
$versi = $_POST['versi'];
$tanggal = $_POST['tanggal_berlaku'];
$isi = $_POST['isi'];
$status = $_POST['status'];

require_once '../config/database.php';

$query = "UPDATE syarat_ketentuan SET 
          judul='$judul', 
          versi='$versi', 
          tanggal_berlaku='$tanggal', 
          isi='$isi', 
          status='$status' 
          WHERE id_sk=$id_sk";

mysqli_query($conn, $query);

// Redirect
header('Location: ../../pages/admin-syarat-ketentuan.html');
exit;
?>