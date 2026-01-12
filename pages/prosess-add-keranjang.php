<?php
session_start();
include '../includes/dbOnlinePOS.php';

if (!isset($_SESSION['idPelanggan'])) {
    header("Location: sign-in-page.php");
    exit;
}

$idPelanggan = $_SESSION['idPelanggan'];
$idProduk    = $_POST['idProduk'] ?? '';
$qty         = (int) ($_POST['qty'] ?? 1);

if ($idProduk === '' || $qty <= 0) {
    header("Location: liat-produk.php?id=" . urlencode($idProduk));
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "CALL sp_kelola_keranjang(?, ?, ?)"
);

if (!$stmt) {
    die("Prepare gagal: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $stmt,
    "ssi",
    $idPelanggan,
    $idProduk,
    $qty
);

mysqli_stmt_execute($stmt);

while (mysqli_more_results($conn)) {
    mysqli_next_result($conn);
}

mysqli_stmt_close($stmt);

header("Location: co-keranjang.php");
exit;