<?php

$pageCSS = '../../css/admin/liat-toko.css';

include __DIR__ . '/../../includes/header-main.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';

?>

<div class="toko-container">

    <div class="toko-header">
        <h1 class="section-title">Toko Terdaftar</h1>
        <button class="add-product">
        </button>
    </div>

    <div class="product-grid">

        <?php for ($i = 0; $i < 10; $i++) { ?>
            <div class="product-card">

                <div class="product-image"></div>

                <div class="product-info">
                    <p class="product-name">nama toko</p>
                </div>

            </div>
        <?php } ?>

    </div>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
