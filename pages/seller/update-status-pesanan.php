<?php
session_start();
include '../../includes/dbOnlinePOS.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['idPenjual'])) {
    $idTrxPenjual = $_POST['idTrxPenjual'];
    $statusKirim = $_POST['statusPengiriman'];
    
    $statusPesanan = ($statusKirim === 'Sampai Tujuan') ? 'Selesai' : 'Diproses';

    $query = "UPDATE tbTransaksiPenjual SET statusPengiriman = ?, statusPesanan = ? WHERE idTrxPenjual = ? AND idPenjual = ?";
    $stmt = mysqli_prepare($conn, $query); 
    mysqli_stmt_bind_param($stmt, "ssss", $statusKirim, $statusPesanan, $idTrxPenjual, $_SESSION['idPenjual']);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: history-seller.php?update=success");
    } else {
        header("Location: history-seller.php?update=failed");
    }
}