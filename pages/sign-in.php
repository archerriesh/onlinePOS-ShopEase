<?php
session_start();
require '../includes/dbOnlinePOS.php';

if (!isset($_POST['username'], $_POST['password'])) {
    header("Location: sign-in-page.php");
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];

$queryPelanggan = "SELECT * FROM tbPelanggan WHERE usernamePelanggan = ?";
$stmtPelanggan = mysqli_prepare($conn, $queryPelanggan);
mysqli_stmt_bind_param($stmtPelanggan, "s", $username);
mysqli_stmt_execute($stmtPelanggan);

$resultPelanggan = mysqli_stmt_get_result($stmtPelanggan);
$pelanggan = mysqli_fetch_assoc($resultPelanggan);

if ($pelanggan && $password === $pelanggan['passwordPelanggan']) {
    $_SESSION['login'] = true;
    $_SESSION['role'] = 'pelanggan';
    $_SESSION['username'] = $pelanggan['usernamePelanggan'];

    header("Location: /onlinePOS/pages/home-page.php");
    exit;
}

$queryPenjual = "SELECT * FROM tbPenjual WHERE usernamePenjual = ?";
$stmtPenjual = mysqli_prepare($conn, $queryPenjual);
mysqli_stmt_bind_param($stmtPenjual, "s", $username);
mysqli_stmt_execute($stmtPenjual);

$resultPenjual = mysqli_stmt_get_result($stmtPenjual);
$penjual = mysqli_fetch_assoc($resultPenjual);

if ($penjual && $password === $penjual['passwordPenjual']) {
    $_SESSION['login'] = true;
    $_SESSION['role'] = 'penjual';
    $_SESSION['username'] = $penjual['usernamePenjual'];

    header("Location: /onlinePOS/pages/seller/index-seller.php");
    exit;
}

header("Location: sign-in-page.php?error=1");
exit;
