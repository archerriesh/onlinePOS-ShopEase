<?php
$pageCSS = '../css/metode-pembayaran.css';
include '../includes/header-main.php';
?>

<div class="overlay">
    <div class="payment-card">

        <h2>Select a payment method</h2>

        <div class="method">
            <label class="radio">
                <input type="radio" name="payment">
                <span class="custom-radio"></span>
                Virtual account
            </label>

            <div class="logos">
                <span class="arrow">&lt;</span>
                <img src="bca.png" alt="BCA">
                <img src="mandiri.png" alt="Mandiri">
                <img src="bsi.png" alt="BSI">
                <span class="arrow">&gt;</span>
            </div>
        </div>

        <div class="method">
            <label class="radio">
                <input type="radio" name="payment">
                <span class="custom-radio"></span>
                E-Wallet
            </label>

            <div class="logos">
                <span class="arrow">&lt;</span>
                <img src="dana.png" alt="DANA">
                <img src="shopeepay.png" alt="ShopeePay">
                <img src="gopay.png" alt="Gopay">
                <span class="arrow">&gt;</span>
            </div>
        </div>

        <div class="method">
            <label class="radio">
                <input type="radio" name="payment">
                <span class="custom-radio"></span>
                QRIS
            </label>

            <div class="logos qris">
                <img src="qris.png" alt="QRIS">
            </div>
        </div>

        <button class="choose-btn">Choose this payment</button>

    </div>
</div>

<?php include '../includes/footer.php'; ?>