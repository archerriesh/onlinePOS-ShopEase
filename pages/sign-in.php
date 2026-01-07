<?php
session_start();
require '../includes/dbOnlinePOS.php';

if (!isset($_POST['username'], $_POST['password'])) {
    header("Location: sign-in-page.php");
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM tbPelanggan WHERE usernamePelanggan = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user) {
    $dbPassword = $user['passwordPelanggan'];

    if ($password === $dbPassword || password_verify($password, $dbPassword)) {
        $_SESSION['login'] = true;
        $_SESSION['role'] = 'pelanggan';
        $_SESSION['username'] = $user['usernamePelanggan'];

        header("Location: /onlinePOS/pages/home-page.php");
        exit;
    }
}

header("Location: sign-in-page.php?error=1");
exit;

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
