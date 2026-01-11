<?php
$pageCSS = '../../css/admin/liat-promo.css';

include __DIR__ . '/../../includes/header-admin.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';
?>

<div class="promo-page">

    <!-- ===== BUTTON TOP ===== -->
    <div class="button-container">
        <button class="btn btn-primary"
            onclick="window.location.href='promo-seller.php'">
            Your voucher
        </button>

        <button class="btn btn-secondary"
            onclick="window.location.href='addPromo-seller.php'">
            Add new voucher
        </button>
    </div>

    <!-- ===== VOUCHER SLIDER ===== -->
    <div class="voucher-container">

        <button class="arrow arrow-left">&lt;</button>
        <button class="arrow arrow-right">&gt;</button>

        <div class="voucher-grid">

            <!-- CARD 1 -->
            <div class="voucher-card">
                <div class="voucher-icon">%</div>
                <div class="voucher-divider"></div>
                <div class="voucher-content">
                    <div class="voucher-title">Discount</div>
                    <div class="voucher-value">10%</div>
                    <div class="voucher-terms">*Term and Condition</div>
                </div>
            </div>

            <!-- CARD 2 -->
            <div class="voucher-card">
                <div class="voucher-icon">%</div>
                <div class="voucher-divider"></div>
                <div class="voucher-content">
                    <div class="voucher-title">Discount</div>
                    <div class="voucher-value">20%</div>
                    <div class="voucher-terms">*Term and Condition</div>
                </div>
            </div>

            <!-- CARD 3 -->
            <div class="voucher-card">
                <div class="voucher-icon">%</div>
                <div class="voucher-divider"></div>
                <div class="voucher-content">
                    <div class="voucher-title">Discount</div>
                    <div class="voucher-value">30%</div>
                    <div class="voucher-terms">*Term and Condition</div>
                </div>
            </div>

            <!-- CARD 4 -->
            <div class="voucher-card">
                <div class="voucher-icon">%</div>
                <div class="voucher-divider"></div>
                <div class="voucher-content">
                    <div class="voucher-title">Discount</div>
                    <div class="voucher-value">15%</div>
                    <div class="voucher-terms">*Term and Condition</div>
                </div>
            </div>

            <!-- CARD 5 -->
            <div class="voucher-card">
                <div class="voucher-icon">%</div>
                <div class="voucher-divider"></div>
                <div class="voucher-content">
                    <div class="voucher-title">Discount</div>
                    <div class="voucher-value">25%</div>
                    <div class="voucher-terms">*Term and Condition</div>
                </div>
            </div>

            <!-- CARD 6 -->
            <div class="voucher-card">
                <div class="voucher-icon">%</div>
                <div class="voucher-divider"></div>
                <div class="voucher-content">
                    <div class="voucher-title">Discount</div>
                    <div class="voucher-value">50%</div>
                    <div class="voucher-terms">*Term and Condition</div>
                </div>
            </div>

        </div> 
    </div> 

</div> 

<?php include __DIR__ . '/../../includes/footer.php'; ?>
