<?php
session_start();
$promoTerpilih = $_GET['promo'] ?? '';
include '../includes/dbOnlinePOS.php';

if (!isset($_SESSION['idPelanggan'])) {
    header("Location: sign-in-page.php");
    exit;
}

if (isset($_GET['mode'])) {
    $_SESSION['mode_checkout'] = $_GET['mode'];
}

if (isset($_GET['id']) && isset($_GET['qty'])) {
    $_SESSION['mode_checkout'] = 'buy_now';
    $_SESSION['bn_idProduk'] = $_GET['id'];
    $_SESSION['bn_qty'] = $_GET['qty'];
}

$modeCheckout = $_SESSION['mode_checkout'] ?? 'cart'; 

if (isset($_GET['items'])) {
    $_SESSION['selected_items_checkout'] = $_GET['items'];
}

$idPelanggan = $_SESSION['idPelanggan'];
$payment = $_SESSION['payment'] ?? 'Belum Dipilih';
$subPayment = $_SESSION['sub_payment'] ?? '';
$metodeLengkap = trim($payment . ' ' . $subPayment);
$kategoriSaja = strtolower(str_replace([' ', '-'], '', trim($payment)));

if ($kategoriSaja == 'virtualaccount') {
    $kategoriSaja = 'transferbank';
} elseif ($kategoriSaja == 'ewallet') {
    $kategoriSaja = 'emoney';
}

if (isset($_GET['action']) && $_GET['action'] == 'hitung_total') {
    ob_clean();
    header('Content-Type: application/json');

    $idPromo = $_GET['idPromo'] ?? '';
    $kurir   = $_GET['kurir'] ?? 'J&T Express';
    $subtotalInput = (float)($_GET['subtotal'] ?? 0);

    $sqlO = "SELECT fn_ongkos_kirim(?) AS ongkir";
    $stmtO = mysqli_prepare($conn, $sqlO);
    mysqli_stmt_bind_param($stmtO, "s", $kurir);
    mysqli_stmt_execute($stmtO);
    $resO = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtO));
    $ongkir = (float)($resO['ongkir'] ?? 0);

    $sqlA = "SELECT fn_biaya_admin(?, ?) AS admin";
    $stmtA = mysqli_prepare($conn, $sqlA);
    mysqli_stmt_bind_param($stmtA, "sd", $metodeLengkap, $subtotalInput);
    mysqli_stmt_execute($stmtA);
    $resA = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtA));
    $admin = (float)($resA['admin'] ?? 0);

    $potonganHarga = 0;   
    $potonganOngkir = 0;  
    $nominalCashback = 0; 
    $tipeTampil = ''; 
    $errorMsg = "";    

    if (!empty($idPromo)) {
        $sqlCek = "SELECT namaPromo, minimalTransaksi, nominalPotongan, persentasePotongan, tipePromo, jenisPembayaran FROM tbpromo WHERE idPromo = ?";
        $stmtC = mysqli_prepare($conn, $sqlCek);
        mysqli_stmt_bind_param($stmtC, "s", $idPromo);
        mysqli_stmt_execute($stmtC);
        $resC = mysqli_stmt_get_result($stmtC);
        $promo = mysqli_fetch_assoc($resC);

        if($promo){
            if ($subtotalInput < (float)$promo['minimalTransaksi']){
                $errorMsg = "Minimal belanja Rp ".number_format($promo['minimalTransaksi'], 0, ',', '.');
            } else if (!empty($promo['jenisPembayaran']) && strtolower(trim($promo['jenisPembayaran'])) !== $kategoriSaja) {
                $errorMsg = "Promo ini khusus pembayaran " . strtoupper($promo['jenisPembayaran']);
            }

            if ($errorMsg === "") {
                $namaPromo = strtolower($promo['namaPromo']);
                $tipePromo = strtolower($promo['tipePromo']);
                $nilaiPromo = ($promo['persentasePotongan'] > 0) 
                              ? $subtotalInput * ($promo['persentasePotongan'] / 100) 
                              : (float)$promo['nominalPotongan'];

                if (strpos($namaPromo, 'ongkir') !== false) {
                    $potonganOngkir = min($nilaiPromo, $ongkir);
                    $tipeTampil = 'gratis_ongkir';
                } else if ($tipePromo === 'diskon') {
                    $potonganHarga = $nilaiPromo;
                    $tipeTampil = 'diskon';
                } else if ($tipePromo === 'cashback') {
                    $nominalCashback = $nilaiPromo;
                    $tipeTampil = 'cashback';
                }
            }
        }
    }

    echo json_encode([
        'ongkir' => $ongkir,
        'admin' => $admin,
        'potongan' => $potonganHarga,
        'potonganOngkir' => $potonganOngkir,
        'cashback' => $nominalCashback,
        'tipe' => $tipeTampil,
        'error' => $errorMsg
    ]);
    exit;
}

