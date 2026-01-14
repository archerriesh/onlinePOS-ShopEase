<?php
$pageCSS = '../../css/admin/tambah-promo.css'; 
include __DIR__ . '/../../includes/header-admin.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';
?>

<div class="voucher-page">

    <div class="voucher-tab-container">
        <button class="btn-tab" onclick="window.location.href='liat-promo.php'">
            Voucher
        </button>
        <button class="btn-tab active">Add new voucher</button>
    </div>

    <div class="voucher-card">
        <form method="post">
            <div class="form-group">
                <label>Promo name</label>
                <input type="text" name="promo_name" placeholder="Enter promo name">
            </div>

            <div class="form-group">
                <label>Promo type</label>
                <input type="text" name="promo_type" placeholder="e.g. Discount, Cashback">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date">
                </div>

                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" name="end_date">
                </div>
            </div>

            <div class="form-group">
                <label>Terms and Condition</label>
                <textarea name="terms" placeholder="Describe terms..."></textarea>
            </div>

            <button type="submit" class="btn-add">Add</button>
        </form>
    </div>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>