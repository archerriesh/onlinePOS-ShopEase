<?php
session_start();
include '../includes/dbOnlinePOS.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['idPelanggan'])) {
        die("Sesi berakhir, silakan login kembali.");
    }

    $idPelanggan = $_SESSION['idPelanggan'];
    $idPromo     = !empty($_POST['idPromo']) ? $_POST['idPromo'] : NULL;
    $metode      = $_POST['metodePembayaran'];
    $ekspedisi   = $_POST['ekspedisi'];
    
    $listIdProduk = $_POST['list_id_produk'] ?? '';

    if (empty($listIdProduk)) {
        echo "<script>alert('Tidak ada produk yang dipilih!'); window.location.href='co-keranjang.php';</script>";
        exit;
    }

    $sql  = "CALL sp_checkout_keranjang_pilihan(?, ?, ?, ?, ?, @status)";
    $stmt = mysqli_prepare($conn, $sql);
    
    mysqli_stmt_bind_param($stmt, "sssss", 
        $idPelanggan, 
        $listIdProduk, 
        $idPromo, 
        $metode, 
        $ekspedisi
    );
    
    if (mysqli_stmt_execute($stmt)) {
        $res = mysqli_query($conn, "SELECT @status AS pesan");
        $row = mysqli_fetch_assoc($res);
        $pesan = $row['pesan'];

        if (strpos($pesan, 'berhasil') !== false) {
            header("Location: history.php?msg=" . urlencode($pesan));
        } else {
            echo "<script>alert('$pesan'); window.location.href='co-keranjang.php';</script>";
        }
    } else {
        echo "Error Database: " . mysqli_error($conn);
    }
    exit;
}