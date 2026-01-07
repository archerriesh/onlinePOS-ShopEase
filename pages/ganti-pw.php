<?php
session_start();
require '../includes/dbOnlinePOS.php';

$username = $_SESSION['username'];

$current = $_POST['current_password'];
$new     = $_POST['new_password'];
$confirm = $_POST['confirm_password'];

/* =========================
   1. VALIDASI DASAR
   ========================= */

// confirm password harus sama
if ($new !== $confirm) {
    die("New password does not match");
}

// panjang password minimal
if (strlen($new) < 8) {
    die("Password must be at least 8 characters");
}

/* =========================
   2. AMBIL PASSWORD LAMA
   ========================= */

$query = "SELECT passwordPelanggan FROM tbPelanggan WHERE usernamePelanggan = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

/* =========================
   3. CEK PASSWORD LAMA
   ========================= */

if (!password_verify($current, $data['passwordPelanggan'])) {
    die("Current password is incorrect");
}

/* =========================
   4. HASH & UPDATE
   ========================= */

$newHash = password_hash($new, PASSWORD_DEFAULT);

$update = "UPDATE tbPelanggan SET passwordPelanggan = ? WHERE usernamePelanggan = ?";
$stmt = mysqli_prepare($conn, $update);
mysqli_stmt_bind_param($stmt, "ss", $newHash, $username);
mysqli_stmt_execute($stmt);

/* =========================
   5. REDIRECT
   ========================= */

header("Location: profile.php?password=success");
exit;
