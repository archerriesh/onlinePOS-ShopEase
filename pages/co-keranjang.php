<?php
session_start();

$pageCSS = '../css/co-keranjang.css';
include '../includes/header-main.php';
include '../includes/dbOnlinePOS.php';

if (!isset($_SESSION['idPelanggan'])) {
    header("Location: sign-in-page.php");
    exit;
}

$idPelanggan = $_SESSION['idPelanggan'];

if (isset($_GET['action']) && $_GET['action'] == 'cek_promo') {
    ob_clean(); 
    header('Content-Type: application/json');
    
    $idPromo = $_GET['idPromo'] ?? '';

    if (empty($idPelanggan) || empty($idPromo)) {
        echo json_encode(['potongan' => 0]);
        exit;
    }

    $sqlFunc = "SELECT fn_promo_terpakai(?, ?, NULL) AS potongan";
    $stmtF = mysqli_prepare($conn, $sqlFunc);
    mysqli_stmt_bind_param($stmtF, "ss", $idPelanggan, $idPromo);
    mysqli_stmt_execute($stmtF);
    $resF = mysqli_stmt_get_result($stmtF);
    $rowF = mysqli_fetch_assoc($resF);

    $potongan = (float)($rowF['potongan'] ?? 0);

    echo json_encode(['potongan' => $potongan]);
    exit; 
}

if (isset($_POST['update_cart'])) {
    $idProduk = $_POST['idProduk'] ?? '';
    $qty      = (int) ($_POST['qty'] ?? 0);

    if ($idProduk !== '') {
        $stmt = mysqli_prepare($conn, "CALL sp_kelola_keranjang(?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssi", $idPelanggan, $idProduk, $qty);
        
        if (!mysqli_stmt_execute($stmt)) {
            die("Error: " . mysqli_error($conn));
        }

        while (mysqli_more_results($conn)) {
            mysqli_next_result($conn);
        }
        mysqli_stmt_close($stmt);
    }
    header("Location: co-keranjang.php");
    exit;
}

$sql = "SELECT k.idProduk, p.namaProduk, k.jumlah, k.hargaSatuan FROM tbKeranjang k JOIN tbProduk p ON k.idProduk = p.idProduk WHERE k.idPelanggan = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $idPelanggan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$currentDate = date('Y-m-d H:i:s');
$sqlPromo = "SELECT * FROM tbpromo WHERE startDate <= ? AND endDate >= ? AND statusAktif = 'Y'";
$stmtPromo = mysqli_prepare($conn, $sqlPromo);
mysqli_stmt_bind_param($stmtPromo, "ss", $currentDate, $currentDate);
mysqli_stmt_execute($stmtPromo);
$resultPromo = mysqli_stmt_get_result($stmtPromo);

$basePath   = "../foto/produk/";
$extensions = ['webp', 'jpg', 'jpeg', 'png'];
$defaultImg = "../assets/img/default.jpg";
?>

