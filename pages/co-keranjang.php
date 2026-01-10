<?php
session_start();

$pageCSS = '../css/co-keranjang.css';
include '../includes/header-main.php';
include '../includes/dbOnlinePOS.php';

/* UPDATE CART */
if (isset($_POST['update_cart'])) {
    $idProduk = $_POST['idProduk'];
    $qty = (int)$_POST['qty'];

    if ($qty <= 0) {
        unset($_SESSION['cart'][$idProduk]);
    } else {
        $_SESSION['cart'][$idProduk] = $qty;
    }

    header("Location: co-keranjang.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$totalItem = 0;
$totalHarga = 0;
?>

<main class="cart-page">
<section class="layout">

<div class="cart-items">
<h3 class="section-title"><?= count($cart); ?> items</h3>

<?php if (empty($cart)) { ?>
    <p class="empty">Cart is empty</p>
<?php } else { ?>

<?php foreach ($cart as $idProduk => $qty): ?>

<?php
$sql = "SELECT idProduk, namaProduk, harga FROM tbProduk WHERE idProduk = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $idProduk);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($result);
if (!$produk) continue;

$subtotal = $produk['harga'] * $qty;
$totalItem += $qty;
$totalHarga += $subtotal;
?>

<div class="cart-item">

    <div class="thumb"></div>

    <div class="item-info">
        <div class="item-name">
            <?= htmlspecialchars($produk['namaProduk']); ?>
        </div>

        <form method="POST" class="qty-form">
            <input type="hidden" name="idProduk" value="<?= $idProduk; ?>">

            <div class="qty">
                <button 
                    type="button" 
                    class="btn-icon"
                    onclick="updateQty('<?= $idProduk; ?>', <?= $qty - 1; ?>)">
                    -
                </button>

                <input type="number" value="<?= $qty; ?>" readonly>

                <button 
                    type="button" 
                    class="btn-icon"
                    onclick="updateQty('<?= $idProduk; ?>', <?= $qty + 1; ?>)">
                    +
                </button>
            </div>
        </form>
    </div>

    <div class="price">
        Rp <?= number_format($subtotal, 0, ',', '.'); ?>
    </div>

</div>

<?php endforeach; ?>
<?php } ?>

</div>

<aside class="summary">

<!-- 🔥 VOUCHER SECTION (INI YANG KURANG) -->
<div class="voucher">Voucher</div>
<div class="voucher-box"></div>

<div class="summary-panel">

    <div class="row">
        <span><?= $totalItem; ?> Item</span>
        <span>Rp <?= number_format($totalHarga, 0, ',', '.'); ?></span>
    </div>

    <div class="row">
        <span>Voucher</span>
        <span>-Rp 0</span>
    </div>

    <hr>

    <div class="row total">
        <span>Subtotal</span>
        <span>Rp <?= number_format($totalHarga, 0, ',', '.'); ?></span>
    </div>

</div>

<a href="co-langsung.php">
    <button class="checkout-btn">Checkout</button>
</a>

</aside>

</section>
</main>

<script>
function updateQty(idProduk, qty) {
    if (qty <= 0) {
        if (!confirm("Are you sure you want to remove the product from your cart?")) {
            return;
        }
    }

    const form = document.createElement("form");
    form.method = "POST";

    form.innerHTML = `
        <input type="hidden" name="update_cart" value="1">
        <input type="hidden" name="idProduk" value="${idProduk}">
        <input type="hidden" name="qty" value="${qty}">
    `;

    document.body.appendChild(form);
    form.submit();
}
</script>

<?php include '../includes/footer.php'; ?>
