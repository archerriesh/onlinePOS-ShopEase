<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require '../../includes/dbOnlinePOS.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index-seller.php');
    exit;
}

$idProduk = $_POST['idProduk'] ?? '';

if (!empty($idProduk)) {
    $querySP = "CALL sp_aktifkan_kembali(?, 'produk')";
    $stmt = mysqli_prepare($conn, $querySP);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $idProduk);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        while (mysqli_more_results($conn)) {
            mysqli_next_result($conn);
        }
    }

    $queryManual = "UPDATE tbproduk SET statusAktif = 'Y' WHERE idProduk = ?";
    $stmtManual = mysqli_prepare($conn, $queryManual);
    
    if ($stmtManual) {
        mysqli_stmt_bind_param($stmtManual, "s", $idProduk);
        mysqli_stmt_execute($stmtManual);
        
        if (mysqli_stmt_affected_rows($stmtManual) >= 0) {
            $_SESSION['success'] = "Produk berhasil diaktifkan kembali!";
        } else {
            $_SESSION['error'] = "Gagal mengupdate database.";
        }
        mysqli_stmt_close($stmtManual);
    } else {
        $_SESSION['error'] = "Kesalahan sistem database.";
    }
}

mysqli_close($conn);
header('Location: index-seller.php');
exit;
?>