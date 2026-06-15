<?php
error_reporting(0);
require_once '../config/database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    mysqli_query($conn, "DELETE FROM syarat_ketentuan WHERE id_sk = $id");
}

header('Location: ../../pages/admin-syarat-ketentuan.html');
exit;
?>