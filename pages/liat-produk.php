<?php
session_start();

$pageCSS = '../css/liat-produk.css';
include '../includes/header-main.php';
include '../includes/dbOnlinePOS.php';

/* ======================================
   AMBIL DATA PRODUK
====================================== */
$idProduk = $_GET['id'] ?? '';

if ($idProduk === '') {
    echo "Produk tidak ditemukan";
    exit;
}

$sql = "
SELECT 
    p.idProduk,
    p.namaProduk,
    p.harga,
    p.stok,
    p.keterangan,
    pen.namaPenjual,
    IFNULL(fn_rating_Produk(p.idProduk), 0) AS rating,
    (
        SELECT COUNT(*) 
        FROM tbReview r 
        WHERE r.idProduk = p.idProduk
    ) AS totalReview
FROM tbProduk p
JOIN tbPenjual pen ON p.idPenjual = pen.idPenjual
WHERE p.idProduk = ?
LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $idProduk);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($result);

if (!$produk) {
    echo "Produk tidak ditemukan";
    exit;
}

/* ======================================
   GAMBAR PRODUK
====================================== */
$basePath = "../foto/produk/";
$extensions = ['webp', 'jpg', 'jpeg', 'png'];
$gambarProduk = "../assets/img/default.jpg";

foreach ($extensions as $ext) {
    $file = $basePath . $produk['idProduk'] . "." . $ext;
    if (file_exists($file)) {
        $gambarProduk = $file;
        break;
    }
}
?>

<main class="container">
<section class="product-card">

    <div class="image-wrap">
        <img src="<?= $gambarProduk; ?>" alt="<?= htmlspecialchars($produk['namaProduk']); ?>">
    </div>

    <div class="info">

        <h1 class="title"><?= htmlspecialchars($produk['namaProduk']); ?></h1>
        <p class="brand"><?= htmlspecialchars($produk['namaPenjual']); ?></p>

        <div class="rating">
            <span><?= number_format((float)$produk['rating'], 1); ?> ★</span>
            <span><?= (int)$produk['totalReview']; ?> reviews</span>
        </div>

        <p class="price">
            Rp <?= number_format($produk['harga'], 0, ',', '.'); ?>
        </p>

        <p class="description">
            <?= nl2br(htmlspecialchars($produk['keterangan'])); ?>
        </p>

        <div class="purchase">

            <div class="qty">
                <button class="btn-icon minus" type="button">−</button>

                <input 
                    type="number"
                    id="qtyInput"
                    value="1"
                    min="1"
                    max="<?= $produk['stok']; ?>"
                    readonly
                >

                <button class="btn-icon plus" type="button">+</button>
            </div>

            <div class="action-row">
                <button class="btn add" id="addToCart">
                    🛒 Add to cart
                </button>

                <button class="btn buy" id="buyNow">
                    Buy now
                </button>
            </div>

        </div>

    </div>
</section>
</main>

<script>
const qtyInput = document.getElementById('qtyInput');
const plusBtn = document.querySelector('.btn-icon.plus');
const minusBtn = document.querySelector('.btn-icon.minus');
const addBtn = document.getElementById('addToCart');
const buyBtn = document.getElementById('buyNow');

const maxStock = <?= (int)$produk['stok']; ?>;
const idProduk = <?= json_encode($produk['idProduk']); ?>;

plusBtn.addEventListener('click', () => {
    let qty = parseInt(qtyInput.value);
    if (qty < maxStock) qtyInput.value = qty + 1;
});

minusBtn.addEventListener('click', () => {
    let qty = parseInt(qtyInput.value);
    if (qty > 1) qtyInput.value = qty - 1;
});

addBtn.addEventListener('click', () => {
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "co-keranjang.php";

    form.innerHTML = `
        <input type="hidden" name="add_cart" value="1">
        <input type="hidden" name="idProduk" value="${idProduk}">
        <input type="hidden" name="qty" value="${qtyInput.value}">
    `;

    document.body.appendChild(form);
    form.submit();
});

buyBtn.addEventListener('click', () => {
    window.location.href =
        `co-langsung.php?id=${idProduk}&qty=${qtyInput.value}`;
});
</script>

<?php include '../includes/footer.php'; ?>
