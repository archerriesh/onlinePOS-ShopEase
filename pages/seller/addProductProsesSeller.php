<?php
session_start();
require '../../includes/dbOnlinePOS.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: addProduct-seller.php");
    exit;
}

// ambil id penjual dari session
$idPenjual = $_SESSION['idPenjual'] ?? null;
if (!$idPenjual) {
    die("Session idPenjual tidak ditemukan");
}

// ambil data dari form
$namaProduk     = $_POST['namaProduk'];
$kategoriProduk = $_POST['kategoriProduk'];
$stok           = (int) $_POST['stok'];
$harga          = (int) $_POST['harga'];
$keterangan     = $_POST['keterangan'];

// insert ke database
$query = "INSERT INTO tbproduk 
          (namaProduk, kategoriProduk, stok, harga, keterangan, idPenjual)
          VALUES (?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    die("Prepare gagal: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $stmt,
    "ssiiss",
    $namaProduk,
    $kategoriProduk,
    $stok,
    $harga,
    $keterangan,
    $idPenjual
);

if (!mysqli_stmt_execute($stmt)) {
    die("Execute gagal: " . mysqli_stmt_error($stmt));
}

// ambil id produk terakhir
$idProduk = mysqli_insert_id($conn);

// upload gambar (opsional)
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
            $folder . $idProduk . '.' . $ext
        );
    }
}

// redirect sukses
header("Location: index-seller.php?success=1");
exit;
