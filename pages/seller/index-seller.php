<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../includes/dbOnlinePOS.php';
$query = "SELECT idProduk, namaProduk, harga FROM tbproduk";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("QUERY ERROR: " . mysqli_error($conn));
}

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

// ext gambar
$basePath = "../../foto/produk/";
$extensions = ['webp', 'jpg', 'jpeg'];

// CSS halaman
$pageCSS = '../../css/seller-index.css';
include("../../includes/header-seller.php");
?>

<section class="container">
    <div class="store-header">
        <span>My products</span>
        <h1>Tokoku</h1>

        <!-- ADD PRODUCT -->
        <a href="addProduct-seller.php" class="add-btn">
            Add new product ⊕
        </a>
    </div>

    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <?php
            $gambarProduk = "../../assets/img/default.jpg";

            // Cek pergambar
            foreach ($extensions as $ext) {
                $path = $basePath . $p['idProduk'] . '.' . $ext;
                if (file_exists($path)) {
                    $gambarProduk = $path;
                    break;
                }
            }
            ?>

            <div class="card">
                <div class="card-actions">
                    <!-- EDIT -->
                    <a href="editProduct-seller.php?id=<?= $p['idProduk'] ?>" class="edit-btn">✎</a>

                    <!-- DELETE -->
                    <span class="delete-btn" data-id="<?= $p['idProduk'] ?>">
                        🗑
                    </span>
                </div>

                <!-- IMAGE -->
                <img src="<?= $gambarProduk ?>">

                <!-- Nama Produk -->
                <h4><?= htmlspecialchars($p['namaProduk']) ?></h4>

                <!-- Harga -->
                <p>Rp <?= number_format($p['harga'], 0, ',', '.') ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- DELETE MODAL -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>

        <div class="trash-icon">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
            </svg>
        </div>

        <h2>Are you sure you want to delete this product?</h2>

        <form id="deleteForm" method="POST" action="deleteProduct-seller.php">
            <input type="hidden" name="idProduk" id="deleteProductId">
            <button type="submit" class="delete-confirm-btn">Delete</button>
        </form>
    </div>
</div>

<!-- Notif -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="notification success" id="notification">
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="notification error" id="notification">
        <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<script>



// Modal delete
const deleteModal = document.getElementById('deleteModal');
const deleteBtns = document.querySelectorAll('.delete-btn');
const closeBtn = deleteModal.querySelector('.close-btn');
const deleteInput = document.getElementById('deleteProductId');

deleteBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const productId = btn.getAttribute('data-id');
        deleteInput.value = productId;
        deleteModal.style.display = 'flex';
    });
});

closeBtn.addEventListener('click', () => {
    deleteModal.style.display = 'none';
});

//click diluar langsung close
window.addEventListener('click', (event) => {
    if (event.target === deleteModal) deleteModal.style.display = 'none';
});

// Auto hide notif
const notification = document.getElementById('notification');
if (notification) {
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
