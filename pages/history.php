<?php
$pageCSS = '../css/history.css';
include '../includes/header-main.php';
?>

<div class="history-page">
    <div class="shop-header">
        <div class="order-tabs">
            <ul>
                <li><a href="">All</a></li>
                <li><a href="">To Ship</a></li>
                <li><a href="">To Receive</a></li>
                <li><a href="">Completed</a></li>
            </ul>
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

                <a href="nulis-review.php" class="review-btn">Review</a>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>