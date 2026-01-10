<?php
session_start();

$pageCSS = '../css/co-keranjang.css';
include '../includes/header-main.php';
include '../includes/dbOnlinePOS.php';

/* ===============================
   AMBIL ID PELANGGAN (TANPA VALIDASI LOGIN)
   idPelanggan = VARCHAR
================================ */
$idPelanggan = $_SESSION['idPelanggan'] ?? '';

/* ===============================
   HANDLE ADD / UPDATE CART
================================ */
if (isset($_POST['add_cart']) || isset($_POST['update_cart'])) {

    $idProduk = $_POST['idProduk']; // VARCHAR
    $qty      = (int) $_POST['qty'];

    if ($qty < 0) $qty = 0;

    $stmt = mysqli_prepare(
        $conn,
        "CALL sp_kelola_keranjang(?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ssi",
        $idPelanggan,
        $idProduk,
        $qty
    );

    mysqli_stmt_execute($stmt);

    // WAJIB bersihin result set SP
    while (mysqli_more_results($conn)) {
        mysqli_next_result($conn);
    }

    mysqli_stmt_close($stmt);

    header("Location: co-keranjang.php");
    exit;
}

/* ===============================
   AMBIL DATA KERANJANG
================================ */
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
?>

<div class="cart-item">

    <div class="thumb"></div>

    <div class="item-info">
        <div class="item-name">
            <?= htmlspecialchars($row['namaProduk']); ?>
        </div>

        <!-- FORM QTY -->
        <form method="POST" class="qty-form">
            <input type="hidden" name="update_cart" value="1">
            <input type="hidden" name="idProduk" value="<?= $row['idProduk']; ?>">

            <div class="qty">
                <button type="button" onclick="changeQty(this, -1)">-</button>

                <input
                    type="number"
                    name="qty"
                    value="<?= $row['jumlah']; ?>"
                    readonly>

                <button type="button" onclick="changeQty(this, 1)">+</button>
            </div>
        </form>
    </div>

    <div class="price">
        Rp <?= number_format($subtotal, 0, ',', '.'); ?>
    </div>

</div>

<?php endwhile; ?>
<?php endif; ?>

</div>

<aside class="summary">
    <div class="voucher">Voucher</div>
    <div class="voucher-box"></div>
    
    <div class="summary-panel">
        <div class="row">
            <span><?= $totalItem; ?> Item</span>
            <span>Rp <?= number_format($totalHarga, 0, ',', '.'); ?></span>
        </div>

        <hr>

        <div class="row total">
            <span>Subtotal</span>
            <span>Rp <?= number_format($totalHarga, 0, ',', '.'); ?></span>
        </div>
    </div>

<button class="checkout-btn">Checkout</button>

</aside>

</section>
</main>

<script>
function changeQty(btn, delta) {
    const form  = btn.closest("form");
    const input = form.querySelector('input[name="qty"]');

    let qty = parseInt(input.value) + delta;

    if (qty < 0) return;

    if (qty === 0 && !confirm("Hapus produk dari keranjang?")) return;

    input.value = qty;
    form.submit();
}
</script>

<?php include '../includes/footer.php'; ?>
