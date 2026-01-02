<?php
session_start();
require '../includes/dbOnlinePOS.php';

if (!isset($_POST['username'], $_POST['password'])) {
    header("Location: sign-in-page.php");
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];

/* query */
$query = "SELECT * FROM tbPelanggan WHERE usernamePelanggan = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user && $password === $user['password']) {
    $_SESSION['login'] = true;
    $_SESSION['username'] = $user['usernamePelanggan'];

    header("Location: home-page.php");
    exit;
} else {
    header("Location: sign-in-page.php?error=1");
    exit;
}
