<?php
require_once '../config/database.php';

$id = $_GET['id'];

mysqli_query($conn, "UPDATE syarat_ketentuan SET status = IF(status='aktif', 'nonaktif', 'aktif') WHERE id_sk=$id");

header('Location: ../../pages/admin-syarat-ketentuan.html');
exit;
?>