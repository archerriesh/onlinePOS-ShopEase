<?php
session_start();
if (!isset($_SESSION['idPenjual'])) {
    header("Location: ../sign-in-page.php");
    exit;
}

include '../../includes/dbOnlinePOS.php';

$idPenjual = $_SESSION['idPenjual'];
$tab = $_GET['tab'] ?? 'all';

$whereStatus = "";
if ($tab === 'topay') {
    $whereStatus = "AND tp.statusPesanan = 'Menunggu Pembayaran'";
} elseif ($tab === 'toship') {
    $whereStatus = "AND tp.statusPengiriman = 'Menunggu Pengiriman'";
} elseif ($tab === 'toreceive') {
    $whereStatus = "AND tp.statusPengiriman = 'Dikirim'";
} elseif ($tab === 'completed') {
    $whereStatus = "AND tp.statusPesanan = 'Selesai'";
}

$sqlTrx = "
    SELECT tp.*, t.tglTransaksi, pl.namaPelanggan,
           (SELECT SUM(d.hargaSatuan * d.jumlah) 
            FROM tbDetTransaksi d 
            JOIN tbProduk p ON d.idProduk = p.idProduk 
            WHERE d.idTransaksi = tp.idTransaksi AND p.idPenjual = tp.idPenjual) as hitungTotal
    FROM tbTransaksiPenjual tp
    JOIN tbTransaksi t ON tp.idTransaksi = t.idTransaksi
    JOIN tbPelanggan pl ON t.idPelanggan = pl.idPelanggan
    WHERE tp.idPenjual = ?
    $whereStatus
    ORDER BY t.tglTransaksi DESC
";

$stmtTrx = mysqli_prepare($conn, $sqlTrx);
mysqli_stmt_bind_param($stmtTrx, "s", $idPenjual);
mysqli_stmt_execute($stmtTrx);
$transaksi = mysqli_stmt_get_result($stmtTrx);

$stmtDetail = mysqli_prepare($conn, "
    SELECT d.hargaSatuan, d.jumlah, p.namaProduk, p.idProduk
    FROM tbDetTransaksi d
    JOIN tbProduk p ON d.idProduk = p.idProduk
    WHERE d.idTransaksi = ? AND p.idPenjual = ?
");

$pageCSS = '../../css/history.css'; 
include '../../includes/header-seller.php';
?>

<div class="history-page">
    <div class="container py-5">
        <h2 class="text-center mb-5 fw-bold" style="color: #4a4431; letter-spacing: 1px;">Incoming Orders</h2>
        
        <div class="d-flex justify-content-center mb-5">
            <ul class="nav nav-pills border-0">
                <li class="nav-item"><a href="?tab=all" class="nav-link <?= $tab=='all'?'active':'' ?>">All</a></li>
                <li class="nav-item"><a href="?tab=topay" class="nav-link <?= $tab=='topay'?'active':'' ?>">Waiting for payment</a></li>
                <li class="nav-item"><a href="?tab=toship" class="nav-link <?= $tab=='toship'?'active':'' ?>">To Ship</a></li>
                <li class="nav-item"><a href="?tab=toreceive" class="nav-link <?= $tab=='toreceive'?'active':'' ?>">Shipped</a></li>
                <li class="nav-item"><a href="?tab=completed" class="nav-link <?= $tab=='completed'?'active':'' ?>">Completed</a></li>
            </ul>
        </div>
        
        <?php if (mysqli_num_rows($transaksi) === 0): ?>
            <div class="text-center py-5">
                <i class="bi bi-box-seam display-1 text-muted opacity-25"></i>
                <p class="mt-3 text-muted">No orders found in this category.</p>
            </div>
        <?php endif; ?>

    <?php while ($trx = mysqli_fetch_assoc($transaksi)): 
        mysqli_stmt_bind_param($stmtDetail, "ss", $trx['idTransaksi'], $idPenjual);
        mysqli_stmt_execute($stmtDetail);
        $details = mysqli_stmt_get_result($stmtDetail);
    ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><strong>Buyer:</strong> <?= htmlspecialchars($trx['namaPelanggan']) ?></span>
                <span class="badge bg-info"><?= $trx['statusPesanan'] ?> | <?= $trx['statusPengiriman'] ?? 'Pending' ?></span>
            </div>
            <div class="card-body">
                <?php while ($item = mysqli_fetch_assoc($details)): ?>
                    <div class="product-item d-flex align-items-center mb-3">
                        <?php 
                            $basePath = "../../foto/produk/"; 
                            $extensions = ['webp', 'jpg', 'jpeg', 'png'];
                            $gambarTampil = "../../assets/img/default.jpg";

                            foreach ($extensions as $ext) {
                                $fileCek = $basePath . $item['idProduk'] . "." . $ext;
                                if (file_exists($fileCek)) {
                                    $gambarTampil = $fileCek;
                                    break;
                                }
                            }
                        ?>

                        <div class="product-img-wrapper me-3">
                            <img src="<?= $gambarTampil; ?>" 
                                alt="<?= htmlspecialchars($item['namaProduk']); ?>" 
                                class="img-thumbnail rounded-3" 
                                style="width: 70px; height: 70px; object-fit: cover; border: 1px solid #f1ece8;">
                        </div>

                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold" style="color: #4a4431;"><?= htmlspecialchars($item['namaProduk']) ?></h6>
                            <small class="text-muted"><?= $item['jumlah'] ?> x Rp <?= number_format($item['hargaSatuan'], 0, ',', '.') ?></small>
                        </div>
                    </div>
                <?php endwhile; ?>
                
                <hr>
                
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0">Total income: <strong>Rp<?= number_format($trx['hitungTotal'], 0, ',', '.') ?></strong></p>
                    </div>
                    
                    <form action="update-status-pesanan.php" method="POST" class="d-flex gap-2">
                        <input type="hidden" name="idTrxPenjual" value="<?= $trx['idTrxPenjual'] ?>">
                        
                        <select name="statusPengiriman" class="form-select form-select-sm">
                            <option value="Menunggu Pengiriman" <?= $trx['statusPengiriman'] == 'Menunggu Pengiriman' ? 'selected' : '' ?>>Menunggu Kirim</option>
                            <option value="Dikirim" <?= $trx['statusPengiriman'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                            <option value="Sampai Tujuan" <?= $trx['statusPengiriman'] == 'Sampai Tujuan' ? 'selected' : '' ?>>Sampai</option>
                        </select>

                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php include '../../includes/footer.php'; ?>