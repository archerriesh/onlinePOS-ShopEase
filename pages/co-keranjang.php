<?php
session_start();

$pageCSS = '../css/co-keranjang.css';
include '../includes/header-main.php';
include '../includes/dbOnlinePOS.php';

$idPelanggan = isset($_SESSION['idPelanggan'])
    ? (int) $_SESSION['idPelanggan']
    : 0;

if (
    $idPelanggan > 0 &&
    (isset($_POST['add_cart']) || isset($_POST['update_cart']))
) {
    $idProduk = $_POST['idProduk'];
    $qty = (int) $_POST['qty'];

    $sql = "CALL sp_kelola_keranjang(?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "isi",
        $idPelanggan,
        $idProduk,
        $qty
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_store_result($stmt);
    mysqli_stmt_free_result($stmt);

    while (mysqli_more_results($conn)) {
        mysqli_next_result($conn);
    }

    mysqli_stmt_close($stmt);
    

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
mysqli_stmt_bind_param($stmt, "i", $idPelanggan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$totalItem  = 0;
$totalHarga = 0;
?>

<main class="cart-page">
<section class="layout">

<div class="cart-items">
<h3 class="section-title"><?= mysqli_num_rows($result); ?> items</h3>

<?php if (mysqli_num_rows($result) === 0) { ?>
    <p class="empty">Cart is empty</p>
<?php } else { ?>

<?php while ($row = mysqli_fetch_assoc($result)) : ?>

<?php
$subtotal = $row['jumlah'] * $row['hargaSatuan'];
$totalItem += $row['jumlah'];
$totalHarga += $subtotal;
?>

<div class="cart-item">

    <div class="thumb"></div>

    <div class="item-info">
        <div class="item-name">
            <?= htmlspecialchars($row['namaProduk']); ?>
        </div>

        <form method="POST" class="qty-form">
            <input type="hidden" name="idProduk" value="<?= $row['idProduk']; ?>">

            <div class="qty">
                <button type="button" class="btn-icon"onclick="updateQty('<?= $row['idProduk']; ?>', <?= $row['jumlah'] - 1; ?>)">-</button>

                <input type="number" value="<?= $row['jumlah']; ?>" readonly>

                <button 
                    type="button" 
                    class="btn-icon"
                    onclick="updateQty('<?= $row['idProduk']; ?>', <?= $row['jumlah'] + 1; ?>)">
                    +
                </button>
            </div>
        </form>
    </div>

    <div class="price">
        Rp <?= number_format($subtotal, 0, ',', '.'); ?>
    </div>

</div>

<?php endwhile; ?>
<?php } ?>

</div>

<aside class="summary">

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
    if (qty < 0) return;

    if (qty === 0) {
        if (!confirm("Are you sure you want to remove the product from your cart?")) {
            return;
        }
    }

    const form = document.createElement("form");
    form.method = "POST";
    form.action = "co-keranjang.php";

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
