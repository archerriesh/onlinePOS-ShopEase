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
        $_SESSION['idPelanggan'] = $user['idPelanggan'];

        header("Location: /onlinePOS/pages/home-page.php");
        exit;
    }
}

$queryPenjual = "SELECT * FROM tbPenjual WHERE usernamePenjual = ?";
$stmtPenjual = mysqli_prepare($conn, $queryPenjual);
mysqli_stmt_bind_param($stmtPenjual, "s", $username);
mysqli_stmt_execute($stmtPenjual);

$resultPenjual = mysqli_stmt_get_result($stmtPenjual);
$penjual = mysqli_fetch_assoc($resultPenjual);

if ($penjual && ($password === $penjual['passwordPenjual'] || password_verify($password, $penjual['passwordPenjual']))) {
    $_SESSION['login'] = true;
    $_SESSION['role'] = 'penjual';
    $_SESSION['username'] = $penjual['usernamePenjual'];
    $_SESSION['idPenjual'] = $penjual['idPenjual']; 

    header("Location: /onlinePOS/pages/seller/home-seller.php");
    exit;
}

$queryAdmin = "SELECT * FROM tbadmin WHERE username = ?";
$stmtAdmin = mysqli_prepare($conn, $queryAdmin);
mysqli_stmt_bind_param($stmtAdmin, "s", $username);
mysqli_stmt_execute($stmtAdmin);
$resultAdmin = mysqli_stmt_get_result($stmtAdmin);
$admin = mysqli_fetch_assoc($resultAdmin);

if ($admin) {
    $dbPasswordAdmin = $admin['password'];
    if ($password === $dbPasswordAdmin || password_verify($password, $dbPasswordAdmin)) {
        $_SESSION['login'] = true;
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = $admin['username'];
        $_SESSION['idAdmin'] = $admin['idAdmin'];
        $_SESSION['namaAdmin'] = $admin['namaAdmin']; 

        header("Location: /onlinePOS/pages/admin/home-page.php");
        exit;
    }
}

header("Location: sign-in-page.php?error=1");
exit;
