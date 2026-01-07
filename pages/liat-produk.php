<?php
$pageCSS = '../css/liat-produk.css';
include '../includes/header-main.php';
include '../includes/dbOnlinePOS.php';

$idProduk = $_GET['id'] ?? '';

$sql = "
SELECT 
    p.idProduk,
    p.namaProduk,
    p.harga,
    p.stok,
    p.keterangan,
    pen.namaPenjual,
    IFNULL(AVG(r.rating), 0) AS rating,
    COUNT(r.idReview) AS totalReview
FROM tbproduk p
JOIN tbpenjual pen ON p.idPenjual = pen.idPenjual
LEFT JOIN tbreview r ON p.idProduk = r.idProduk
WHERE p.idProduk = ?
GROUP BY p.idProduk
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
?>

<main class="container">
    <section class="product-card">
        <div class="image-wrap">
            <img src="https://via.placeholder.com/420x520" alt="Produk">
        </div>

        <div class="info">

            <h1 class="title"><?= $produk['namaProduk']; ?></h1>

            <p class="brand"><?= $produk['namaPenjual']; ?></p>

            <div class="rating">
                <span class="stars">
                    <?= number_format($produk['rating'], 1); ?> ★
                </span>
                <span class="reviews">
                    <?= $produk['totalReview']; ?> reviews
                </span>
            </div>

            <p class="price">
                Rp <?= number_format($produk['harga'], 0, ',', '.'); ?>
            </p>

            <p class="description">
                <?= nl2br($produk['keterangan']); ?>
            </p>

            <div class="purchase">

            <div class="qty">
                <button class="btn-icon minus" type="button">−</button>
                <input type="number" value="1" min="1">
                <button class="btn-icon plus" type="button">+</button>
            </div>

            <div class="action-row">
                <a href="co-keranjang.php"><button class="btn add">🛒 Add to cart</button></a>
                <a href="co-langsung.php"><button class="btn buy">Buy now</button></a>
            </div>

            </div>

        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>