<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: pages/sign-in-page.php");
    exit;
}

$pageCSS = '../css/home-page.css';
include '../includes/header-main.php';
?>
<main class="home-page">
    <section class="hero">
        <div class="home-container">
        <h1>Welcome to ShopEase</h1>
        <p class="subtitle">
            an online shopping platform designed to deliver convenience at every step. From discovering products to completing secure payments, ShopEase ensures a seamless, efficient, and comfortable shopping experience for everyone.
        </p>
        </div>
    </section>

    <section class="trending">
        <h2 class="section-title">Now Trending</h2>
        <div class="cards">
            <article class="card">
                <img src="images/tshirt-cream.jpg" alt="Creamy Cake Layer Tee" />
                <div class="card-content">
                <h3>Creamy Cake Layer Tee - Cream</h3>
                <p class="price">Rp 79.000</p>
                </div>
            </article>

            <article class="card">
                <img src="images/bunnies-pink.jpg" alt="Cardigan Zip Bunnies - PINK" />
                <div class="card-content">
                <h3>Cardigan Zip Bunnies - PINK</h3>
                <p class="price">Rp 259.000</p>
                </div>
            </article>
        
            <article class="card">
                <img src="images/handbag.jpg" alt="Grunge Bow Lace Hand Bag" />
                <div class="card-content">
                <h3>Grunge Bow Lace Hand Bag</h3>
                <p class="price">Rp 377.000</p>
                </div>
            </article>
        
            <article class="card">
                <img src="images/denim-skirt.jpg" alt="Jorts Starry Night - Denim Grey" />
                <div class="card-content">
                <h3>Jorts Starry Night - Dirty Grey</h3>
                <p class="price">Rp 230.000</p>
                </div>
            </article>

            <article class="card">
                <img src="images/converse.jpg" alt="Converse Run Start 90s" />
                <div class="card-content">
                <h3>Converse Run Start 90s</h3>
                <p class="price">Rp 1.399.000</p>
                </div>
            </article>
            </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
