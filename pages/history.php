<?php
session_start();

if (!isset($_SESSION['idPelanggan'])) {
    header("Location: sign-in-page.php");
    exit;
}

include '../includes/dbOnlinePOS.php';

$idPelanggan = $_SESSION['idPelanggan'];

$stmt = mysqli_prepare($conn, "
        SELECT DISTINCT
        t.idTransaksi,
        t.totalTransaksi,
        t.statusTransaksi,
        t.statusPengiriman,
        t.tglTransaksi,
        pj.idPenjual,
        pj.namaPenjual
    FROM tbTransaksi t
    JOIN tbDetTransaksi dt ON t.idTransaksi = dt.idTransaksi
    JOIN tbProduk p ON dt.idProduk = p.idProduk
    JOIN tbPenjual pj ON p.idPenjual = pj.idPenjual
    WHERE t.idPelanggan = ?
    ORDER BY t.tglTransaksi DESC
");

if (!$stmt) {
    die(mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "s", $idPelanggan);
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);

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

        <?php while ($trx = mysqli_fetch_assoc($query)) { ?>
            <div class="order-card">

                <div class="store-name">
                    <?= htmlspecialchars($trx['namaPenjual']) ?>
                </div>

                <?php 
                    $stmtDetail = mysqli_prepare($conn, "
                        SELECT 
                            d.idDetail,
                            d.hargaSatuan,
                            d.jumlah,
                            d.idReview,
                            p.namaProduk
                        FROM tbDetTransaksi d
                        JOIN tbProduk p ON d.idProduk = p.idProduk
                        WHERE d.idTransaksi = ?
                        AND p.idPenjual = ?
                    ");

                    if (!$stmtDetail) {
                        die(mysqli_error($conn));
                    }

                    mysqli_stmt_bind_param(
                        $stmtDetail,
                        "ss",
                        $trx['idTransaksi'],
                        $trx['idPenjual']
                    );
                    mysqli_stmt_execute($stmtDetail);
                    $detail = mysqli_stmt_get_result($stmtDetail);
                ?>

                <?php while ($item = mysqli_fetch_assoc($detail)) { ?>
                    <div class="product-item">
                        <img src="../foto/produk/default.png" alt="<?= htmlspecialchars($item['namaProduk']) ?>">
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
                <?php } ?>

                <div class="order-footer">
                    <div class="total-text">
                        Total Order:
                        <span>
                            Rp<?= number_format($trx['totalTransaksi'], 0, ',', '.') ?>
                        </span>
                    </div>

                        <a href="nulis-review.php?id=<?= $trx['idTransaksi'] ?>" class="review-btn">
                            Review
                        </a>
                </div>

            </div>
        <?php } ?>

    </div>
</div>

<?php include '../includes/footer.php'; ?>