// --- BAGIAN PROSES CHECKOUT ---
if (isset($_POST['btn_checkout'])) {
    if (!empty($_POST['idPromo'])) {
        $idP = $_POST['idPromo'];
        $cek = mysqli_query($conn, "SELECT jenisPembayaran FROM tbpromo WHERE idPromo = '$idP'");
        $rp = mysqli_fetch_assoc($cek);

        if (!empty($rp['jenisPembayaran']) && strtolower(trim($rp['jenisPembayaran'])) !== $kategoriSaja) {
            echo "<script>alert('Gagal! Promo ini khusus pembayaran " . strtoupper($rp['jenisPembayaran']) . "'); window.location.href='co-langsung.php';</script>";
            exit;
        }
    }

    $idPromo   = !empty($_POST['idPromo']) ? $_POST['idPromo'] : NULL;
    $ekspedisi = $_POST['ekspedisi'] ?? 'J&T Express';

    if ($modeCheckout === 'buy_now') {
        $bn_id = $_SESSION['bn_idProduk'] ?? '';
        $bn_qty = $_SESSION['bn_qty'] ?? 0;
        $sql = "CALL sp_checkout_transaksi(?, ?, ?, ?, ?, ?, @status)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssisss", $idPelanggan, $bn_id, $bn_qty, $idPromo, $metodeLengkap, $ekspedisi);
    } else {
        $selectedItems = $_POST['selected_items'] ?? '';
        $sql = "CALL sp_checkout_keranjang_pilihan(?, ?, ?, ?, ?, @status)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $idPelanggan, $selectedItems, $idPromo, $metodeLengkap, $ekspedisi);
    }
    
    mysqli_stmt_execute($stmt);
    $res = mysqli_query($conn, "SELECT @status AS pesan");
    $row = mysqli_fetch_assoc($res);
    $pesan = $row['pesan'] ?? 'Gagal.';

    echo "<script>alert('$pesan');</script>";
    if (strpos($pesan, 'berhasil') !== false) {
        unset($_SESSION['mode_checkout'], $_SESSION['bn_idProduk'], $_SESSION['bn_qty'], $_SESSION['selected_items_checkout']);
        echo "<script>window.location.href='history.php';</script>";
        exit;
    }
}

// --- PENGAMBILAN DATA UNTUK TAMPILAN ---
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
    $itemParam = $_SESSION['selected_items_checkout'] ?? ($_GET['items'] ?? ''); 
    $selectedItems = !empty($itemParam) ? explode(',', $itemParam) : [];
    if (!empty($selectedItems)) {
        $placeholders = implode(',', array_fill(0, count($selectedItems), '?'));
        $sql = "SELECT k.idProduk, k.jumlah, p.namaProduk, p.harga as hargaSatuan FROM tbKeranjang k JOIN tbProduk p ON k.idProduk = p.idProduk WHERE k.idPelanggan = ? AND k.idProduk IN ($placeholders)";
        $stmt = mysqli_prepare($conn, $sql);
        $types = "s" . str_repeat("s", count($selectedItems));
        mysqli_stmt_bind_param($stmt, $types, $idPelanggan, ...$selectedItems);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($res)) {
            $items[] = $row;
            $totalHarga += ($row['jumlah'] * $row['hargaSatuan']);
        }
    }
}

