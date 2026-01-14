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

if (isset($_GET['action']) && $_GET['action'] == 'hitung_total') {
    ob_clean();
    header('Content-Type: application/json');

    $idPromo = $_GET['idPromo'] ?? '';
    $kurir   = $_GET['kurir'] ?? 'J&T Express';

    $sqlO = "SELECT fn_ongkos_kirim(?) AS ongkir";
    $stmtO = mysqli_prepare($conn, $sqlO);
    mysqli_stmt_bind_param($stmtO, "s", $kurir);
    mysqli_stmt_execute($stmtO);
    $resO = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtO));
    $ongkir = (float)($resO['ongkir'] ?? 0);

    $sqlA = "SELECT fn_biaya_admin(?, ?) AS admin";
    $stmtA = mysqli_prepare($conn, $sqlA);
    mysqli_stmt_bind_param($stmtA, "ss", $metodeLengkap, $idPelanggan);
    mysqli_stmt_execute($stmtA);
    $resA = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtA));
    $admin = (float)($resA['admin'] ?? 0);

    $potongan = 0;
    if (!empty($idPromo)) {
        $sqlP = "SELECT fn_promo_terpakai(?, ?, ?, ?) AS pot";
        $stmtP = mysqli_prepare($conn, $sqlP);
        mysqli_stmt_bind_param($stmtP, "ssss", $idPromo, $idPelanggan, $kurir, $metodeLengkap);
        mysqli_stmt_execute($stmtP);
        $resP = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtP));
        $potongan = (float)($resP['pot'] ?? 0);
    }

    echo json_encode([
        'ongkir' => $ongkir,
        'admin' => $admin,
        'potongan' => $potongan
    ]);
    exit; 
}

if (isset($_POST['btn_checkout'])) {
    $idPromo   = !empty($_POST['idPromo']) ? $_POST['idPromo'] : NULL;
    $ekspedisi = $_POST['ekspedisi'] ?? 'J&T Express';

    if ($modeCheckout === 'buy_now') {
        $bn_id = $_SESSION['bn_idProduk'];
        $bn_qty = $_SESSION['bn_qty'];
        mysqli_query($conn, "DELETE FROM tbKeranjang WHERE idPelanggan = '$idPelanggan'");
        $sqlBN = "INSERT INTO tbKeranjang (idPelanggan, idProduk, jumlah, hargaSatuan) 
                  SELECT '$idPelanggan', idProduk, $bn_qty, harga FROM tbProduk WHERE idProduk = '$bn_id'";
        mysqli_query($conn, $sqlBN);
    }

    $sql = "CALL sp_checkout_keranjang(?, ?, ?, ?, @status)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $idPelanggan, $idPromo, $metodeLengkap, $ekspedisi);
    
    if (mysqli_stmt_execute($stmt)) {
        $res = mysqli_query($conn, "SELECT @status AS pesan");
        $row = mysqli_fetch_assoc($res);
        $pesan = $row['pesan'] ?? 'Proses Gagal';
        if (strpos($pesan, 'berhasil') !== false) {
            unset($_SESSION['mode_checkout'], $_SESSION['bn_idProduk'], $_SESSION['bn_qty']);
            echo "<script>alert('$pesan'); window.location.href='history.php';</script>";
            exit;
        } else {
            echo "<script>alert('$pesan');</script>";
        }
    }
}

