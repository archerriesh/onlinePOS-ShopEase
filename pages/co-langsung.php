<?php
session_start();
include '../includes/dbOnlinePOS.php';

if (!isset($_SESSION['idPelanggan'], $_SESSION['bn_idProduk'], $_SESSION['bn_qty'])) {
    header("Location: index.php");
    exit;
}

$idPelanggan = $_SESSION['idPelanggan'];
$idProduk    = $_SESSION['bn_idProduk'];
$qty         = (int) $_SESSION['bn_qty'];

$payment     = $_SESSION['payment'] ?? 'Belum Dipilih';
$subPayment  = $_SESSION['sub_payment'] ?? '';
$metodeLengkap = trim($payment . ' ' . $subPayment);

$stmt = mysqli_prepare($conn, "
    SELECT idProduk, namaProduk, harga 
    FROM tbProduk 
    WHERE idProduk = ?
");
mysqli_stmt_bind_param($stmt, "s", $idProduk);
mysqli_stmt_execute($stmt);
$produk = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$produk || $qty <= 0) {
    echo "<script>alert('Produk tidak valid'); window.location.href='index.php';</script>";
    exit;
}

$subtotalProduk = $produk['harga'] * $qty;

if (isset($_GET['action']) && $_GET['action'] === 'hitung_total') {
    header('Content-Type: application/json');

    $kurir  = $_GET['kurir'] ?? 'JNE';
    $promo  = $_GET['idPromo'] ?? '';

    // Ongkir
    $stmt = mysqli_prepare($conn, "SELECT fn_ongkos_kirim(?) AS ongkir");
    mysqli_stmt_bind_param($stmt, "s", $kurir);
    mysqli_stmt_execute($stmt);
    $ongkir = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['ongkir'] ?? 0;

    $stmt = mysqli_prepare($conn, "SELECT fn_biaya_admin(?) AS admin");
    mysqli_stmt_bind_param($stmt, "s", $metodeLengkap);
    mysqli_stmt_execute($stmt);
    $admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['admin'] ?? 0;

    $potongan = 0;
    if (!empty($promo)) {
        $stmt = mysqli_prepare($conn, "
            SELECT fn_promo_terpakai(?, ?, ?, ?) AS pot
        ");
        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $promo,
            $idPelanggan,
            $kurir,
            $metodeLengkap
        );
        mysqli_stmt_execute($stmt);
        $potongan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['pot'] ?? 0;
    }

    echo json_encode([
        'ongkir'    => (float)$ongkir,
        'admin'     => (float)$admin,
        'potongan'  => (float)$potongan
    ]);
    exit;
}

if (isset($_POST['btn_checkout'])) {

    $idPromo   = !empty($_POST['idPromo']) ? $_POST['idPromo'] : NULL;
    $ekspedisi = $_POST['ekspedisi'] ?? 'JNE';

    $stmt = mysqli_prepare($conn, "
        CALL sp_checkout_buy_now(?,?,?,?,?,?,@status)
    ");
    mysqli_stmt_bind_param(
        $stmt,
        "ssisss",
        $idPelanggan,
        $idProduk,
        $qty,
        $idPromo,
        $metodeLengkap,
        $ekspedisi
    );

    if (mysqli_stmt_execute($stmt)) {
        $res = mysqli_query($conn, "SELECT @status AS pesan");
        $pesan = mysqli_fetch_assoc($res)['pesan'];

        if (strpos($pesan, 'berhasil') !== false) {
            unset($_SESSION['bn_idProduk'], $_SESSION['bn_qty'], $_SESSION['mode_checkout']);
            echo "<script>alert('$pesan'); window.location.href='history.php';</script>";
            exit;
        } else {
            echo "<script>alert('$pesan');</script>";
        }
    }
}

$stmtU = mysqli_prepare($conn, "
    SELECT namaPelanggan, kontakPelanggan, alamatPelanggan
    FROM tbPelanggan
    WHERE idPelanggan = ?
");
mysqli_stmt_bind_param($stmtU, "s", $idPelanggan);
mysqli_stmt_execute($stmtU);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtU));

$resultPromo = mysqli_query($conn, "
    SELECT * FROM tbPromo
    WHERE endDate >= NOW()
    AND statusAktif = 'Y'
");

$pageCSS = '../css/co-langsung.css';
include '../includes/header-main.php';
?>

<main class="checkout-page">
    <section class="left">
        <h3 class="section-title">Ordered Item</h3>

        <div class="order-card">
            <div class="order-info">
                <p class="product-name"><?= htmlspecialchars($produk['namaProduk']) ?></p>
                <small><?= $qty ?> item x Rp <?= number_format($produk['harga'], 0, ',', '.') ?></small>
            </div>
            <div class="subtotal">
                Rp <?= number_format($subtotalProduk, 0, ',', '.') ?>
            </div>
        </div>

        <h3 class="section-title">Alamat Pengiriman</h3>
        <div class="address-box">
            <p><strong><?= htmlspecialchars($user['namaPelanggan']) ?></strong></p>
            <p><?= htmlspecialchars($user['alamatPelanggan']) ?></p>
        </div>

        <div class="payment-box">
            <span><?= htmlspecialchars($metodeLengkap) ?></span>
            <button type="button" onclick="location.href='metode-pembayaran.php'">Ganti</button>
        </div>
    </section>

    <aside class="summary-panel">
        <form method="POST">
            <input type="hidden" name="idPromo" id="inputPromo">

            <div class="mb-3">
                <label>Ekspedisi</label>
                <select name="ekspedisi" class="form-select">
                    <option value="JNE">JNE</option>
                    <option value="SICEPAT">SiCepat</option>
                    <option value="J&T Express">J&T Express</option>
                </select>
            </div>

            <div class="row total">
                <span>Total Harga</span>
                <span id="displayTotal">Rp 0</span>
            </div>

            <button type="submit" name="btn_checkout" class="checkout-btn">
                Checkout Now
            </button>
        </form>
    </aside>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const subtotal = <?= $subtotalProduk ?>;
        const selectKurir = document.querySelector('select[name="ekspedisi"]');

        function hitungUlang() {
            fetch(`<?= $_SERVER['PHP_SELF'] ?>?action=hitung_total&kurir=${selectKurir.value}`)
                .then(res => res.json())
                .then(d => {
                    const total = (subtotal + d.ongkir + d.admin) - d.potongan;
                    document.getElementById('displayTotal').innerText =
                        'Rp ' + new Intl.NumberFormat('id-ID').format(total);
                });
        }

        selectKurir.onchange = hitungUlang;
        hitungUlang();
    });
</script>

<?php include '../includes/footer.php'; ?>