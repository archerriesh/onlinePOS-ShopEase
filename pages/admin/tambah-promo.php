<?php
$pageCSS = '../../css/admin/tambah-promo.css';

include __DIR__ . '/../../includes/header-admin.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $namaPromo         = $_POST['namaPromo'] ?? '';
    $tipePromo         = $_POST['tipePromo'] ?? '';
    $minimalTransaksi  = $_POST['minimalTransaksi'] ?? null;
    $jenisPembayaran   = $_POST['jenisPembayaran'] ?? '';
    $startDate         = $_POST['startDate'] ?? null;
    $endDate           = $_POST['endDate'] ?? null;

    $persentasePotongan = null;
    $nominalPotongan    = null;

    if ($tipePromo === 'diskon') {
        $persentasePotongan = $_POST['persentasePotongan'] ?: null;
    }

    if ($tipePromo === 'cashback') {
        $nominalPotongan = $_POST['nominalPotongan'] ?: null;
    }

    $stmt = mysqli_prepare(
        $conn,
        "CALL sp_insert_promo(?, ?, ?, ?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ssississ",
        $namaPromo,
        $tipePromo,
        $minimalTransaksi,
        $jenisPembayaran,
        $persentasePotongan,
        $nominalPotongan,
        $startDate,
        $endDate
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        header("Location: liat-promo.php?success=1");
        exit;
    } else {
        echo "<script>alert('Gagal menambahkan promo');</script>";
    }
}
?>

<div class="voucher-page">

    <div class="voucher-tab-container">
        <button class="btn-tab"
            onclick="window.location.href='liat-promo.php'">
            Voucher
        </button>
        <button class="btn-tab active">
            Add new voucher
        </button>
    </div>

    <div class="voucher-card">
        <form method="post">

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

            <button type="submit" class="btn-add">
                Add Promo
            </button>

        </form>
    </div>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
