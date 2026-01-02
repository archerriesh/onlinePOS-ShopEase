<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: pages/sign-in-page.php");
    exit;
}

include '../includes/header-main.php';
?>

<div class="container py-5 mt-5">
    <div class="text-center mb-5">
        <h1 class="auth-title">Welcome to ShopEase</h1>
        <p class="auth-subtitle">
            Your simple, fast, and reliable online shopping experience.
        </p>
    </div>

    <div class="row justify-content-center g-4">

        <!-- Lihat Produk -->
        <div class="col-12 col-md-4">
            <a href="pages/liat-produk.php" class="feature-card">
                <h4>View Products</h4>
                <p>Browse and explore available products.</p>
            </a>
        </div>

        <!-- Checkout Keranjang -->
        <div class="col-12 col-md-4">
            <a href="pages/co-keranjang.php" class="feature-card">
                <h4>Cart Checkout</h4>
                <p>Checkout items added to your cart.</p>
            </a>
        </div>

        <!-- Checkout Langsung -->
        <div class="col-12 col-md-4">
            <a href="pages/co-langsung.php" class="feature-card">
                <h4>Direct Checkout</h4>
                <p>Buy instantly without using cart.</p>
            </a>
        </div>

    </div>
</div>

<?php
include '../includes/footer.php';
?>