$items = [];
$totalHarga = 0;
if ($modeCheckout === 'buy_now') {
    $stmt = mysqli_prepare($conn, "SELECT idProduk, namaProduk, harga as hargaSatuan FROM tbProduk WHERE idProduk = ?");
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['bn_idProduk']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($res)) {
        $row['jumlah'] = $_SESSION['bn_qty'];
        $items[] = $row;
        $totalHarga = $row['hargaSatuan'] * $row['jumlah'];
    }
} else {
    $stmt = mysqli_prepare($conn, "SELECT k.*, p.namaProduk, p.harga as hargaSatuan FROM tbKeranjang k JOIN tbProduk p ON k.idProduk = p.idProduk WHERE k.idPelanggan = ?");
    mysqli_stmt_bind_param($stmt, "s", $idPelanggan);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $items[] = $row;
        $totalHarga += ($row['jumlah'] * $row['hargaSatuan']);
    }
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
            foreach ($exts as $e) {
                if (file_exists($basePath . $item['idProduk'] . "." . $e)) {
                    $gambar = $basePath . $item['idProduk'] . "." . $e; 
                    break;
                }
            }
        ?>
        <div class="order-card">
            <img src="<?= $gambar ?>" alt="produk">
            <div class="order-info">
                <p class="product-name"><?= htmlspecialchars($item['namaProduk']) ?></p>
                <small><?= $item['jumlah'] ?> item x Rp <?= number_format($item['hargaSatuan'], 0, ',', '.') ?></small>
            </div>
            <div class="subtotal" style="margin-left: auto; font-weight: bold;">
                Rp <?= number_format($item['jumlah'] * $item['hargaSatuan'], 0, ',', '.') ?>
            </div>
        </div>
        <?php endforeach; ?>

        <h3 class="section-title">Shipping Address</h3>
        <div class="address-box">
            <p><strong><?= htmlspecialchars(($user['namaPelanggan'] ?? 'User') . ' (' . ($user['kontakPelanggan'] ?? '-') . ')'); ?></strong></p>
            <p><?= htmlspecialchars($user['alamatPelanggan'] ?? 'Alamat belum diatur.'); ?></p>
        </div>
        
        <h3 class="section-title">Payment Method</h3>
        <div class="payment-box">
            <span><?= $metodeLengkap ?></span>
            <button type="button" onclick="window.location.href='metode-pembayaran.php'">Change</button>
        </div>
    </section>

    <aside class="summary-panel">
        <div class="voucher-wrapper" id="voucherWrapper">
            <p class="voucher-title">Voucher</p>
            <div class="voucher-box" id="voucherToggle">
                <span id="selectedVoucherText">Pilih promo yang tersedia</span>
            </div>
            <div class="promo-dropdown" id="voucherContent">
                <?php while ($p = mysqli_fetch_assoc($resultPromo)): ?>
                    <div class="promo-item">
                        <span><?= htmlspecialchars($p['namaPromo']); ?></span>
                        <button type="button" class="apply-btn" 
                                data-id="<?= $p['idPromo'] ?>" 
                                data-name="<?= $p['namaPromo'] ?>">Gunakan</button>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="summary-details">
            <div class="row">
                <span>Subtotal</span>
                <span>Rp <?= number_format($totalHarga, 0, ',', '.'); ?></span>
            </div>
            <div class="row">
                <span>Ongkir (<span id="namaKurir">-</span>)</span>
                <span id="txtOngkir">Rp 0</span>
            </div>
            <div class="row">
                <span>Biaya Admin</span>
                <span id="txtAdmin">Rp 0</span>
            </div>
            <div id="rowPotongan" style="display:none; color: red;" class="row">
                <span>Promo</span>
                <span id="txtPotongan">- Rp 0</span>
            </div>
        </div>
        <hr>
        <form method="POST" id="formCheckout">
            <input type="hidden" name="idPromo" id="inputPromo">
            <input type="hidden" name="metodePembayaran" value="<?= $metodeLengkap ?>">
            
            <div class="mb-3">
                <label style="font-size:13px; font-weight:bold;">Ekspedisi:</label>
                <select name="ekspedisi" class="form-select">
                    <option value="J&T Express">J&T Express</option>
                    <option value="JNE Reguler">JNE Reguler</option>
                    <option value="Kurir Toko">Kurir Toko</option>
                </select>
            </div>

            <div class="row total">
                <span>Total Harga</span>
                <span id="displayTotal">Rp <?= number_format($totalHarga, 0, ',', '.'); ?></span>
            </div>

            <button type="submit" name="btn_checkout" class="checkout-btn">Checkout Now</button>
        </form>
    </aside>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const vWrapper = document.getElementById('voucherWrapper');
    const vToggle = document.getElementById('voucherToggle');
    const selectKurir = document.querySelector('select[name="ekspedisi"]');
    const subtotalProduk = <?= $totalHarga ?>;
    
    let currentPromoId = "";

    function hitungUlangSemua() {
        const kurir = selectKurir.value;
        const url = `co-langsung.php?action=hitung_total&idPromo=${currentPromoId}&kurir=${kurir}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                document.getElementById('namaKurir').innerText = kurir;
                document.getElementById('txtOngkir').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.ongkir);
                document.getElementById('txtAdmin').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.admin);
                document.getElementById('txtPotongan').innerText = '- Rp ' + new Intl.NumberFormat('id-ID').format(data.potongan);
                
                document.getElementById('rowPotongan').style.display = (data.potongan > 0) ? 'flex' : 'none';

                const totalAkhir = (subtotalProduk + data.ongkir + data.admin) - data.potongan;
                document.getElementById('displayTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalAkhir);
            });
    }

    selectKurir.onchange = hitungUlangSemua;

    document.querySelectorAll('.apply-btn').forEach(btn => {
        btn.onclick = function() {
            currentPromoId = this.dataset.id;
            const name = this.dataset.name;
            document.getElementById('inputPromo').value = currentPromoId;
            document.getElementById('selectedVoucherText').innerHTML = `<strong>${name}</strong>`;
            vWrapper.classList.remove('active');
            hitungUlangSemua(); 
        };
    });

    if(vToggle) {
        vToggle.onclick = () => vWrapper.classList.toggle('active');
    }
    
    hitungUlangSemua();
});
</script>
<?php include '../includes/footer.php'; ?>