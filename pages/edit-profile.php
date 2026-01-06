<?php
session_start();
require '../includes/dbOnlinePOS.php';

if (!isset($_SESSION['username'])) {
    header("Location: sign-in-page.php");
    exit;
}

$username = $_SESSION['username'];

$query = "UPDATE tbPelanggan
          SET namaPelanggan = ?,
              kontakPelanggan = ?,
              alamatPelanggan = ?
          WHERE usernamePelanggan = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param(
    $stmt,
    "ssss",
    $_POST['namaPelanggan'],
    $_POST['kontakPelanggan'],
    $_POST['alamatPelanggan'],
    $username
);

mysqli_stmt_execute($stmt);

header("Location: profile.php?update=success");
exit;