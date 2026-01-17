<?php
session_start();
require '../../includes/dbOnlinePOS.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['idProduk'])) {
    $idProduk = $_POST['idProduk'];
    $query = "CALL sp_delete_produk(?)";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $idProduk);
        mysqli_stmt_execute($stmt);
        $_SESSION['success'] = "Produk berhasil dinonaktifkan!";
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error'] = "Database error!";
    }
}
header('Location: index-seller.php');
exit;