<?php
include __DIR__ . '/../../includes/dbOnlinePOS.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "CALL sp_delete_penjual('$id')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: liat-toko.php");
        exit();
    } else {
        echo "Error Database: " . mysqli_error($conn);
    }
} else {
    header("Location: liat-toko.php");
}
?>