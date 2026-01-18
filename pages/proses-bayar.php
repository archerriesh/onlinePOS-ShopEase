<?php
session_start();
include '../includes/dbOnlinePOS.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idTransaksi = $_POST['idTransaksi'];
    $status = $_POST['statusBaru'];
    
    $sql = "CALL sp_proses_pembayaran(?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $idTransaksi, $status);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: history.php?tab=toship&status=paid");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>