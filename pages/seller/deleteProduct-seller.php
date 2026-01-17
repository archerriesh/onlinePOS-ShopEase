<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require '../../includes/dbOnlinePOS.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index-seller.php');
    exit;
}

$idProduk = $_POST['idProduk'] ?? '';

if (empty($idProduk)) {
    $_SESSION['error'] = "ID produk tidak valid";
    header('Location: index-seller.php');
    exit;
}

//Hapus foto dulu
$folder = "../../foto/produk/";
$files = glob($folder . $idProduk . ".*");

foreach ($files as $file) {
    if (file_exists($file)) {
        unlink($file);
    }
}

//Hapus data di database
$query = "DELETE FROM tbproduk WHERE idProduk = ?";
$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "s", $idProduk);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    $_SESSION['success'] = "Produk berhasil dihapus!";
} else {
    $_SESSION['error'] = "Produk tidak ditemukan";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

header('Location: index-seller.php');
exit;
?>
