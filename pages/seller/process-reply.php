<?php
session_start();
require '../../includes/dbOnlinePOS.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idReview = $_POST['idReview'];
    $idProduk = $_POST['idProduk'];
    $isiBalasan = $_POST['isiBalasan'];

    $sql = "UPDATE tbReview SET balasanPenjual = ? WHERE idReview = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $isiBalasan, $idReview);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: liat-produk.php?id=" . $idProduk . "#review");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}