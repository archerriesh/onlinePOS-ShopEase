<?php
session_start();

$pageCSS = '../css/liat-produk.css';
include '../includes/header-main.php';
include '../includes/dbOnlinePOS.php';

$idProduk = $_GET['id'] ?? '';
$filterRating = $_GET['rating'] ?? '';

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

if ($filterRating !== '') {
    $sqlReview = "
        SELECT rating, isiKomentar, tglReview, balasanPenjual
        FROM tbReview
        WHERE idProduk = ?
        AND rating = ?
        ORDER BY tglReview DESC
    ";
    $stmtReview = mysqli_prepare($conn, $sqlReview);
    mysqli_stmt_bind_param($stmtReview, "si", $idProduk, $filterRating);
} else {
    $sqlReview = "
        SELECT rating, isiKomentar, tglReview, balasanPenjual
        FROM tbReview
        WHERE idProduk = ?
        ORDER BY tglReview DESC
    ";
    $stmtReview = mysqli_prepare($conn, $sqlReview);
    mysqli_stmt_bind_param($stmtReview, "s", $idProduk);
}

mysqli_stmt_execute($stmtReview);
$resultReview = mysqli_stmt_get_result($stmtReview);

$reviews = [];
while ($row = mysqli_fetch_assoc($resultReview)) {
    $reviews[] = $row;
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
                <input type="number" id="qtyInput" value="1" min="1" max="<?= $produk['stok']; ?>" readonly>
                <button class="btn-icon plus" type="button">+</button>
            </div>

            <div class="action-row">
                <button class="btn add" id="addToCart">🛒 Add to cart</button>
                <button class="btn buy" id="buyNow">Buy now</button>
            </div>

        </div>

    </div>
</section>

<section class="review-wrapper" id="review">

    <div class="review-content">

        <h1>Product Review</h1>

        <div class="rating-summary">

            <div class="left-rating">
                <div class="score"><?= number_format((float)$produk['rating'], 1); ?></div>
                <div class="stars">
                    <?php
                        $rating = round($produk['rating']);
                        echo str_repeat('★', $rating);
                        echo str_repeat('☆', 5 - $rating);
                    ?>
                </div>
            </div>

            <form class="rating-filter">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>">
                    <label for="star<?= $i ?>">
                        <span class="star"><?= str_repeat('★', $i) ?></span> <?= $i ?> star
                    </label>
                <?php endfor; ?>
            </form>

        </div>

        <div class="review-list">
            <?php if ($reviews): ?>
                <?php foreach ($reviews as $r): ?>
                    <div class="review-card">
                        <div class="avatar"></div>
                        <div>
                            <div class="review-stars">
                                <?= str_repeat('★', $r['rating']) . str_repeat('☆', 5 - $r['rating']); ?>
                            </div>
                            <p><?= htmlspecialchars($r['isiKomentar']); ?></p>

                            <?php if (!empty($r['balasanPenjual'])): ?>
                                <div class="seller-reply" style="margin-top: 10px; padding: 12px; background: #f9f9f9; border-left: 3px solid #5D5A43; border-radius: 4px;">
                                    <strong style="display: block; font-size: 13px; color: #5D5A43; margin-bottom: 5px;">
                                        Seller Response:
                                    </strong>
                                    <p style="margin: 0; font-style: italic; font-size: 14px; color: #555;">
                                        <?= htmlspecialchars($r['balasanPenjual']); ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="opacity:0.6;">There's no review yet.</p>
            <?php endif; ?>
        </div>

    </div>
</section>

</main>

<script>
const qtyInput = document.getElementById('qtyInput');
const maxStock = <?= (int)$produk['stok']; ?>;
const idProduk = <?= json_encode($produk['idProduk']); ?>;
const ratingRadios = document.querySelectorAll('.rating-filter input[type="radio"]');
const urlParams = new URLSearchParams(window.location.search);
const currentRating = urlParams.get('rating');

if (currentRating) {
    ratingRadios.forEach(radio => {
        if (radio.value === currentRating) radio.checked = true;
    });
}

ratingRadios.forEach(radio => {
    radio.addEventListener('click', () => {
        if (radio.value === currentRating) {
            window.location.href = `liat-produk.php?id=${idProduk}#review`;
        } else {
            window.location.href = `liat-produk.php?id=${idProduk}&rating=${radio.value}#review`;
        }
    });
});

document.querySelector('.plus').onclick = () => {
    if (+qtyInput.value < maxStock) qtyInput.value++;
};
document.querySelector('.minus').onclick = () => {
    if (+qtyInput.value > 1) qtyInput.value--;
};

document.getElementById('addToCart').onclick = () => {
    const f = document.createElement('form');
    f.method = 'POST';
    f.action = 'prosess-add-keranjang.php';
    f.innerHTML = `
        <input type="hidden" name="idProduk" value="${idProduk}">
        <input type="hidden" name="qty" value="${qtyInput.value}">
    `;
    document.body.appendChild(f);
    f.submit();
};

document.getElementById('buyNow').onclick = () => {
    location.href = `co-langsung.php?id=${idProduk}&qty=${qtyInput.value}`;
};
</script>

<?php include '../includes/footer.php'; ?>
