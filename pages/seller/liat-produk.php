<?php
session_start();

$pageCSS = '../../css/liat-produks.css';

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
    $sqlReview = "SELECT r.idReview, r.rating, r.isiKomentar, r.tglReview, r.balasanPenjual
    FROM tbReview r 
    WHERE r.idProduk = ? AND r.rating = ? 
    ORDER BY r.tglReview DESC
    ";
    $stmtReview = mysqli_prepare($conn, $sqlReview);
    mysqli_stmt_bind_param($stmtReview, "si", $idProduk, $filterRating);
} else {
    $sqlReview = "SELECT r.idReview, r.rating, r.isiKomentar, r.tglReview, r.balasanPenjual
    FROM tbReview r 
    WHERE r.idProduk = ? 
    ORDER BY r.tglReview DESC";
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

        <div class="seller-actions" style="margin-top: 30px; display: flex; gap: 10px;">
            <a href="editProduct-seller.php?id=<?= $produk['idProduk']; ?>" 
               style="background-color: #f0ad4e; color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: bold;">
               ✎ Edit Product Info
            </a>
            
            <button onclick="confirmDelete('<?= $produk['idProduk']; ?>')" 
                    style="background-color: #d9534f; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">
               🗑 Non-activate Product
            </button>
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
                    <div class="review-card" style="border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px;">
                        <div class="avatar"></div>
                        <div style="flex: 1;">
                            <div class="review-stars">
                                <?php
                                    $fullStar = (int)$r['rating'];
                                    echo str_repeat('★', $fullStar);
                                    echo str_repeat('☆', 5 - $fullStar);
                                ?>
                                <span style="font-size: 12px; color: #888; margin-left: 10px;">
                                    <?= date('d M Y', strtotime($r['tglReview'])); ?>
                                </span>
                            </div>
                            
                            <p style="margin: 10px 0;"><?= htmlspecialchars($r['isiKomentar']); ?></p>

                            <?php if (!empty($r['balasanPenjual'])): ?>
                                <div class="seller-reply" style="margin-top: 10px; padding: 15px; background: #f4f1ed; border-left: 4px solid #5D5A43; border-radius: 0 8px 8px 0;">
                                    <strong style="color: #5D5A43; font-size: 13px;">Your Response:</strong>
                                    <p style="margin-top: 5px; margin-bottom: 0; font-style: italic; color: #444;">
                                        <?= htmlspecialchars($r['balasanPenjual']); ?>
                                    </p>
                                </div>
                            <?php else: ?>
                                <button type="button" 
                                        onclick="openReplyModal('<?= $produk['idProduk']; ?>', '<?= $r['idReview']; ?>')"
                                        style="margin-top: 10px; background: #5D5A43; color: white; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: bold; transition: 0.3s;">
                                    <i class="fas fa-reply"></i> Reply to Customer
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="opacity:0.6; text-align: center; padding: 40px;">No reviews found for this product.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
</main>

<div id="replyModal" class="modal-overlay" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
    <div class="reply-box" style="background: #E5D9CD; padding: 30px; border-radius: 15px; width: 450px; text-align: center; position: relative;">
        <span onclick="closeReplyModal()" style="position:absolute; right:15px; top:10px; cursor:pointer;">&times;</span>
        
        <h2 style="font-size: 24px; margin-bottom: 10px;">Write your reply</h2>
        <p style="font-size: 14px; color: #555; margin-bottom: 20px;">
            Take a moment to respond and let your customer know you care. Show your appreciation by replying to this review.
        </p>

        <form action="process-reply.php" method="POST">
            <input type="hidden" name="idReview" id="modalIdReview">
            <input type="hidden" name="idProduk" id="modalIdProduk">
            
            <textarea name="isiBalasan" placeholder="Write your reply here..." required
                      style="width: 100%; height: 120px; border-radius: 10px; border: 1px solid #C4B5A5; padding: 10px; margin-bottom: 20px;"></textarea>
            
            <button type="submit" style="background: #5D5A43; color: white; border: none; padding: 12px 50px; border-radius: 10px; cursor: pointer; font-weight: bold;">
                Send
            </button>
        </form>
    </div>
</div>

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

function openReplyModal(idProduk, idReview) {
    document.getElementById('modalIdProduk').value = idProduk;
    document.getElementById('modalIdReview').value = idReview;
    document.getElementById('replyModal').style.display = 'flex';
}

function closeReplyModal() {
    document.getElementById('replyModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('replyModal')) {
        closeReplyModal();
    }
}
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>