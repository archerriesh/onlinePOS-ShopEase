<?php
session_start();
require '../../includes/dbOnlinePOS.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: addProduct-seller.php");
    exit;
}

// ambil id penjual
$idPenjual = $_SESSION['idPenjual'] ?? null;
if (!$idPenjual) {
    die("Session idPenjual tidak ditemukan");
}

$namaProduk     = $_POST['namaProduk'];
$kategoriProduk = $_POST['kategoriProduk'];
$stok           = (int) $_POST['stok'];
$harga          = (float) $_POST['harga'];
$keterangan     = $_POST['keterangan'];

$query = "CALL sp_insert_produk(?, ?, ?, ?, ?, ?)";
$stmt  = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Prepare gagal: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $stmt,
    "sssids",
    $idPenjual,
    $namaProduk,
    $kategoriProduk,
    $stok,
    $harga,
    $keterangan
);

mysqli_stmt_execute($stmt);


$result   = mysqli_stmt_get_result($stmt);
$row      = mysqli_fetch_assoc($result);
$idProduk = $row['idProduk'] ?? null;


mysqli_stmt_free_result($stmt);
mysqli_next_result($conn);
mysqli_stmt_close($stmt);

if (!$idProduk) {
    die("Gagal mendapatkan idProduk");
}

// upload gambar
if (!empty($_FILES['gambar']['name'])) {
    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($ext, $allowed)) {
        $folder = "../../foto/produk/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        move_uploaded_file(
            $_FILES['gambar']['tmp_name'],
            $folder . $idProduk . "." . $ext
        );
    }
}

// notif sukses
$_SESSION['success'] = "Produk berhasil ditambahkan!";
header("Location: products.php");
exit;
