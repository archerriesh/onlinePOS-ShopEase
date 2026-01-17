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

// ambil data dari form
$namaProduk     = $_POST['namaProduk'];
$kategoriProduk = $_POST['kategoriProduk'];
$stok           = (int) $_POST['stok'];
$harga          = (int) $_POST['harga'];
$keterangan     = $_POST['keterangan'];


// generate idProduk baru
$cek = mysqli_query($conn, "SELECT MAX(idProduk) AS last_id FROM tbproduk");
$row = mysqli_fetch_assoc($cek);

if ($row['last_id']) {
    $angka = (int) substr($row['last_id'], 2); 
    $angka++;
    $idProduk = "PR" . str_pad($angka, 3, "0", STR_PAD_LEFT);
} else {
    $idProduk = "PR001"; 
}

// insert ke database
$query = "INSERT INTO tbproduk 
          (idProduk, namaProduk, kategoriProduk, stok, harga, keterangan, idPenjual)
          VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    die("Prepare gagal: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $stmt,
    "sssiiss",
    $idProduk,
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

// upload gambar produk
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

// tampilin notif sukses
$_SESSION['success'] = "Produk berhasil ditambahkan!";
header("Location: index-seller.php");
exit;
