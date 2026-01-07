<?php
session_start();
require '../includes/dbOnlinePOS.php';

if (!isset($_SESSION['username'])) {
    header("Location: sign-in.php");
    exit;
}

$username = $_SESSION['username'];
$idPelanggan = $_SESSION['idPelanggan'];
$from = $_POST['from'] ?? 'profile';

/* Ambil field dari form */
$current = $_POST['current_password'] ?? '';
$new     = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

/* Validasi */
if ($new !== $confirm) {
    header("Location: ganti-pw-page.php?error=confirm&from=$from");
    exit;
}

if (strlen($new) < 8) {
    header("Location: ganti-pw-page.php?error=length&from=$from");
    exit;
}

/* Ambil password lama dari DB */
$query = "SELECT passwordPelanggan FROM tbPelanggan WHERE idPelanggan = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $idPelanggan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: ganti-pw-page.php?error=wrong&from=$from");
    exit;
}

/* Cek password lama */
$dbPassword = $data['passwordPelanggan'];

if (substr($dbPassword,0,4) === '$2y$') {
    $valid = password_verify($current, $dbPassword);
} else {
    $valid = ($current === $dbPassword);
}

if (!$valid) {
    header("Location: ganti-pw-page.php?error=wrong&from=$from");
    exit;
}

$newHash = password_hash($new, PASSWORD_DEFAULT);
$sql = "UPDATE tbPelanggan SET passwordPelanggan='$newHash' WHERE idPelanggan='$idPelanggan'";
if (mysqli_query($conn, $sql)) {
    echo "Berhasil, affected rows: ".mysqli_affected_rows($conn);
} else {
    echo "Gagal: ".mysqli_error($conn);
}
exit;


session_regenerate_id(true);

/* ================= REDIRECT ================= */

header(
    $from === 'edit'
        ? "Location: edit-profile-page.php?success=password"
        : "Location: profile.php?success=password"
);
exit;