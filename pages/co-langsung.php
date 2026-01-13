<?php
session_start();
include '../includes/dbOnlinePOS.php';

if (!isset($_SESSION['idPelanggan'])) {
    header("Location: sign-in-page.php");
    exit;
}

$idPelanggan = $_SESSION['idPelanggan'];

if (isset($_POST['btn_checkout'])) {
    $idPromo   = !empty($_POST['idPromo']) ? $_POST['idPromo'] : NULL;
    $metode    = $_POST['metodePembayaran'] ?? 'Virtual Account BCA';
    $ekspedisi = $_POST['ekspedisi'] ?? 'J&T Express';

    $sql = "CALL sp_checkout_keranjang(?, ?, ?, ?, @status)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $idPelanggan, $idPromo, $metode, $ekspedisi);
    
    if (mysqli_stmt_execute($stmt)) {
        $res = mysqli_query($conn, "SELECT @status AS pesan");
        $rowPesan = mysqli_fetch_assoc($res);
        $pesan = $rowPesan['pesan'];

        if (strpos($pesan, 'berhasil') !== false) {
            echo "<script>alert('$pesan'); window.location.href='history.php';</script>";
            exit;
        } else {
            $error_sp = $pesan;
        }
    } else {
        die("Error SP: " . mysqli_error($conn));
    }
}

$sql_cart = "SELECT k.*, p.namaProduk, p.harga FROM tbKeranjang k 
             JOIN tbProduk p ON k.idProduk = p.idProduk 
             WHERE k.idPelanggan = ?";
$stmt_cart = mysqli_prepare($conn, $sql_cart);
mysqli_stmt_bind_param($stmt_cart, "s", $idPelanggan);
mysqli_stmt_execute($stmt_cart);
$result = mysqli_stmt_get_result($stmt_cart);

$sql_user = "SELECT namaPelanggan, kontakPelanggan, alamatPelanggan FROM tbpelanggan WHERE idPelanggan = ?";
$stmt_user = mysqli_prepare($conn, $sql_user);
mysqli_stmt_bind_param($stmt_user, "s", $idPelanggan);
mysqli_stmt_execute($stmt_user);
$res_user = mysqli_stmt_get_result($stmt_user);
$user = mysqli_fetch_assoc($res_user);

$namaUser   = $user['namaPelanggan'] ?? 'Nama Tidak Terdaftar';
$telpUser   = $user['kontakPelanggan'] ?? '-';
$alamatUser = $user['alamatPelanggan'] ?? 'Alamat belum diatur.';

$totalItem = 0;
$totalHarga = 0;
$items = [];

while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
    $totalItem += $row['jumlah'];
    $totalHarga += ($row['jumlah'] * $row['hargaSatuan']);
}

$payment = $_SESSION['payment'] ?? 'Virtual Account';
$sub     = $_SESSION['sub_payment'] ?? 'BCA';

$basePath   = "../foto/produk/";
$extensions = ['webp', 'jpg', 'jpeg', 'png'];
$defaultImg = "../assets/img/default.jpg";

$pageCSS = '../css/co-langsung.css';
include '../includes/header-main.php';
?>

<main class="checkout-page">
    <section class="left">
        <h3 class="section-title">Ordered Items</h3>

        <?php foreach ($items as $item): ?>
        <div class="order-card">
            <img src="../foto/produk/<?= $item['idProduk'] ?>.webp" alt="product" 
                 onerror="this.src='../assets/img/default.jpg'">
            <div class="order-info">
                <p class="product-name"><?= htmlspecialchars($item['namaProduk']) ?></p>
                <small><?= $item['jumlah'] ?> item x Rp <?= number_format($item['hargaSatuan'], 0, ',', '.') ?></small>
            </div>
            <div class="subtotal" style="margin-left: auto; font-weight: bold;">
                Rp <?= number_format($item['jumlah'] * $item['hargaSatuan'], 0, ',', '.') ?>
            </div>
        </div>
        <?php endforeach; ?>

        <h3 class="section-title">Address</h3>
        <div class="address-box">
            <p class="address-name">
                <?= htmlspecialchars($namaUser); ?> (<?= htmlspecialchars($telpUser); ?>)
            </p>
            
            <p class="address-detail">
                <?= htmlspecialchars($alamatUser); ?>
            </p>
        </div>

        <h3 class="section-title">Payment Method</h3>
        <div class="payment-box">
            <span><?= $payment ?> - <?= $sub ?></span>
            <button type="button" onclick="window.location.href='metode-pembayaran.php'">Change</button>
        </div>
    </section>

    <aside class="right">
        <h3 class="section-title">Summary</h3>
        <div class="summary">
            <div class="row">
                <span>Total Items (<?= $totalItem ?>)</span>
                <span>Rp <?= number_format($totalHarga, 0, ',', '.') ?></span>
            </div>
            <div class="row">
                <span>Shipping</span>
                <span style="color: green;">Free</span>
            </div>
            <hr>
            <div class="row total">
                <span>Subtotal</span>
                <span>Rp <?= number_format($totalHarga, 0, ',', '.') ?></span>
            </div>
        </div>

        <form method="POST">
            <input type="hidden" name="idPromo" value="<?= $_SESSION['idPromo'] ?? '' ?>">
            <input type="hidden" name="metodePembayaran" value="<?= $payment ?> <?= $sub ?>">
            <input type="hidden" name="ekspedisi" value="J&T Express">
            
            <?php if (isset($error_sp)): ?>
                <p style="color: red; font-size: 12px; margin-bottom: 10px;"><?= $error_sp ?></p>
            <?php endif; ?>

            <button type="submit" name="btn_checkout" class="checkout-btn">Confirm Checkout</button>
        </form>
    </aside>
</main>

<?php include '../includes/footer.php'; ?>