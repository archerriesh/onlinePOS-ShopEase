<?php
include __DIR__ . '/../../includes/dbOnlinePOS.php';
$id = $_GET['id'];
mysqli_query($conn, "CALL sp_aktifkan_kembali('$id', 'buyer')");
header("Location: kelola-buyer.php");