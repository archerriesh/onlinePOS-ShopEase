<?php
session_start();
include '../includes/dbOnlinePOS.php';

if (!isset($_SESSION['idPelanggan'])) {
    header("Location: sign-in-page.php");
    exit;
}

$idPelanggan = $_SESSION['idPelanggan'];
$modeCheckout = $_SESSION['mode_checkout'] ?? '';
$payment = $_SESSION['payment'] ?? 'Belum Dipilih';
$subPayment = $_SESSION['sub_payment'] ?? '';
$metodeLengkap = trim($payment . ' ' . $subPayment);

if ($modeCheckout === 'buy_now') {
    $bn_id = $_SESSION['bn_idProduk'] ?? '';
    $bn_qty = $_SESSION['bn_qty'] ?? 0;
    
    mysqli_query($conn, "DELETE FROM tbKeranjang WHERE idPelanggan = '$idPelanggan'");
    
    if(!empty($bn_id)){
        mysqli_query($conn, "INSERT INTO tbKeranjang (idPelanggan, idProduk, jumlah, hargaSatuan) 
                            SELECT '$idPelanggan', idProduk, $bn_qty, harga FROM tbProduk WHERE idProduk = '$bn_id'");
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'hitung_total') {
    ob_clean();
    header('Content-Type: application/json');
    $idPromo = $_GET['idPromo'] ?? '';
    $kurir   = $_GET['kurir'] ?? 'JNE';

    $sqlO = "SELECT fn_ongkos_kirim(?) AS ongkir";
    $stmtO = mysqli_prepare($conn, $sqlO);
    mysqli_stmt_bind_param($stmtO, "s", $kurir);
    mysqli_stmt_execute($stmtO);
    $ongkir = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtO))['ongkir'] ?? 0;

    $sqlA = "SELECT fn_biaya_admin(?, ?) AS admin";
    $stmtA = mysqli_prepare($conn, $sqlA);
    mysqli_stmt_bind_param($stmtA, "ss", $metodeLengkap, $idPelanggan);
    mysqli_stmt_execute($stmtA);
    $admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtA))['admin'] ?? 0;

    $potongan = 0;
    if (!empty($idPromo)) {
        $sqlP = "SELECT fn_promo_terpakai(?, ?, ?, ?) AS pot";
        $stmtP = mysqli_prepare($conn, $sqlP);
        mysqli_stmt_bind_param($stmtP, "ssss", $idPromo, $idPelanggan, $kurir, $metodeLengkap);
        mysqli_stmt_execute($stmtP);
        $potongan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtP))['pot'] ?? 0;
    }

    echo json_encode(['ongkir' => (float)$ongkir, 'admin' => (float)$admin, 'potongan' => (float)$potongan]);
    exit; 
}

if (isset($_POST['btn_checkout'])) {
    $idPromo   = !empty($_POST['idPromo']) ? $_POST['idPromo'] : NULL;
    $ekspedisi = $_POST['ekspedisi'] ?? 'JNE';

    $sql = "CALL sp_checkout_keranjang(?, ?, ?, ?, @status)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $idPelanggan, $idPromo, $metodeLengkap, $ekspedisi);
    
    if (mysqli_stmt_execute($stmt)) {
        $res = mysqli_query($conn, "SELECT @status AS pesan");
        $pesan = mysqli_fetch_assoc($res)['pesan'];
        if (strpos($pesan, 'berhasil') !== false) {
            unset($_SESSION['mode_checkout'], $_SESSION['bn_idProduk'], $_SESSION['bn_qty']);
            echo "<script>alert('$pesan'); window.location.href='history.php';</script>";
            exit;
        } else { echo "<script>alert('$pesan');</script>"; }
    }
}

$items = [];
$totalHarga = 0;
$stmtI = mysqli_prepare($conn, "SELECT k.*, p.namaProduk, p.harga as hargaSatuan FROM tbKeranjang k JOIN tbProduk p ON k.idProduk = p.idProduk WHERE k.idPelanggan = ?");
mysqli_stmt_bind_param($stmtI, "s", $idPelanggan);
mysqli_stmt_execute($stmtI);
$resI = mysqli_stmt_get_result($stmtI);
while ($row = mysqli_fetch_assoc($resI)) {
    $items[] = $row;
    $totalHarga += ($row['jumlah'] * $row['hargaSatuan']);
}

$resultPromo = mysqli_query($conn, "SELECT * FROM tbPromo WHERE endDate >= NOW() AND statusAktif = 'Y'");
$stmtU = mysqli_prepare($conn, "SELECT namaPelanggan, kontakPelanggan, alamatPelanggan FROM tbpelanggan WHERE idPelanggan = ?");
mysqli_stmt_bind_param($stmtU, "s", $idPelanggan);
mysqli_stmt_execute($stmtU);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtU));

$pageCSS = '../css/co-langsung.css';
include '../includes/header-main.php';
?>

