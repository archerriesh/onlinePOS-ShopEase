<?php
session_start();
require '../includes/dbOnlinePOS.php';

if (!isset($_SESSION['login'])) {
    header("Location: sign-in-page.php");
    exit;
}

$old_username = $_SESSION['username'];
$role = $_SESSION['role'];

$nama = $_POST['nama'];
$kontak = $_POST['kontak'];
$alamat = $_POST['alamat'];
$new_username = $_POST['new_username'];

switch ($role) {
    case 'pelanggan':
        $query = "UPDATE tbPelanggan SET namaPelanggan = ?, kontakPelanggan = ?, alamatPelanggan = ?, usernamePelanggan = ? WHERE usernamePelanggan = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $nama, $kontak, $alamat, $new_username, $old_username);
        break;

    case 'penjual':
        $kategori = $_POST['kategoriToko'];
        $query = "UPDATE tbPenjual SET namaPenjual = ?, kontakPenjual = ?, alamatPenjual = ?, usernamePenjual = ?, kategoriToko = ? WHERE usernamePenjual = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $nama, $kontak, $alamat, $new_username, $kategori, $old_username);
        break;

    case 'admin':
        $spec = $_POST['specification'];
        $query = "UPDATE tbAdmin SET namaAdmin = ?, email = ?, alamat = ?, username = ?, specification = ? WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $nama, $kontak, $alamat, $new_username, $spec, $old_username);
        break;
}

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['username'] = $new_username;
    header("Location: profile.php?update=success");
} else {
    echo "Error updating record: " . mysqli_error($conn);
}
exit;