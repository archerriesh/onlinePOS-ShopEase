<?php
require '../includes/dbOnlinePOS.php';

if (!isset($_POST['username'], $_POST['password'])) {
    header("Location: sign-up-page.php");
    exit;
}

$nama     = $_POST['nama'];
$username = $_POST['username'];
$kontak   = $_POST['kontak'];
$alamat   = $_POST['alamat'];
$password = $_POST['password'];
$confirm  = $_POST['confirm_password'];

if ($password !== $confirm) {
    header("Location: sign-up-page.php?error=password");
    exit;
}

$cek = "SELECT usernamePelanggan FROM tbPelanggan WHERE usernamePelanggan = ?";
$stmtCek = mysqli_prepare($conn, $cek);
mysqli_stmt_bind_param($stmtCek, "s", $username);
mysqli_stmt_execute($stmtCek);
mysqli_stmt_store_result($stmtCek);

if (mysqli_stmt_num_rows($stmtCek) > 0) {
    header("Location: sign-up-page.php?error=username");
    exit;
}
mysqli_stmt_close($stmtCek);

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$query = "CALL sp_signup_pelanggan(?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Gagal menyiapkan SP: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $stmt, 
    "sssss", 
    $nama, 
    $username, 
    $hashedPassword, 
    $alamat, 
    $kontak
);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: sign-in-page.php?signup=success");
    exit;
} else {
    die("Gagal Registrasi: " . mysqli_stmt_error($stmt));
}