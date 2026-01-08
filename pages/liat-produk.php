<?php
$pageCSS = '../css/liat-produk.css';
include '../includes/header-main.php';
include '../includes/dbOnlinePOS.php';

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

if (!$stmt) {
    die("SQL Error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "s", $idProduk);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($result);

if (!$produk) {
    echo "Produk tidak ditemukan";
    exit;
}

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
            <img 
                src="<?= $gambarProduk; ?>" 
                alt="<?= htmlspecialchars($produk['namaProduk']); ?>">
        </div>

        <div class="info">

            <h1 class="title"><?= htmlspecialchars($produk['namaProduk']); ?></h1>

            <p class="brand"><?= htmlspecialchars($produk['namaPenjual']); ?></p>

            <div class="rating">
                <span class="stars">
                    <?= number_format((float)$produk['rating'], 1); ?> ★
                </span>
                <span class="reviews">
                    <?= (int)$produk['totalReview']; ?> reviews
                </span>
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
                        value="1" 
                        min="1" 
                        max="<?= $produk['stok']; ?>">
                    <button class="btn-icon plus" type="button">+</button>
                </div>

                <div class="action-row">
                    <a href="co-keranjang.php?id=<?= $produk['idProduk']; ?>">
                        <button class="btn add">🛒 Add to cart</button>
                    </a>

                    <a href="co-langsung.php?id=<?= $produk['idProduk']; ?>">
                        <button class="btn buy">Buy now</button>
                    </a>
                </div>

            </div>

        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
