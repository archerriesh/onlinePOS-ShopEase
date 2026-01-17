<?php
session_start();

$pageCSS = '../../css/admin/liat-produk.css';

include __DIR__ . '/../../includes/header-seller.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';

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
    $sqlReview = "SELECT r.rating, r.isiKomentar, r.tglReview FROM tbReview r WHERE r.idProduk = ? AND r.rating = ? ORDER BY r.tglReview DESC";
    $stmtReview = mysqli_prepare($conn, $sqlReview);
    mysqli_stmt_bind_param($stmtReview, "si", $idProduk, $filterRating);
} else {
    $sqlReview = "SELECT r.rating, r.isiKomentar, r.tglReview FROM tbReview r WHERE r.idProduk = ? ORDER BY r.tglReview DESC";
    $stmtReview = mysqli_prepare($conn, $sqlReview);
    mysqli_stmt_bind_param($stmtReview, "s", $idProduk);
}

mysqli_stmt_execute($stmtReview);
$resultReview = mysqli_stmt_get_result($stmtReview);
$reviews = [];
while ($row = mysqli_fetch_assoc($resultReview)) { $reviews[] = $row; }

$basePath = "../../foto/produk/"; 
$extensions = ['webp', 'jpg', 'jpeg', 'png'];
$gambarProduk = "../../assets/img/default.jpg"; 

foreach ($extensions as $ext) {
    $fileCek = $basePath . $produk['idProduk'] . "." . $ext;
    if (file_exists($fileCek)) {
        $gambarProduk = $fileCek;
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
                        $ratingValue = round($produk['rating']);
                        echo str_repeat('★', $ratingValue);
                        echo str_repeat('☆', 5 - $ratingValue);
                    ?>
                </div>
            </div>

            <form class="rating-filter">
                <?php for($i=1; $i<=5; $i++): ?>
                    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>">
                    <label for="star<?= $i ?>"><span class="star"><?= str_repeat('★', $i) ?></span> <?= $i ?> star</label>
                <?php endfor; ?>
            </form>
        </div>

        <div class="review-list">
            <?php if (count($reviews) > 0): ?>
                <?php foreach ($reviews as $r): ?>
                    <div class="review-card">
                        <div class="avatar"></div>
                        <div>
                            <div class="review-stars">
                                <?php
                                    $fullStar = (int)$r['rating'];
                                    echo str_repeat('★', $fullStar);
                                    echo str_repeat('☆', 5 - $fullStar);
                                ?>
                            </div>
                            <p><?= htmlspecialchars($r['isiKomentar']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="opacity:0.6;">Belum ada review untuk produk ini</p>
            <?php endif; ?>
        </div>
    </div>
</section>
</main>

<script>
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

if (window.location.hash === '#review') {
    const reviewSection = document.getElementById('review');
    if (reviewSection) reviewSection.scrollIntoView({ behavior: 'smooth' });
}
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>