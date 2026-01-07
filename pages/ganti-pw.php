<?php
session_start();
require '../includes/dbOnlinePOS.php';
mysqli_set_charset($conn, 'utf8mb4'); 

if (!isset($_SESSION['username'])) {
    header("Location: sign-in.php");
    exit;
}

$username = $_SESSION['username'];
$from = $_POST['from'] ?? 'profile';

$current = $_POST['current_password'] ?? '';
$new     = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($new !== $confirm) {
    header("Location: ganti-pw-page.php?error=confirm&from=$from");
    exit;
}

if (strlen($new) < 8) {
    header("Location: ganti-pw-page.php?error=length&from=$from");
    exit;
}

$query = "SELECT passwordPelanggan FROM tbPelanggan WHERE usernamePelanggan = ?";
$stmt = mysqli_prepare($conn, $query);
if (!$stmt) die("Prepare failed: " . mysqli_error($conn));

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: ganti-pw-page.php?error=wrong&from=$from");
    exit;
}

$dbPassword = $data['passwordPelanggan'] ?? '';

$valid = (substr($dbPassword,0,4) === '$2y$') 
    ? password_verify($current, $dbPassword)
    : ($current === $dbPassword);

if (!$valid) {
    header("Location: ganti-pw-page.php?error=wrong&from=$from");
    exit;
}

$newHash = password_hash($new, PASSWORD_DEFAULT);

$stmtUpdate = mysqli_prepare($conn, "UPDATE tbPelanggan SET passwordPelanggan = ? WHERE usernamePelanggan = ?");
if (!$stmtUpdate) die("Prepare update failed: " . mysqli_error($conn));

mysqli_stmt_bind_param($stmtUpdate, "ss", $newHash, $username);

if (mysqli_stmt_execute($stmtUpdate)) {
    session_regenerate_id(true);

    $redirect = ($from === 'edit') 
        ? "edit-profile-page.php?success=password" 
        : "ganti-pw-page.php?success=password";

    header("Location: $redirect");
    exit;
}