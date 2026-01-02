<?php
$pageCSS = '../css/liat-produk.css';
include '../includes/header-main.php';
?>

<main class="container">
<section class="product-card" aria-label="Produk">
    <div class="image-wrap">
    <img src="https://via.placeholder.com/420x520?text=Cardigan+Pink" alt="Cardigan Pink" />
    </div>

    <div class="info">
    <h1 class="title">Cardigan Zip Bunnies - PINK</h1>
    <p class="brand">COLORBOX Official</p>

    <div class="rating" aria-label="Rating">
        <span class="stars">4.3 ★★★★☆</span>
        <span class="reviews">38 reviews</span>
        <span class="sold">59 sold</span>
    </div>

    <p class="description">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor
        incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.
    </p>

    <div class="purchase">

        <div class="quantity">
            <span>Quantity</span>
            <div class="qty">
            <button class="btn-icon">-</button>
            <input type="number" value="1" min="1">
            <button class="btn-icon">+</button>
            </div>
        </div>

        <div class="action-row">
            <button class="btn add">🛒 Add to cart</button>
            <button class="btn buy">Buy now</button>
        </div>

        </div>

</section>
</main>

<?php include '../includes/footer.php'; ?>