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
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ShopEase - Tokoku</title>

<!-- CSS -->
<link rel="stylesheet" href="/ONLINEPOS-SHOPEASE/css/seller-index.css">
</head>

<body>

<header class="navbar">
    <h2 class="logo">ShopEase</h2>
    <nav>
        <a href="#">Home</a>
        <a href="#">Products</a>
        <a href="#">Sales</a>
    </nav>
</header>

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
            <div class="card">

                <div class="card-actions">
                    <!-- EDIT -->
                    <a href="editProduct-seller.php?id=<?= $p['idProduk'] ?>" class="edit-btn">
                        ✎
                    </a>

                    <!-- DELETE -->
                    <span class="delete-btn"
                          onclick="openDeleteModal(
                              <?= $p['idProduk'] ?>,
                              '<?= htmlspecialchars($p['namaProduk'], ENT_QUOTES) ?>'
                          )">
                        🗑
                    </span>
                </div>

                <!-- IMAGE -->
                <img src="../../images/placeholder.png" alt="product">

                <!-- PRODUCT NAME -->
                <h4><?= htmlspecialchars($p['namaProduk']) ?></h4>

                <!-- PRICE -->
                <p>Rp <?= number_format($p['harga'], 0, ',', '.') ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- DELETE MODAL -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeDeleteModal()">&times;</span>

        <div class="trash-icon">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
            </svg>
        </div>

        <h2>Are you sure you want delete this product?</h2>

        <form id="deleteForm" method="POST" action="deleteProduct-seller.php">
            <input type="hidden" name="idProduk" id="deleteProductId">
            <button type="submit" class="delete-confirm-btn">
                Delete
            </button>
        </form>
    </div>
</div>

<!-- Notifikasi - PINDAHKAN KE SINI -->
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
// HAPUS DUPLIKAT - HANYA SATU FUNCTION
function openDeleteModal(productId, productName) {
    console.log('Opening modal for ID:', productId); // untuk debug
    const modal = document.getElementById('deleteModal');
    document.getElementById('deleteProductId').value = productId;
    modal.style.display = 'flex';
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'none';
}

// Close modal ketika klik di luar
window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        closeDeleteModal();
    }
}

// Auto hide notification
const notification = document.getElementById('notification');
if (notification) {
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}
</script>

</body>
</html>
