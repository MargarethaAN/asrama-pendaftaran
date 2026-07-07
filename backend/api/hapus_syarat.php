<?php
require_once '../config/database.php';

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM syarat_ketentuan WHERE id_sk=$id");

header('Location: ../../pages/admin-syarat-ketentuan.html');
exit;
?>