<?php
include 'includes/dbOnlinePOS.php';
include 'includes/header-main.php';
?>

<div class="container landing-container">
    <div class="row align-items-center w-100">
        <div class="col-12 col-md-6 text-center order-1 order-md-2 mb-4 mb-md-0">
            <img src="foto/landing.png" class="img-fluid auth-img" alt="ShopEase Illustration">
        </div>
        <div class="col-12 col-md-6 order-2 order-md-1">
            <h1 class="auth-title fw-bold text-center">Welcome to ShopEase</h1>
            <p class="auth-subtitle mb-4 text-center">
                an online shopping platform designed to deliver convenience at every step.
                From discovering products to completing secure payments, ShopEase ensures
                a seamless, efficient, and comfortable shopping experience for everyone.            
            </p>
            <p class="auth-subtitle mb-4 text-center">Choose a role</p>

            <div class="row justify-content-center g-4 role-wrapper">
                <div class="col-4 col-md-4 text-center">
                    <a href="pages/sign-in-page.php" class="role-circle">
                        <span>Buyer</span>
                    </a>
                </div>

                <div class="col-4 col-md-4 text-center">
                    <a href="pages/sign-in-page.php" class="role-circle">
                        <span>Seller</span>
                    </a>
                </div>

                <div class="col-4 col-md-4 text-center">
                    <a href="pages/sign-in-page.php" class="role-circle">
                        <span>Staff</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>