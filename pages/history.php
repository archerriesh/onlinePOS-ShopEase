<?php
session_start();

if (!isset($_SESSION['idPelanggan'])) {
    header("Location: sign-in-page.php");
    exit;
}

include '../includes/dbOnlinePOS.php';

$idPelanggan = $_SESSION['idPelanggan'];
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
    SELECT DISTINCT
        t.idTransaksi,
        t.tglTransaksi
    FROM tbTransaksi t
    JOIN tbTransaksiPenjual tp ON t.idTransaksi = tp.idTransaksi
    WHERE t.idPelanggan = ?
    $whereStatus
    ORDER BY t.tglTransaksi DESC
";

$stmtTrx = mysqli_prepare($conn, $sqlTrx);
mysqli_stmt_bind_param($stmtTrx, "s", $idPelanggan);
mysqli_stmt_execute($stmtTrx);
$transaksi = mysqli_stmt_get_result($stmtTrx);

$stmtToko = mysqli_prepare($conn, "
    SELECT
        tp.idTrxPenjual,
        tp.idPenjual,
        pj.namaPenjual,
        tp.totalPenjual,
        tp.statusPesanan
    FROM tbTransaksiPenjual tp
    JOIN tbPenjual pj ON tp.idPenjual = pj.idPenjual
    WHERE tp.idTransaksi = ?
");

$stmtDetail = mysqli_prepare($conn, "
    SELECT
        d.hargaSatuan,
        d.jumlah,
        p.namaProduk
    FROM tbDetTransaksi d
    JOIN tbProduk p ON d.idProduk = p.idProduk
    WHERE d.idTransaksi = ?
      AND p.idPenjual = ?
");

$pageCSS = '../css/history.css';
include '../includes/header-main.php';
?>

<div class="history-page">
    <div class="shop-header">

        <div class="order-tabs">
            <ul>
                <li><a href="?tab=all" class="tab-link <?= $tab=='all'?'active':'' ?>">All</a></li>
                <li><a href="?tab=topay" class="tab-link <?= $tab=='topay'?'active':'' ?>">To Pay</a></li>
                <li><a href="?tab=toship" class="tab-link <?= $tab=='toship'?'active':'' ?>">To Ship</a></li>
                <li><a href="?tab=toreceive" class="tab-link <?= $tab=='toreceive'?'active':'' ?>">To Receive</a></li>
                <li><a href="?tab=completed" class="tab-link <?= $tab=='completed'?'active':'' ?>">Completed</a></li>
            </ul>
        </div>

        <?php if (mysqli_num_rows($transaksi) === 0): ?>
            <p class="text-center mt-5">TIDAK ADA TRANSAKSI</p>
        <?php endif; ?>

        <?php while ($trx = mysqli_fetch_assoc($transaksi)) { ?>

            <?php
            mysqli_stmt_bind_param($stmtToko, "s", $trx['idTransaksi']);
            mysqli_stmt_execute($stmtToko);
            $tokos = mysqli_stmt_get_result($stmtToko);
            ?>

            <?php while ($toko = mysqli_fetch_assoc($tokos)) { ?>
                <div class="order-card">

                    <div class="store-name">
                        <?= htmlspecialchars($toko['namaPenjual']) ?>
                    </div>

                    <?php
                    mysqli_stmt_bind_param(
                        $stmtDetail,
                        "ss",
                        $trx['idTransaksi'],
                        $toko['idPenjual']
                    );
                    mysqli_stmt_execute($stmtDetail);
                    $detail = mysqli_stmt_get_result($stmtDetail);

                    $items = [];
                    while ($row = mysqli_fetch_assoc($detail)) {
                        $items[] = $row;
                    }
                    ?>

                    <?php foreach ($items as $i => $item) { ?>
                        <div class="product-item">
                            <img src="../foto/produk/default.png" alt="">
                            <div class="product-info">
                                <p class="product-name"><?= htmlspecialchars($item['namaProduk']) ?></p>
                            </div>
                            <div class="product-qty"><?= $item['jumlah'] ?></div>
                            <div class="product-price">
                                Rp<?= number_format($item['hargaSatuan'], 0, ',', '.') ?>
                            </div>
                        </div>

                        <?php if ($i < count($items) - 1): ?>
                            <div class="divider"></div>
                        <?php endif; ?>
                    <?php } ?>

                    <div class="order-footer">
                        <div class="total-text">
                            Total:
                            <span>Rp<?= number_format($toko['totalPenjual'], 0, ',', '.') ?></span>
                        </div>

                        <?php if ($toko['statusPesanan'] === 'Selesai'): ?>
                            <a href="nulis-review.php?id=<?= $toko['idTrxPenjual'] ?>" class="review-btn">
                                Review
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            <?php } ?>
        <?php } ?>

    </div>
</div>

<?php include '../includes/footer.php'; ?>