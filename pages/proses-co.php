<?php
session_start();
include '../includes/dbOnlinePOS.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPelanggan = $_SESSION['idPelanggan'];
    $idPromo = !empty($_POST['idPromo']) ? $_POST['idPromo'] : NULL;
    $metode = $_POST['metodePembayaran'];
    $ekspedisi = $_POST['ekspedisi'];

    $sql = "CALL sp_checkout_keranjang(?, ?, ?, ?, @status)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $idPelanggan, $idPromo, $metode, $ekspedisi);
    
    if (mysqli_stmt_execute($stmt)) {
        $res = mysqli_query($conn, "SELECT @status AS pesan");
        $row = mysqli_fetch_assoc($res);
        $pesan = $row['pesan'];

        if (strpos($pesan, 'berhasil') !== false) {
            header("Location: history.php?msg=" . urlencode($pesan));
        } else {
            echo "<script>alert('$pesan'); window.location.href='co-langsung.php';</script>";
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    exit;
}