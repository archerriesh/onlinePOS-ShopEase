<?php
session_start();

if (!isset($_SESSION['idPelanggan'])) {
    header("Location: sign-in-page.php");
    exit;
}

include '../includes/dbOnlinePOS.php';

$idPelanggan = $_SESSION['idPelanggan'];

$stmtTrx = mysqli_prepare($conn, "
    SELECT idTransaksi, tglTransaksi
    FROM tbTransaksi
    WHERE idPelanggan = ?
    ORDER BY tglTransaksi DESC
");

if (!$stmtTrx) {
    die(mysqli_error($conn));
}

mysqli_stmt_bind_param($stmtTrx, "s", $idPelanggan);
mysqli_stmt_execute($stmtTrx);
$transaksi = mysqli_stmt_get_result($stmtTrx);

if (mysqli_num_rows($transaksi) === 0) {
    echo "<p style='color:red'>TIDAK ADA TRANSAKSI</p>";
}

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
                <li><a href="#" class="tab-link active">All</a></li>
                <li><a href="#" class="tab-link">To Pay</a></li>
                <li><a href="#" class="tab-link">To Ship</a></li>
                <li><a href="#" class="tab-link">To Receive</a></li>
                <li><a href="#" class="tab-link">Completed</a></li>
            </ul>
        </div>

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
                                <p class="product-name">
                                    <?= htmlspecialchars($item['namaProduk']) ?>
                                </p>
                            </div>
                            <div class="product-qty">
                                <?= $item['jumlah'] ?>
                            </div>
                            <div class="product-price">
                                Rp<?= number_format($item['hargaSatuan'], 0, ',', '.') ?>
                            </div>
                        </div>

                        <?php if ($i < count($items) - 1) { ?>
                            <div class="divider"></div>
                        <?php } ?>
                    <?php } ?>

                    <div class="order-footer">
                        <div class="total-text">
                            Total:
                            <span>
                                Rp<?= number_format($toko['totalPenjual'], 0, ',', '.') ?>
                            </span>
                        </div>

                        <?php if ($toko['statusPesanan'] === 'Selesai') { ?>
                            <a href="nulis-review.php?id=<?= $toko['idTrxPenjual'] ?>" class="review-btn">
                                Review
                            </a>
                        <?php } ?>
                    </div>

                </div>
            <?php } ?>

        <?php } ?>

    </div>
</div>

<?php include '../includes/footer.php'; ?>