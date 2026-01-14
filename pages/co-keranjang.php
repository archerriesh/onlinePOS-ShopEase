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
    
    $sqlCek = "SELECT jenisPembayaran FROM tbpromo WHERE idPromo = ?";
    $stmtCek = mysqli_prepare($conn, $sqlCek);
    mysqli_stmt_bind_param($stmtCek, "s", $idPromo);
    mysqli_stmt_execute($stmtCek);
    $resPromo = mysqli_stmt_get_result($stmtCek);
    $dataPromo = mysqli_fetch_assoc($resPromo);

    $metodeKirim = $dataPromo['jenisPembayaran'] ?? 'transferbank';

    $sql = "SELECT fn_promo_terpakai(?, ?, 'JNE', ?) AS potongan";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $idPromo, $idPelanggan, $metodeKirim);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    echo json_encode(['potongan' => (float)($row['potongan'] ?? 0)]);
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

$sql = "
SELECT 
    k.idProduk,
    p.namaProduk,
    k.jumlah,
    k.hargaSatuan
FROM tbKeranjang k
JOIN tbProduk p ON k.idProduk = p.idProduk
WHERE k.idPelanggan = ?
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $idPelanggan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$totalItem  = 0;
$totalHarga = 0;

$currentDate = date('Y-m-d H:i:s');
$sqlPromo = "SELECT * FROM tbpromo WHERE startDate <= ? AND endDate >= ?";
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
                <p class="empty">Cart is empty</p>
            <?php else: ?>

            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <?php
                $subtotal    = $row['jumlah'] * $row['hargaSatuan'];
                $totalItem  += $row['jumlah'];
                $totalHarga += $subtotal;

                $gambar = $defaultImg;
                foreach ($extensions as $ext) {
                    $file = $basePath . $row['idProduk'] . "." . $ext;
                    if (file_exists($file)) {
                        $gambar = $file;
                        break;
                    }
                }
            ?>

            <div class="cart-item">
                <div class="thumb">
                    <img src="<?= $gambar; ?>" alt="<?= htmlspecialchars($row['namaProduk']); ?>">
                </div>

                <div class="item-info">
                    <div class="item-name">
                        <?= htmlspecialchars($row['namaProduk']); ?>
                    </div>

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

                <div class="price">
                    Rp <?= number_format($row['hargaSatuan'], 0, ',', '.'); ?>
                </div>
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
                        <?php while ($promo = mysqli_fetch_assoc($resultPromo)): 
                            $isPercent = ($promo['persentasePotongan'] > 0);
                            $discountVal = $isPercent ? $promo['persentasePotongan'] : $promo['nominalPotongan'];
                            $type = $isPercent ? 'percent' : 'flat';
                            $label = $isPercent ? $discountVal."%" : "Rp ".number_format($discountVal,0,',','.');
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

            <div class="summary-header">
                <strong>Total <?= $totalItem; ?> Barang</strong>
            </div>

            <div class="summary-details" style="margin: 10px 0; font-size: 13px; color: #555;">
                <?php 
                mysqli_data_seek($result, 0); 
                while ($detail = mysqli_fetch_assoc($result)): 
                ?>
                    <div class="row" style="margin-bottom: 5px;">
                        <span><?= $detail['jumlah']; ?> item @ Rp <?= number_format($detail['hargaSatuan'], 0, ',', '.'); ?></span>
                        <span style="font-weight: 500;">Rp <?= number_format($detail['jumlah'] * $detail['hargaSatuan'], 0, ',', '.'); ?></span>
                    </div>
                <?php endwhile; ?>

                <div id="rowPotongan">
                    <span id="labelPromoUsed">Potongan Promo</span>
                    <span id="txtPotongan">- Rp 0</span>
                </div>

            </div>

            <hr>

            <div class="row total">
                <span>Total Harga</span>
                <span style="color: #61593d; font-size: 18px;">Rp <?= number_format($totalHarga, 0, ',', '.'); ?></span>
            </div>

            <button type="button" onclick="window.location.href='co-langsung.php'" class="checkout-btn">Checkout</button>
        </aside>

    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.qty-form').forEach(form => {
        const minus = form.querySelector('.minus');
        const plus  = form.querySelector('.plus');
        const input = form.querySelector('.qty-input');

        if(plus) {
            plus.onclick = (e) => {
                e.preventDefault(); 
                input.value = (parseInt(input.value, 10) || 0) + 1;
                form.submit(); 
            };
        }

        if(minus) {
            minus.onclick = (e) => {
                e.preventDefault();
                let qty = parseInt(input.value, 10) || 0;
                if (qty - 1 <= 0) {
                    if (!confirm('Hapus produk dari keranjang?')) return;
                    input.value = 0;
                } else {
                    input.value = qty - 1;
                }
                form.submit();
            };
        }
    });

    const voucherToggle = document.getElementById('voucherToggle');
    const voucherWrapper = document.querySelector('.voucher-wrapper');
    const selectedVoucherText = document.getElementById('selectedVoucherText');
    const txtPotongan = document.getElementById('txtPotongan');
    const txtTotalHarga = document.querySelector('.row.total span:last-child');
    
    const totalHargaAsli = parseFloat("<?= $totalHarga ?>") || 0;
    let activeVoucherId = null;

    if (voucherToggle) {
        voucherToggle.onclick = (e) => {
            e.stopPropagation();
            voucherWrapper.classList.toggle('active');
        };
    }

    document.querySelectorAll('.apply-btn').forEach(btn => {
        btn.onclick = function (e) {
            e.preventDefault();
            e.stopPropagation();

            const promoId = this.getAttribute('data-idpromo');
            const promoName = this.getAttribute('data-name');

            if (activeVoucherId === promoId) {
                resetVoucher();
                return;
            }

            const url = `co-keranjang.php?action=cek_promo&idPromo=${promoId}`;

            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Gagal memuat data promo');
                    return response.json();
                })
                .then(data => {
                    const potongan = parseFloat(data.potongan) || 0;
                    
                    activeVoucherId = promoId;
                    selectedVoucherText.innerHTML = `Voucher: <strong>${promoName}</strong>`;
                    selectedVoucherText.style.color = "#61593d";

                    txtPotongan.textContent = `- Rp ${new Intl.NumberFormat('id-ID').format(potongan)}`;
                    
                    const totalBaru = totalHargaAsli - potongan;
                    txtTotalHarga.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(totalBaru)}`;

                    voucherWrapper.classList.remove('active');
                })
                .catch(err => {
                    console.error("Detail Error:", err);
                    alert("Terjadi kesalahan saat memasang voucher.");
                });
        };
    });

    function resetVoucher() {
        activeVoucherId = null;
        selectedVoucherText.textContent = "Pilih promo yang tersedia";
        selectedVoucherText.style.color = "#8a817c";
        txtPotongan.textContent = "- Rp 0";
        txtTotalHarga.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(totalHargaAsli)}`;
        voucherWrapper.classList.remove('active');
    }

    window.onclick = (e) => {
        if (voucherWrapper && !voucherWrapper.contains(e.target)) {
            voucherWrapper.classList.remove('active');
        }
    };
});
</script>

<?php include '../includes/footer.php'; ?>