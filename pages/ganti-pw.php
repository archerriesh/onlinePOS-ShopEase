<?php
session_start();
require '../includes/dbOnlinePOS.php';
mysqli_set_charset($conn, 'utf8mb4'); 

if (!isset($_SESSION['login'])) {
    header("Location: sign-in-page.php");
    exit;
}

$username = $_SESSION['username'];
$role     = $_SESSION['role'];
$from     = $_POST['from'] ?? 'profile';
$current  = $_POST['current_password'] ?? '';
$new      = $_POST['new_password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if ($new !== $confirm) {
    header("Location: ganti-pw-page.php?error=confirm&from=$from");
    exit;
}

if (strlen($new) < 8) {
    header("Location: ganti-pw-page.php?error=length&from=$from");
    exit;
}

switch ($role) {
    case 'pelanggan':
        $table = "tbPelanggan";
        $colID = "idPelanggan";
        $colUser = "usernamePelanggan";
        $colPass = "passwordPelanggan";
        break;
    case 'penjual':
        $table = "tbPenjual"; 
        $colID = "idPenjual"; 
        $colUser = "usernamePenjual"; 
        $colPass = "passwordPenjual"; 
        break;
    case 'admin':
        $table = "tbAdmin"; 
        $colID = "idAdmin"; 
        $colUser = "username"; 
        $colPass = "password"; 
        break;
}

$query = "SELECT $colID, $colPass FROM $table WHERE $colUser = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: ganti-pw-page.php?error=wrong&from=$from");
    exit;
}

$dbPassword = trim($data[$colPass]);
$idUser     = $data[$colID];

if (substr($dbPassword, 0, 4) === '$2y$') {
    $valid = password_verify($current, $dbPassword);
} else {
    $valid = (trim($current) === $dbPassword);
}

if (!$valid) {
    header("Location: ganti-pw-page.php?error=wrong&from=$from");
    exit;
}

$newHash = password_hash($new, PASSWORD_DEFAULT);
mysqli_stmt_close($stmt); 

try {
    $stmtUpdate = mysqli_prepare($conn, "UPDATE $table SET $colPass = ? WHERE $colID = ?");
    mysqli_stmt_bind_param($stmtUpdate, "ss", $newHash, $idUser);

    if (mysqli_stmt_execute($stmtUpdate)) {
        session_regenerate_id(true); 
        
        $redirect = ($from === 'edit') 
            ? "edit-profile-page.php?success=password" 
            : "ganti-pw-page.php?success=password";

        header("Location: $redirect");
        exit;
    } else {
        throw new Exception("Update failed");
    }
} catch (Exception $e) {
    header("Location: ganti-pw-page.php?error=updatefail&from=$from");
    exit;
}