<main class="cart-page">
    <section class="layout">
        <div class="cart-items">
            <h3 class="section-title"><?= mysqli_num_rows($result); ?> items</h3>

            <?php if (mysqli_num_rows($result) === 0): ?>
                <p class="empty">Keranjang kosong.</p>
            <?php else: ?>
                <?php 
                $totalHargaSemua = 0;
                $totalItemSemua = 0;
                while ($row = mysqli_fetch_assoc($result)): 
                    $subtotal = $row['jumlah'] * $row['hargaSatuan'];
                    $totalHargaSemua += $subtotal;
                    $totalItemSemua += $row['jumlah'];

                    $gambar = $defaultImg;
                    foreach ($extensions as $ext) {
                        $file = $basePath . $row['idProduk'] . "." . $ext;
                        if (file_exists($file)) { $gambar = $file; break; }
                    }
                ?>
                <div class="cart-item" data-id="<?= $row['idProduk']; ?>">
                    <div class="item-selector">
                        <input type="checkbox" class="item-checkbox" 
                               value="<?= $row['idProduk']; ?>" 
                               data-price="<?= $row['hargaSatuan']; ?>" 
                               data-qty="<?= $row['jumlah']; ?>" checked>
                    </div>               
                    <div class="thumb"><img src="<?= $gambar; ?>"></div>
                    <div class="item-info">
                        <div class="item-name"><?= htmlspecialchars($row['namaProduk']); ?></div>
                        <form method="POST" class="qty-form">
                            <input type="hidden" name="update_cart" value="1">
                            <input type="hidden" name="idProduk" value="<?= $row['idProduk']; ?>">
                            <div class="qty">
                                <button type="button" class="btn-icon minus">−</button>
                                <input type="text" name="qty" class="qty-input" value="<?= $row['jumlah']; ?>" readonly>
                                <button type="button" class="btn-icon plus">+</button>
                            </div>
                        </form>
                    </div>
                    <div class="price">Rp <?= number_format($row['hargaSatuan'], 0, ',', '.'); ?></div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <aside class="summary-panel">
            <div class="voucher-wrapper">
                <p class="voucher-title">Voucher</p>
                <div class="voucher-box" id="voucherToggle">
                    <div class="voucher-placeholder">
                        <span id="selectedVoucherText">Pilih promo yang tersedia</span>
                    </div>
                </div>
                <div class="promo-dropdown" id="voucherContent">
                    <?php if (mysqli_num_rows($resultPromo) === 0): ?>
                        <div class="promo-item"><span>Tidak ada promo tersedia</span></div>
                    <?php else: ?>
                        <?php mysqli_data_seek($resultPromo, 0); 
                        while ($promo = mysqli_fetch_assoc($resultPromo)): 
                            $isPercent = (strpos(strtolower($promo['tipePromo']), 'diskon') !== false);
                            $label = $isPercent ? $promo['persentasePotongan']."%" : "Rp ".number_format($promo['nominalPotongan'],0,',','.');
                        ?>
                            <div class="promo-item">
                                <span><?= htmlspecialchars($promo['namaPromo']); ?> (<?= $label ?>)</span>
                                <button type="button" class="apply-btn" 
                                        data-idpromo="<?= $promo['idPromo']; ?>" 
                                        data-name="<?= htmlspecialchars($promo['namaPromo']); ?>">Gunakan</button>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="summary-header"><strong>Total <?= $totalItemSemua ?> Barang</strong></div>
            <div class="summary-details" id="summaryList">
                </div>

            <hr>
            <div class="row total">
                <span>Total Harga</span>
                <span id="grandTotalDisplay" style="color: #61593d; font-size: 18px;">Rp 0</span>
            </div>
            <button type="button" id="btnCheckout" class="checkout-btn">Checkout</button>
        </aside>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const voucherWrapper = document.querySelector('.voucher-wrapper');
    const voucherToggle = document.getElementById('voucherToggle');
    const selectedVoucherText = document.getElementById('selectedVoucherText');
    const summaryDetails = document.getElementById('summaryList');
    const grandTotalDisplay = document.getElementById('grandTotalDisplay');
    const summaryHeader = document.querySelector('.summary-header strong');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    
    let activeVoucherId = null;
    let currentPotongan = 0;

    voucherToggle.onclick = (e) => {
        e.stopPropagation();
        voucherWrapper.classList.toggle('active');
    };

    document.querySelectorAll('.apply-btn').forEach(btn => {
        btn.onclick = function(e) {
            e.stopPropagation();
            const promoId = this.dataset.idpromo;
            const promoName = this.dataset.name;

            if (activeVoucherId === promoId) {
                resetVoucher();
                return;
            }

            fetch(`co-keranjang.php?action=cek_promo&idPromo=${promoId}`)
                .then(res => res.json())
                .then(data => {
                    if (parseFloat(data.potongan) <= 0) {
                        alert("Voucher tidak memenuhi syarat atau keranjang kosong.");
                        resetVoucher();
                    } else {
                        activeVoucherId = promoId;
                        currentPotongan = parseFloat(data.potongan);
                        selectedVoucherText.innerHTML = `Voucher: <strong>${promoName}</strong>`;
                        updateSummary();
                        voucherWrapper.classList.remove('active');
                    }
                })
                .catch(err => console.error("Error fetching promo:", err));
        };
    });

    function resetVoucher() {
        activeVoucherId = null;
        currentPotongan = 0;
        selectedVoucherText.textContent = "Pilih promo yang tersedia";
        updateSummary();
    }

    function updateSummary() {
        let totalBelanja = 0;
        let totalQty = 0;
        let html = '';

        checkboxes.forEach(cb => {
            if (cb.checked) {
                const p = parseFloat(cb.dataset.price);
                const q = parseInt(cb.dataset.qty);
                const sub = p * q;
                totalBelanja += sub;
                totalQty += q;
                html += `<div class="row"><span>${q} item @ Rp ${p.toLocaleString('id-ID')}</span><span>Rp ${sub.toLocaleString('id-ID')}</span></div>`;
            }
        });

        if (currentPotongan > 0) {
            html += `<div id="rowPotongan" style="color:red"><span>Potongan Promo</span><span>- Rp ${currentPotongan.toLocaleString('id-ID')}</span></div>`;
        }
        
        summaryHeader.textContent = `Total ${totalQty} Barang`;
        summaryDetails.innerHTML = html;
        grandTotalDisplay.textContent = `Rp ${Math.max(0, totalBelanja - currentPotongan).toLocaleString('id-ID')}`;
    }

    checkboxes.forEach(cb => cb.onchange = updateSummary);

    document.querySelectorAll('.qty-form .plus, .qty-form .minus').forEach(btn => {
        btn.onclick = function() {
            const form = this.closest('form');
            const input = form.querySelector('.qty-input');
            let val = parseInt(input.value);
            if (this.classList.contains('plus')) val++;
            else val--;
            
            if (val < 1) { 
                if(!confirm('Hapus item dari keranjang?')) return; 
                val = 0; 
            }
            input.value = val;
            form.submit();
        };
    });

    document.getElementById('btnCheckout').onclick = () => {
        const ids = Array.from(checkboxes).filter(c => c.checked).map(c => c.value);
        if(!ids.length) return alert("Pilih minimal satu barang!");
        
        let checkoutUrl = `co-langsung.php?mode=cart&items=${ids.join(',')}`;
        if (activeVoucherId) {
            checkoutUrl += `&promo=${activeVoucherId}`;
        }
        window.location.href = checkoutUrl;
    };
    updateSummary();
});
</script>

<?php include '../includes/footer.php'; ?>