<?php
session_start();
require '../../includes/dbOnlinePOS.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idReview = $_POST['idReview'];
    $idProduk = $_POST['idProduk'];
    $isiBalasan = $_POST['isiBalasan'];

    $sql = "UPDATE tbReview SET balasanPenjual = ? WHERE idReview = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $isiBalasan, $idReview);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Reply sent successfully!";
    } else {
        $_SESSION['error'] = "Failed to send reply.";
    }
    
    header("Location: liat-produk.php?id=" . $idProduk);
    exit;
}