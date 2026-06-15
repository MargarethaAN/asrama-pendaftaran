<?php
error_reporting(0);
require_once '../config/database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    mysqli_query($conn, "UPDATE syarat_ketentuan SET status = IF(status = 'aktif', 'nonaktif', 'aktif') WHERE id_sk = $id");
}

header('Location: ../../pages/admin-syarat-ketentuan.html');
exit;
?>