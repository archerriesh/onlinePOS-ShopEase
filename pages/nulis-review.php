<?php
session_start();
require '../includes/dbOnlinePOS.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idDet = $_POST['idDetail']; 
    $idProd = $_POST['idProduk']; 
    $rating = (int)$_POST['rating'];
    $komentar = $_POST['komentar'];

    if ($rating >= 1) {
        $sql = "CALL sp_insert_review(?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssis", $idDet, $idProd, $rating, $komentar);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: history.php?tab=completed&status=success");
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}