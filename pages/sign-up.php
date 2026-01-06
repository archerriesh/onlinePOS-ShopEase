<?php
require '../includes/dbOnlinePOS.php';

if (!isset($_POST['username'], $_POST['password'], $_POST['confirm_password'])) {
    header("Location: sign-up-page.php");
    exit;
}

$nama     = $_POST['nama'];
$username = $_POST['username'];
$kontak   = $_POST['kontak'];
$alamat   = $_POST['alamat'];
$password = $_POST['password'];
$confirm  = $_POST['confirm_password'];

// cek password sama
if ($password !== $confirm) {
    header("Location: sign-up-page.php?error=password");
    exit;
}

// cek username sudah ada atau belum
$cek = "SELECT * FROM tbPelanggan WHERE usernamePelanggan = ?";
$stmt = mysqli_prepare($conn, $cek);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    header("Location: sign-up-page.php?error=username");
    exit;
}

// INSERT ke database
$query = "INSERT INTO tbPelanggan 
(namaPelanggan, usernamePelanggan, passwordPelanggan, kontak, alamat)
VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param(
    $stmt,
    "sssss",
    $nama,
    $username,
    $password,
    $kontak,
    $alamat
);

if (mysqli_stmt_execute($stmt)) {
    header("Location: sign-in-page.php?signup=success");
    exit;
}

header("Location: sign-up-page.php?error=failed");
exit;
