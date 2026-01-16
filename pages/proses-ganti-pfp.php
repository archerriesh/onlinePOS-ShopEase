<?php
session_start();
require '../includes/dbOnlinePOS.php';

if (!isset($_FILES['profile_img'])) {
    header("Location: profile.php");
    exit;
}

$username   = $_SESSION['username'];
$role       = $_SESSION['role'];
$redirectTo = $_POST['redirect_to'] ?? 'profile.php'; // Halaman asal

$targetDir = "../foto/";
$file      = $_FILES['profile_img'];
$ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed   = ['jpg', 'jpeg', 'png'];

if (!in_array($ext, $allowed)) {
    header("Location: $redirectTo?error=ext"); exit;
}
if ($file['size'] > 2000000) {
    header("Location: $redirectTo?error=size"); exit;
}

$newFileName = $role . "_" . $username . "_" . time() . "." . $ext;
$targetPath  = $targetDir . $newFileName;

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    switch ($role) {
        case 'pelanggan': $query = "UPDATE tbPelanggan SET fotoPelanggan = ? WHERE usernamePelanggan = ?"; break;
        case 'penjual':   $query = "UPDATE tbPenjual SET fotoPenjual = ? WHERE usernamePenjual = ?"; break;
        case 'admin':     $query = "UPDATE tbAdmin SET foto = ? WHERE username = ?"; break;
    }

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $newFileName, $username);
    mysqli_stmt_execute($stmt);

    header("Location: $redirectTo?upload=success");
} else {
    header("Location: $redirectTo?error=system");
}
exit;