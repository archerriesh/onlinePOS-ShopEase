<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require '../../includes/dbOnlinePOS.php';

// Cek apakah request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index-seller.php');
    exit;
}

// Ambil ID produk dari form
$idProduk = isset($_POST['idProduk']) ? intval($_POST['idProduk']) : 0;

if ($idProduk <= 0) {
    $_SESSION['error'] = "ID produk tidak valid";
    header('Location: index-seller.php');
    exit;
}

// Mulai transaksi
mysqli_begin_transaction($conn);

try {
    // Query untuk delete produk
    $query = "DELETE FROM tbproduk WHERE idProduk = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        throw new Exception("Gagal mempersiapkan statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $idProduk);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Gagal menghapus produk: " . mysqli_stmt_error($stmt));
    }
    
    // Cek apakah ada baris yang terhapus
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        mysqli_commit($conn);
        $_SESSION['success'] = "Produk berhasil dihapus!";
    } else {
        mysqli_rollback($conn);
        $_SESSION['error'] = "Produk tidak ditemukan";
    }
    
    mysqli_stmt_close($stmt);
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['error'] = $e->getMessage();
}

mysqli_close($conn);

// Redirect kembali ke halaman index
header('Location: index-seller.php');
exit;
?>