<main class="checkout-page">
    <section class="left">
        <h3 class="section-title">Ordered Items</h3>
        <?php 
        $basePath = "../foto/produk/";
        $exts = ['webp', 'jpg', 'jpeg', 'png'];
        foreach ($items as $item): 
            $gambar = "../assets/img/default.jpg"; 
            foreach ($exts as $e) { if (file_exists($basePath . $item['idProduk'] . "." . $e)) { $gambar = $basePath . $item['idProduk'] . "." . $e; break; } }
        ?>
        <div class="order-card">
            <img src="<?= $gambar ?>">
            <div class="order-info">
                <p class="product-name"><?= htmlspecialchars($item['namaProduk']) ?></p>
                <small><?= $item['jumlah'] ?> item x Rp <?= number_format($item['hargaSatuan'], 0, ',', '.') ?></small>
            </div>
            <div class="subtotal" style="margin-left: auto; font-weight: bold;">
                Rp <?= number_format($item['jumlah'] * $item['hargaSatuan'], 0, ',', '.') ?>
            </div>
        </div>
        <?php endforeach; ?>

        <h3 class="section-title">Alamat Pengiriman</h3>
        <div class="address-box">
            <p><strong><?= htmlspecialchars(($user['namaPelanggan'] ?? 'User') . ' (' . ($user['kontakPelanggan'] ?? '-') . ')'); ?></strong></p>
            <p><?= htmlspecialchars($user['alamatPelanggan'] ?? 'Alamat belum diatur.'); ?></p>
        </div>
        <div class="payment-box"><span><?= $metodeLengkap ?></span><button type="button" onclick="window.location.href='metode-pembayaran.php'">Ganti</button></div>
    </section>

    <aside class="summary-panel">
        <div class="voucher-wrapper" id="voucherWrapper">
            <p class="voucher-title">Voucher</p>
            <div class="voucher-box" id="voucherToggle">
                <span id="selectedVoucherText">Pilih promo yang tersedia</span>
            </div>
            <div class="promo-dropdown" id="voucherContent">
                <?php if (mysqli_num_rows($resultPromo) === 0): ?>
                    <div class="promo-item"><span>Tidak ada promo</span></div>
                <?php else: ?>
                    <?php mysqli_data_seek($resultPromo, 0); while ($p = mysqli_fetch_assoc($resultPromo)): ?>
                        <div class="promo-item" style="display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #eee;">
                            <span><?= htmlspecialchars($p['namaPromo']); ?></span>
                            <button type="button" class="apply-btn" 
                                    data-id="<?= $p['idPromo'] ?>" 
                                    data-name="<?= $p['namaPromo'] ?>"
                                    style="background: #ff5722; color: #fff; border: none; padding: 2px 10px; cursor: pointer; border-radius: 4px;">Gunakan</button>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="summary-details">
            <div class="row"><span>Subtotal Produk</span><span>Rp <?= number_format($totalHarga, 0, ',', '.'); ?></span></div>
            <div class="row"><span>Ongkir (<span id="namaKurir">-</span>)</span><span id="txtOngkir">Rp 0</span></div>
            <div class="row"><span>Biaya Layanan</span><span id="txtAdmin">Rp 0</span></div>
            <div id="rowPotongan" style="display:none; color: red;" class="row"><span>Potongan Promo</span><span id="txtPotongan">- Rp 0</span></div>
        </div>
        <hr>
        <form method="POST">
            <input type="hidden" name="idPromo" id="inputPromo">
            <input type="hidden" name="metodePembayaran" value="<?= $metodeLengkap ?>">
            <div class="mb-3">
                <label>Ekspedisi:</label>
                <select name="ekspedisi" class="form-select">
                    <option value="JNE">JNE</option>
                    <option value="SICEPAT">SiCepat</option>
                    <option value="J&T Express">J&T Express</option> 
                </select>
            </div>
            <div class="row total"><span>Total Harga</span><span id="displayTotal">Rp 0</span></div>
            <button type="submit" name="btn_checkout" class="checkout-btn">Checkout Now</button>
        </form>
    </aside>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectKurir = document.querySelector('select[name="ekspedisi"]');
        const vWrapper = document.getElementById('voucherWrapper');
        const vToggle = document.getElementById('voucherToggle');
        const subtotalProduk = <?= $totalHarga ?>;

        function hitungUlang() {
            const promoId = document.getElementById('inputPromo').value;
            fetch(`co-langsung.php?action=hitung_total&idPromo=${promoId}&kurir=${selectKurir.value}`)
                .then(res => res.json()).then(data => {
                    document.getElementById('namaKurir').innerText = selectKurir.value;
                    document.getElementById('txtOngkir').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.ongkir);
                    document.getElementById('txtAdmin').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.admin);
                    document.getElementById('txtPotongan').innerText = '- Rp ' + new Intl.NumberFormat('id-ID').format(data.potongan);
                    document.getElementById('rowPotongan').style.display = (data.potongan > 0) ? 'flex' : 'none';
                    document.getElementById('displayTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format((subtotalProduk + data.ongkir + data.admin) - data.potongan);
                });
        }

        if(vToggle) {
            vToggle.onclick = () => vWrapper.classList.toggle('active');
        }

        document.querySelectorAll('.apply-btn').forEach(btn => {
            btn.onclick = function() {
                document.getElementById('inputPromo').value = this.dataset.id;
                document.getElementById('selectedVoucherText').innerHTML = `<strong>${this.dataset.name}</strong>`;
                vWrapper.classList.remove('active');
                hitungUlang();
            };
        });

        selectKurir.onchange = hitungUlang;
        hitungUlang();
    });
</script>
<?php include '../includes/footer.php'; ?>