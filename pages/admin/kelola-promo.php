<?php
$pageCSS = '../../css/admin/kelola-promo.css';
include __DIR__ . '/../../includes/header-admin.php';
?>

<div class="kelola-promo-page">

    <div class="kelola-header">
        <h1>Manage Voucher</h1>
        <span class="subtitle">Edit, update, or delete your promo</span>
    </div>

    <div class="promo-card">

        <div class="promo-badge">%</div>

        <form class="promo-form" method="post">

            <div class="form-group">
                <label>Promo Name</label>
                <input type="text" name="namaPromo" required>
            </div>

            <div class="form-group">
                <label>Promo Type</label>
                <select name="tipePromo" required>
                    <option value="">-- Select Type --</option>
                    <option value="diskon">Diskon</option>
                    <option value="cashback">Cashback</option>
                </select>
            </div>

            <div class="form-group">
                <label>Minimal Transaction (Rp)</label>
                <input type="number" name="minimalTransaksi" required>
            </div>

            <div class="form-group">
                <label>Payment Method</label>
                <select name="jenisPembayaran" required>
                    <option value="">-- Select Payment --</option>
                    <option value="qris">QRIS</option>
                    <option value="cod">COD</option>
                    <option value="transferbank">Transfer Bank</option>
                    <option value="emoney">E-Money</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Discount (%)</label>
                    <input type="number" name="persentasePotongan">
                </div>

                <div class="form-group">
                    <label>Cashback (Rp)</label>
                    <input type="number" name="nominalPotongan">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="startDate" required>
                </div>

                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" name="endDate" required>
                </div>
            </div>

            <div class="promo-actions">
                <button type="button" class="btn-delete">Delete</button>
                <button type="submit" class="btn-save">Save Changes</button>
            </div>

        </form>

    </div>

    <div class="back-wrapper">
        <a href="promo-seller.php" class="btn-back">← Back to Voucher List</a>
    </div>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
