<?php
session_start();

$pageCSS = '../css/liat-produk.css';
include '../includes/header-main.php';
include '../includes/dbOnlinePOS.php';

/* ======================================
   AMBIL DATA PRODUK
====================================== */
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

// ambil review produk
if ($filterRating !== '') {
    $sqlReview = "
        SELECT 
            r.rating,
            r.isiKomentar,
            r.tglReview
        FROM tbReview r
        WHERE r.idProduk = ?
        AND r.rating = ?
        ORDER BY r.tglReview DESC
    ";

    $stmtReview = mysqli_prepare($conn, $sqlReview);
    mysqli_stmt_bind_param($stmtReview, "si", $idProduk, $filterRating);
} else {
    $sqlReview = "
        SELECT 
            r.rating,
            r.isiKomentar,
            r.tglReview
        FROM tbReview r
        WHERE r.idProduk = ?
        ORDER BY r.tglReview DESC
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
                <input type="radio" id="star1" name="rating" value="1">
                <label for="star1"><span class="star">★</span> 1 star</label>

                <input type="radio" id="star2" name="rating" value="2">
                <label for="star2"><span class="star">★★</span> 2 star</label>

                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3"><span class="star">★★★</span> 3 star</label>

                <input type="radio" id="star4" name="rating" value="4">
                <label for="star4"><span class="star">★★★★</span> 4 star</label>

                <input type="radio" id="star5" name="rating" value="5">
                <label for="star5"><span class="star">★★★★★</span> 5 star</label>
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
    form.action = "prosess-add-keranjang.php";

    form.innerHTML = `
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

const ratingRadios = document.querySelectorAll(
    '.rating-filter input[type="radio"]'
);

const urlParams = new URLSearchParams(window.location.search);
const currentRating = urlParams.get('rating');

if (currentRating) {
    ratingRadios.forEach(radio => {
        if (radio.value === currentRating) {
            radio.checked = true;
        }
    });
}

ratingRadios.forEach(radio => {
    radio.addEventListener('click', () => {

        if (radio.value === currentRating) {
            window.location.href =
                `liat-produk.php?id=${idProduk}#review`;
        } 
        else {
            window.location.href =
                `liat-produk.php?id=${idProduk}&rating=${radio.value}#review`;
        }
    });
});

if (window.location.hash === '#review') {
    const reviewSection = document.getElementById('review');
    if (reviewSection) {
        reviewSection.scrollIntoView({ behavior: 'smooth' });
    }
}
</script>

<?php include '../includes/footer.php'; ?>
