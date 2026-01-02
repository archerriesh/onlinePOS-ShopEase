<?php
$pageCSS = '../css/co-langsung.css';
include '../includes/header-main.php';
?>

<main class="container">
    <section class="left">
        <h3 class="section-title">Ordered item</h3>

        <div class="order-card">
            <img src="product.png" alt="product">
            <div class="order-info">
                <p class="product-name">Jorts Starry Night - Dirty Grey</p>
            </div>
            <div class="price">Rp230.000</div>
            <div class="qty">1</div>
            <div class="subtotal">Rp230.000</div>
        </div>

        <h3 class="section-title">Address</h3>
        <div class="address-box">
            <p class="address-name">Keano (+62) 80814022008</p>
            <p class="address-detail">
                Jalan Surya Sumanti 37 (masuk gang sebelah Indomaret),
                Sukagalih, Sukajadi, Kota Bandung,
                Bandung Kulon, Jawa Barat (40212)
            </p>
        </div>

        <h3 class="section-title">Payment Method</h3>
        <div class="payment-box">
            <span>Virtual Account Transfer</span>
            <button>See all methods</button>
        </div>
    </section>

    <aside class="right">
        <h3 class="section-title">Voucher</h3>
        <div class="voucher-box"></div>

        <div class="summary">
            <div class="row">
                <span>1 item</span>
                <span>Rp 230.000</span>
            </div>
            <div class="row">
                <span>0 Voucher used</span>
                <span>-Rp.0</span>
            </div>
            <div class="row total">
                <span>SubTotal</span>
                <span>Rp.777.000</span>
            </div>
        </div>

        <button class="checkout-btn">Checkout</button>
    </aside>
</main>

<?php include '../includes/footer.php'; ?>