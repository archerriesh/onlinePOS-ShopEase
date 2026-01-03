<?php
$pageCSS = '../css/liat-produk.css';
include '../includes/header-main.php';
?>

<main class="container">
    <section class="product-card">
        <div class="image-wrap">
            <img src="https://via.placeholder.com/420x520" alt="Produk">
        </div>

        <div class="info">

            <h1 class="title">Cardigan Zip Bunnies - PINK</h1>

            <p class="brand">COLORBOX Official</p>

            <div class="rating">
                <span class="stars">4.3 ★★★★☆</span>
                <span class="reviews">38 reviews</span>
                <span class="sold">59 sold</span>
            </div>

            <p class="description">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            </p>

            <div class="purchase">

            <div class="quantity">
                <span>Quantity</span>
                <div class="qty">
                    <input type="number" value="1" min="1">
                </div>
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