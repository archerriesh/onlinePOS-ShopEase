<?php
session_start();
include '../includes/dbOnlinePOS.php';

if (!isset($_POST['btn_checkout'])) {
    header("Location: co-keranjang.php");
    exit;
}

$idPelanggan = $_SESSION['idPelanggan'];
$mode = $_POST['modeCheckout'];
$idPromo = !empty($_POST['idPromo']) ? $_POST['idPromo'] : NULL;
$ekspedisi = $_POST['ekspedisi'];
$payment = $_SESSION['payment'] ?? 'Belum Dipilih';
$subPayment = $_SESSION['sub_payment'] ?? '';
$metodeLengkap = trim($payment . ' ' . $subPayment);

try {
    if ($mode === 'buy_now') {
        $idProduk = $_POST['bn_idProduk'];
        $qty = $_POST['bn_qty'];

        $sql = "CALL sp_checkout_transaksi(?, ?, ?, ?, ?, ?, @status)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssisss", $idPelanggan, $idProduk, $qty, $idPromo, $metodeLengkap, $ekspedisi);
    } 
    else {
        $itemParam = $_POST['selected_items'] ?? '';
        $selectedItems = !empty($itemParam) ? explode(',', $itemParam) : [];
        $ids = "'" . implode("','", $selectedItems) . "'";

        mysqli_query($conn, "CREATE TEMPORARY TABLE temp_saved AS 
                             SELECT * FROM tbKeranjang 
                             WHERE idPelanggan = '$idPelanggan' AND idProduk NOT IN ($ids)");

        mysqli_query($conn, "DELETE FROM tbKeranjang 
                             WHERE idPelanggan = '$idPelanggan' AND idProduk NOT IN ($ids)");

        $sql = "CALL sp_checkout_keranjang(?, ?, ?, ?, @status)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $idPelanggan, $idPromo, $metodeLengkap, $ekspedisi);
    }

    if (mysqli_stmt_execute($stmt)) {
        $res = mysqli_query($conn, "SELECT @status AS pesan");
        $row = mysqli_fetch_assoc($res);
        $pesan = $row['pesan'] ?? 'Gagal';

        if (strpos(strtolower($pesan), 'berhasil') !== false) {
            if ($mode !== 'buy_now') {
                mysqli_query($conn, "INSERT INTO tbKeranjang SELECT * FROM temp_saved");
            }
            
            unset($_SESSION['mode_checkout'], $_SESSION['bn_idProduk'], $_SESSION['bn_qty']);
            
            echo "<script>alert('$pesan'); window.location.href='history.php';</script>";
        } else {
            throw new Exception($pesan);
        }
    } else {
        throw new Exception("Gagal mengeksekusi perintah database.");
    }

} catch (Exception $e) {
    if ($mode !== 'buy_now') {
        mysqli_query($conn, "INSERT INTO tbKeranjang SELECT * FROM temp_saved");
    }
    echo "<script>alert('Error: " . $e->getMessage() . "'); window.history.back();</script>";
}