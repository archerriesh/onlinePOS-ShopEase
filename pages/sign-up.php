<?php
require '../includes/dbOnlinePOS.php';

if (!isset($_POST['username'], $_POST['password'])) {
    header("Location: sign-up-page.php");
    exit;
}

$role     = $_POST['role'];
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

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

switch ($role) {
    case 'admin':
        $spec = $_POST['specification'];
        $email = $_POST['kontak'];
        $query = "CALL sp_signup_admin(?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $nama, $hashedPassword, $username, $spec, $email, $alamat);
        break;

    case 'penjual':
        $kategori = $_POST['kategoriToko'] ?? 'Umum';
        $query = "CALL sp_signup_penjual(?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $nama, $username, $hashedPassword, $alamat, $kategori, $kontak);
        break;

    case 'pelanggan':
        default:
        $query = "CALL sp_signup_pelanggan(?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $nama, $username, $hashedPassword, $alamat, $kontak);
        break;
}

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: sign-in-page.php?signup=success");
    exit;
} else {
    die("Gagal Registrasi: " . mysqli_stmt_error($stmt));
}
?>