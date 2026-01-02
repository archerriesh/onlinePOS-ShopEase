<?php
$pageCSS = '../css/co-keranjang.css';
include '../includes/header-main.php';
?>

<main class="container">
    <section class="layout">
        <div class="cart-items" aria-label="Daftar Item">
        <h3 class="section-title">3 items</h3>

        <div class="cart-item">
            <div class="thumb" aria-label="image"></div>

            <div class="item-info">
            <div class="item-name">Pakepakai - Women Caramel Cardigan</div>
            <div class="qty">
                <button class="btn-icon" aria-label="Kurangi">-</button>
                <input type="number" value="1" min="1" max="99" />
                <button class="btn-icon" aria-label="Tambahkan">+</button>
            </div>
            </div>

            <div class="price">Rp. 259.000</div>
        </div>

        <div class="cart-item">
            <div class="thumb" aria-label="image"></div>

            <div class="item-info">
            <div class="item-name">Pakepakai - Women Caramel Cardigan</div>
            <div class="qty">
                <button class="btn-icon" aria-label="Kurangi">-</button>
                <input type="number" value="1" min="1" max="99" />
                <button class="btn-icon" aria-label="Tambahkan">+</button>
            </div>
            </div>

            <div class="price">Rp. 259.000</div>
        </div>

        <div class="cart-item">
            <div class="thumb" aria-label="image">

            </div>
            <div class="item-info">
            <div class="item-name">Pakepakai - Women Caramel Cardigan</div>
            <div class="qty">
                <button class="btn-icon" aria-label="Kurangi">-</button>
                <input type="number" value="1" min="1" max="99" />
                <button class="btn-icon" aria-label="Tambahkan">+</button>
            </div>
            </div>

            <div class="price">Rp. 259.000</div>
        </div>
        </div>

        <aside class="summary" aria-label="Ringkasan">
        <div class="voucher">Voucher</div>
        <div class="voucher-box" aria-hidden="true"></div>

        <div class="summary-panel">
            <div class="row">
                <span>3 Item</span>
                <span>Rp. 777.000</span>
            </div>
            <div class="row">
                <span>0 Voucher used</span>
                <span>-Rp. 0</span>
            </div>

            <hr />

            <div class="row total">
                <span>Subtotal</span>
                <span>Rp. 777.000</span>
            </div>
        </div>

        <button class="checkout-btn" type="button" aria-label="Checkout">
            Checkout
        </button>
        </aside>
  </section>
</main>

<?php include '../includes/footer.php'; ?>