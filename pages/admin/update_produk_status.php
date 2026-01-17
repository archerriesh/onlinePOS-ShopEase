<?php
include __DIR__ . '/../../includes/dbOnlinePOS.php';

$id = $_POST['id'] ?? '';
$action = $_POST['action'] ?? '';

header('Content-Type: application/json');

if (empty($id) || empty($action)) {
    echo json_encode(['status' => 'error', 'message' => 'Data ID atau Aksi tidak diterima']);
    exit;
}

$statusBaru = ($action === 'nonaktif') ? 'N' : 'Y';
$query = "UPDATE tbproduk SET statusAktif = ? WHERE idProduk = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $statusBaru, $id);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) >= 0) {
        echo json_encode([
            'status' => 'success', 
            'message' => "Produk " . $id . " berhasil diubah menjadi " . ($statusBaru === 'N' ? 'Nonaktif' : 'Aktif')
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah status di database']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
}