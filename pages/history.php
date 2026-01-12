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
    SELECT DISTINCT t.idTransaksi, t.tglTransaksi
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
        tp.statusPesanan, 
        tp.subTotal,
        tp.biayaAdmin,
        tp.ongkir,
        tp.potonganPromo
    FROM tbTransaksiPenjual tp
    JOIN tbPenjual pj ON tp.idPenjual = pj.idPenjual
    WHERE tp.idTransaksi = ?
");

$stmtDetail = mysqli_prepare($conn, "
    SELECT d.idProduk, d.hargaSatuan, d.jumlah, p.namaProduk
    FROM tbDetTransaksi d
    JOIN tbProduk p ON d.idProduk = p.idProduk
    WHERE d.idTransaksi = ? AND p.idPenjual = ?
");

$pageCSS = '../css/history.css';
include '../includes/header-main.php';

$basePath = "../foto/produk/";
$extensions = ['webp', 'jpg', 'jpeg','png'];
$defaultImage = "../assets/img/default.jpg";
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
            <p class="text-center mt-5">There's no order yet.</p>
        <?php endif; ?>

        <?php while ($trx = mysqli_fetch_assoc($transaksi)) { 
            mysqli_stmt_bind_param($stmtToko, "s", $trx['idTransaksi']);
            mysqli_stmt_execute($stmtToko);
            $tokos = mysqli_stmt_get_result($stmtToko);
        ?>

            <?php while ($toko = mysqli_fetch_assoc($tokos)) { 
                mysqli_stmt_bind_param($stmtDetail, "ss", $trx['idTransaksi'], $toko['idPenjual']);
                mysqli_stmt_execute($stmtDetail);
                $detail = mysqli_stmt_get_result($stmtDetail);

                $items = [];
                while ($row = mysqli_fetch_assoc($detail)) {
                    $items[] = $row;
                }
            ?>
                <div class="order-card">
                    <div class="store-name">
                        <?= htmlspecialchars($toko['namaPenjual']) ?>
                    </div>

                    <?php foreach ($items as $i => $item) { 
                        $imgPath = $defaultImage;
                        foreach ($extensions as $ext) {
                            $try = $basePath . $item['idProduk'] . '.' . $ext;
                            if (file_exists($try)) {
                                $imgPath = $try;
                                break;
                            }
                        }
                    ?>
                        <div class="product-item">
                            <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($item['namaProduk']) ?>">
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
                        <div class="footer-right">
                            <div class="detail">
                                <a class="toggle-details-btn">See Details</a>
                                <div class="order-details">
                                    <div class="detail-row">
                                        <span>Subtotal</span>
                                        <span>Rp<?= number_format($toko['subTotal'],0,',','.') ?></span>
                                    </div>
                                    <div class="detail-row">
                                        <span>Service Fee</span>
                                        <span>Rp<?= number_format($toko['biayaAdmin'],0,',','.') ?></span>
                                    </div>
                                    <div class="detail-row">
                                        <span>Shipping Fee</span>
                                        <span>Rp<?= number_format($toko['ongkir'],0,',','.') ?></span>
                                    </div>
                                    <div class="detail-row">
                                        <span>Voucher Applied</span>
                                        <span>Rp<?= number_format($toko['ongkir'],0,',','.') ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="total-text">
                                Total:
                                <span>Rp<?= number_format($toko['totalPenjual'], 0, ',', '.') ?></span>
                            </div>

                            <?php if ($toko['statusPesanan'] === 'Selesai'): ?>
                                <a href="nulis-review.php?id=<?= $toko['idTrxPenjual'] ?>" class="review-btn">Review</a>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div> 

<script>
document.querySelectorAll('.toggle-details-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const details = this.nextElementSibling;
        if(details.style.display === 'none' || details.style.display === '') {
            details.style.display = 'block';
            this.textContent = 'Hide Details';
        } else {
            details.style.display = 'none';
            this.textContent = 'See Details';
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>