$currentDate = date('Y-m-d H:i:s');
$sqlPromo = "SELECT * FROM tbpromo WHERE startDate <= ? AND endDate >= ? AND statusAktif = 'Y'";
$stmtPromo = mysqli_prepare($conn, $sqlPromo);
mysqli_stmt_bind_param($stmtPromo, "ss", $currentDate, $currentDate);
mysqli_stmt_execute($stmtPromo);
$resultPromo = mysqli_stmt_get_result($stmtPromo);

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
        foreach ($items as $item): 
            $gambar = "../assets/img/default.jpg"; 
            foreach (['webp', 'jpg', 'jpeg', 'png'] as $e) {
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
            <p><strong><?= htmlspecialchars($user['namaPelanggan'] ?? 'User'); ?> (<?= $user['kontakPelanggan'] ?? '-'; ?>)</strong></p>
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
                <?php if (mysqli_num_rows($resultPromo) === 0): ?>
                    <div class="promo-item"><span>Tidak ada promo tersedia</span></div>
                <?php else: ?>
                    <?php while ($p = mysqli_fetch_assoc($resultPromo)): 
                        $label = ($p['persentasePotongan'] > 0) ? $p['persentasePotongan']."%" : "Rp ".number_format($p['nominalPotongan'],0,',','.');
                    ?>
                        <div class="promo-item">
                            <span><?= htmlspecialchars($p['namaPromo']); ?> (<?= $label ?>)</span>
                            <button type="button" class="apply-btn" data-id="<?= $p['idPromo'] ?>" data-name="<?= htmlspecialchars($p['namaPromo']) ?>">Gunakan</button>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="summary-details">
            <div class="row"><span>Subtotal</span><span>Rp <?= number_format($totalHarga, 0, ',', '.'); ?></span></div>
            <div class="row"><span>Ongkir (<span id="namaKurir">-</span>)</span><span id="txtOngkir">Rp 0</span></div>
            <div class="row"><span>Biaya Admin</span><span id="txtAdmin">Rp 0</span></div>
            <div id="rowPotongan" style="display:none; color: red;" class="row"><span>Potongan Promo</span><span id="txtPotongan">- Rp 0</span></div>
        </div>
        <hr>
        <form action="" method="POST" id="formCheckout">
            <input type="hidden" name="idPromo" id="inputPromo">
            <input type="hidden" name="modeCheckout" value="<?= $modeCheckout ?>">
            <?php if($modeCheckout === 'buy_now'): ?>
                <input type="hidden" name="bn_idProduk" value="<?= $_SESSION['bn_idProduk'] ?? '' ?>">
                <input type="hidden" name="bn_qty" value="<?= $_SESSION['bn_qty'] ?? 0 ?>">
            <?php else: ?>
                <input type="hidden" name="selected_items" value="<?= htmlspecialchars($itemParam ?? '') ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label>Ekspedisi:</label>
                <select name="ekspedisi" class="form-select">
                    <option value="J&T Express">J&T Express</option>
                    <option value="JNE Reguler">JNE Reguler</option>
                    <option value="SiCepat">SiCepat</option>
                </select>
            </div>
            <div class="row total"><span>Total Harga</span><span id="displayTotal">Rp <?= number_format($totalHarga, 0, ',', '.'); ?></span></div>
            <button type="submit" name="btn_checkout" class="checkout-btn">Checkout Now</button>
        </form>
    </aside>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const vWrapper = document.getElementById('voucherWrapper');
    const vToggle = document.getElementById('voucherToggle');
    const selectedVoucherText = document.getElementById('selectedVoucherText');
    const selectKurir = document.querySelector('select[name="ekspedisi"]');
    const subtotalProduk = <?= $totalHarga ?>;
    let currentPromoId = "<?= $promoTerpilih ?>";

    if (currentPromoId !== "") {
        document.getElementById('inputPromo').value = currentPromoId;
        const btnAsli = document.querySelector(`.apply-btn[data-id="${currentPromoId}"]`);
        if (btnAsli) {
            selectedVoucherText.innerHTML = `Voucher: <strong>${btnAsli.dataset.name}</strong>`;
        }
    }

    function hitungUlang() {
        fetch(`co-langsung.php?action=hitung_total&idPromo=${currentPromoId}&kurir=${selectKurir.value}&subtotal=${subtotalProduk}`)
            .then(res => res.json())
            .then(data => {
                if (data.error !== ""){
                    alert(data.error);
                    currentPromoId ="";
                    document.getElementById('inputPromo').value = "";
                    selectedVoucherText.textContent = "Pilih promo yang tersedia";
                    hitungUlang();
                    return;
                }

                document.getElementById('namaKurir').innerText = selectKurir.value;
                
                const ongkirFinal = Math.max(0, data.ongkir - data.potonganOngkir);
                document.getElementById('txtOngkir').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(ongkirFinal);
                
                document.getElementById('txtAdmin').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.admin);
                
                const rowPot = document.getElementById('rowPotongan');
                const txtPot = document.getElementById('txtPotongan');
                const labelPot = rowPot.querySelector('span:first-child');

                if (data.tipe === 'gratis_ongkir') {
                    rowPot.style.display = 'flex';
                    rowPot.style.color = 'green';
                    labelPot.innerText = 'Potongan Ongkir';
                    txtPot.innerText = '- Rp ' + new Intl.NumberFormat('id-ID').format(data.potonganOngkir);
                } else if (data.tipe === 'diskon') {
                    rowPot.style.display = 'flex';
                    rowPot.style.color = 'red';
                    labelPot.innerText = 'Potongan Harga';
                    txtPot.innerText = '- Rp ' + new Intl.NumberFormat('id-ID').format(data.potongan);
                } else if (data.tipe === 'cashback') {
                    rowPot.style.display = 'flex';
                    rowPot.style.color = 'blue';
                    labelPot.innerText = 'Estimasi Cashback';
                    txtPot.innerText = '+ Rp ' + new Intl.NumberFormat('id-ID').format(data.cashback);
                } else {
                    rowPot.style.display = 'none';
                }

                const total = (subtotalProduk + data.admin + data.ongkir) - (data.potongan + data.potonganOngkir);
                document.getElementById('displayTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.max(0, total));
            });
    }

    vToggle.onclick = (e) => { e.stopPropagation(); vWrapper.classList.toggle('active'); };
    document.addEventListener('click', () => vWrapper.classList.remove('active'));

    document.querySelectorAll('.apply-btn').forEach(btn => {
        btn.onclick = function(e) {
            e.stopPropagation();
            if (currentPromoId === this.dataset.id) {
                currentPromoId = "";
                selectedVoucherText.textContent = "Pilih promo yang tersedia";
            } else {
                currentPromoId = this.dataset.id;
                selectedVoucherText.innerHTML = `Voucher: <strong>${this.dataset.name}</strong>`;
            }
            document.getElementById('inputPromo').value = currentPromoId;
            vWrapper.classList.remove('active');
            hitungUlang();
        };
    });

    selectKurir.onchange = hitungUlang;
    hitungUlang();
});
</script>

<?php include '../includes/footer.php'; ?>