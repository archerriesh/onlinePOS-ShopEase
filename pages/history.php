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
    $whereStatus = "AND t.statusTransaksi = 'Menunggu Pembayaran'";
} elseif ($tab === 'toship') {
    $whereStatus = "AND tp.statusPengiriman = 'Dikemas' AND t.statusTransaksi = 'Sudah Bayar'";
} elseif ($tab === 'toreceive') {
    $whereStatus = "AND tp.statusPengiriman = 'Dikirim'";
} elseif ($tab === 'completed') {
    $whereStatus = "AND tp.statusPesanan = 'Selesai'";
}

$sqlTrx = "
    SELECT DISTINCT t.idTransaksi, t.tglTransaksi, t.statusTransaksi
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

$sqlToko = "
    SELECT 
        tp.idTrxPenjual, tp.idPenjual, pj.namaPenjual, 
        tp.totalPenjual, tp.statusPesanan, tp.statusPengiriman,
        tp.subTotal, tp.biayaAdmin, tp.ongkir, tp.potonganPromo
    FROM tbTransaksiPenjual tp
    JOIN tbPenjual pj ON tp.idPenjual = pj.idPenjual
    JOIN tbTransaksi t ON tp.idTransaksi = t.idTransaksi
    WHERE tp.idTransaksi = ? $whereStatus
";
$stmtToko = mysqli_prepare($conn, $sqlToko);

$stmtDetail = mysqli_prepare($conn, "
    SELECT d.idDetail, d.idProduk, d.hargaSatuan, d.jumlah, p.namaProduk
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
            <div class="text-center py-5">
                <i class="bi bi-box-seam display-1 text-muted opacity-25"></i>
                <p class="mt-3 text-muted">There's no order yet.</p>
            </div>
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
                                        <span>Rp<?= number_format($toko['potonganPromo'],0,',','.') ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="total-text">
                                Total:
                                <?php 
                                    $totalTampil = $toko['totalPenjual'];
                                    if (is_null($totalTampil)) {
                                        $totalTampil = ($toko['subTotal'] + $toko['biayaAdmin'] + $toko['ongkir']) - ($toko['potonganPromo'] ?? 0);
                                    }
                                ?>
                                <span>Rp<?= number_format($totalTampil, 0, ',', '.') ?></span>
                            </div>

                            <div class="rvw">
                                <?php if ($toko['statusPesanan'] === 'Selesai'): ?>
                                    <button type="button" 
                                            class="review-btn btn-trigger-review" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#reviewModal"
                                            data-iddetail="<?= $item['idDetail'] ?>" 
                                            data-idproduk="<?= $item['idProduk'] ?>"
                                            data-nama="<?= htmlspecialchars($item['namaProduk']) ?>">
                                        Review 
                                    </button>
                                <?php endif; ?>

                                <?php if ($trx['statusTransaksi'] === 'Menunggu Pembayaran'): ?>
                                    <button type="button" 
                                            class="pay-btn btn-trigger-pay" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#payModal"
                                            data-idtrx="<?= $trx['idTransaksi'] ?>"
                                            style="background-color: #ba704a; color: white; border: none; padding: 10px 25px; border-radius: 8px; font-weight: 600;">
                                        Pay Now
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div> 

<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 20px; padding: 20px;">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Add a Review</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <p class="text-muted" id="modalProductName">Review for product...</p>
        
        <form action="nulis-review.php" method="POST" id="formReviewModal">
            <input type="hidden" name="idDetail" id="modalIdDetail">
            <input type="hidden" name="idProduk" id="modalIdProduk">
            <input type="hidden" name="rating" id="modalRatingValue" value="0">

            <div class="review-stars mb-4" style="font-size: 40px; cursor: pointer;">
                <span class="star-item" data-value="1" style="color: #ccc;">★</span>
                <span class="star-item" data-value="2" style="color: #ccc;">★</span>
                <span class="star-item" data-value="3" style="color: #ccc;">★</span>
                <span class="star-item" data-value="4" style="color: #ccc;">★</span>
                <span class="star-item" data-value="5" style="color: #ccc;">★</span>
            </div>

            <textarea name="komentar" class="form-control mb-3" placeholder="Provide a detailed review" rows="4" required style="border-radius: 12px; background: #f8f9fa;"></textarea>
            
            <button type="submit" class="btn w-100 py-2 fw-bold" style="background: #ba704a; color: white; border-radius: 10px;">Send Review</button>
        </form>
        </div>
    </div>
  </div>
</div>

<div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; padding: 20px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Confirm Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p>Are you sure you want to confirm payment for transaction <strong id="displayIdTrx"></strong>?</p>
                
                <form action="proses-bayar.php" method="POST">
                    <input type="hidden" name="idTransaksi" id="modalIdTrxPay">
                    <input type="hidden" name="statusBaru" value="Sudah Bayar">
                    
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                        <button type="submit" class="btn btn-success w-100" style="border-radius: 10px; background-color: #ba704a; border: none;">Yes, Confirm</button>
                    </div>
                </form>
            </div>
        </div>
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

document.querySelectorAll('.btn-trigger-review').forEach(btn => {
    btn.addEventListener('click', function() {
        const idDetail = this.getAttribute('data-iddetail');
        const idProduk = this.getAttribute('data-idproduk');
        const namaProd = this.getAttribute('data-nama');
        
        document.getElementById('modalIdDetail').value = idDetail;
        document.getElementById('modalIdProduk').value = idProduk;
        document.getElementById('modalProductName').textContent = "Rating for: " + namaProd;
        
        resetStarsModal();
        document.getElementById('modalRatingValue').value = 0;
    });
});

const modalStars = document.querySelectorAll('.star-item');
const modalRatingInput = document.getElementById('modalRatingValue');

modalStars.forEach(star => {
    star.addEventListener('click', function() {
        const val = this.dataset.value;
        modalRatingInput.value = val;
        resetStarsModal();
        highlightStarsModal(val);
    });

    star.addEventListener('mouseover', function() {
        resetStarsModal();
        highlightStarsModal(this.dataset.value);
    });
});

document.querySelector('.review-stars').addEventListener('mouseleave', function() {
    resetStarsModal();
    if(modalRatingInput.value > 0) {
        highlightStarsModal(modalRatingInput.value);
    }
});

function highlightStarsModal(val) {
    modalStars.forEach(s => {
        if(parseInt(s.dataset.value) <= parseInt(val)) s.style.color = "#ffcc00";
    });
}

function resetStarsModal() {
    modalStars.forEach(s => s.style.color = "#ccc");
}

document.querySelectorAll('.btn-trigger-pay').forEach(btn => {
    btn.addEventListener('click', function() {
        const idTrx = this.getAttribute('data-idtrx');
        document.getElementById('modalIdTrxPay').value = idTrx;
        document.getElementById('displayIdTrx').textContent = idTrx;
    });
});
</script>

<?php include '../includes/footer.php'; ?>