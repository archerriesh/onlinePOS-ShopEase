<?php
include __DIR__ . '/../../includes/dbOnlinePOS.php';
$id = $_GET['id'];
mysqli_query($conn, "CALL sp_delete_pelanggan('$id')");
header("Location: kelola-buyer.php");