<?php
$pageCSS = '../css/history.css';
include '../includes/header-main.php';
?>

<div class="shop-header">
    <div class="order-tabs">
        <span class="active">All</span>
        <span>Too Ship</span>
        <span>To Receive</span>
        <span>Completed</span>
    </div>

    <div class="order-card">

        <div class="store-name">Dream Door</div>

        <div class="product-item">
            <img src="../images/jorts.png" alt="Jorts">

            <div class="product-info">
                <p class="product-name">Jorts Starry Night - Dirty Grey</p>
            </div>

            <div class="product-qty">1</div>
            <div class="product-price">Rp230.000</div>
        </div>

        <div class="divider"></div>

        <div class="product-item">
            <img src="../images/tee.png" alt="Tee">

            <div class="product-info">
                <p class="product-name">Creamy Cake Layer Tee - Cream</p>
            </div>

            <div class="product-qty">1</div>
            <div class="product-price">Rp79.000</div>
        </div>

        <div class="order-footer">
            <div class="total-text">
                Total Order: <span>Rp309.000</span>
            </div>

            <button class="review-btn">Review</button>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>