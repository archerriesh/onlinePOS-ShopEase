<?php
session_start(); 
$pageCSS = '../../css/addPromo-seller.css';
include __DIR__ . '/../../includes/header-seller.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';

$idPenjualLogin = isset($_SESSION['idPenjual']) ? $_SESSION['idPenjual'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $namaPromo         = $_POST['namaPromo'] ?? '';
    $tipePromo         = $_POST['tipePromo'] ?? '';
    $minimalTransaksi  = $_POST['minimalTransaksi'] ?? null;
    $jenisPembayaran   = $_POST['jenisPembayaran'] ?? '';
    $startDate         = $_POST['startDate'] ?? null;
    $endDate           = $_POST['endDate'] ?? null;
    $persentasePotongan = ($_POST['persentasePotongan'] ?? '') !== '' ? $_POST['persentasePotongan'] : null;
    $nominalPotongan    = ($_POST['nominalPotongan'] ?? '') !== '' ? $_POST['nominalPotongan'] : null;

    if ($tipePromo === 'diskon') {
        $nominalPotongan = null; 
    } else if ($tipePromo === 'cashback' || $tipePromo === 'ongkir') {
        $persentasePotongan = null; 
    }

    $query = "INSERT INTO tbpromo (namaPromo, tipePromo, minimalTransaksi, jenisPembayaran, persentasePotongan, nominalPotongan, startDate, endDate, idPenjual, idAdmin, statusAktif) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, '', 'Y')";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "ssssidsss", 
        $namaPromo,
        $tipePromo,
        $minimalTransaksi,
        $jenisPembayaran,
        $persentasePotongan,
        $nominalPotongan,
        $startDate,
        $endDate,
        $idPenjualLogin
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Promo berhasil ditambahkan ke My Voucher!',
                    icon: 'success',
                    confirmButtonColor: '#61593d',
                    background: '#faf7f5'
                }).then(() => {
                    window.location.href='my-voucher.php';
                });
            });
        </script>";
    } else {
        $errorMsg = mysqli_error($conn);
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Gagal menambahkan promo: " . $errorMsg . "',
                    icon: 'error',
                    confirmButtonColor: '#ba704a',
                    background: '#faf7f5'
                });
            });
        </script>";
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="voucher-page">
    <div class="voucher-tab-container">
        <button class="btn-tab" onclick="window.location.href='promo-seller.php'"> ShopEase Voucher</button>
        <button class="btn-tab" onclick="window.location.href='my-voucher.php'">My Voucher</button>
        <button class="btn-tab active">Add new voucher</button>
    </div>

    <div class="voucher-card">
        <form method="post">
            <div class="form-group">
                <label>Promo Name</label>
                <input type="text" name="namaPromo" required placeholder="Contoh: Voucher Cashback">
            </div>

            <div class="form-group">
                <label>Promo Type</label>
                <select name="tipePromo" id="tipePromo" required>
                    <option value="">-- Select Type --</option>
                    <option value="diskon">Diskon</option>
                    <option value="cashback">Cashback</option>
                    <option value="ongkir">Gratis Ongkir</option>
                </select>
            </div>

            <div class="form-group">
                <label>Minimum Purchase (Rp)</label>
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
                    <input type="number" name="persentasePotongan" id="inputPersen" step="0.01">
                </div>

                <div class="form-group">
                    <label>Discount amount(Rp)</label>
                    <input type="number" name="nominalPotongan" id="inputNominal">
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

            <button type="submit" class="btn-add">Add Promo</button>
        </form>
    </div>
</div>

<script>
document.getElementById('tipePromo').addEventListener('change', function() {
    const inputPersen = document.getElementById('inputPersen');
    const inputNominal = document.getElementById('inputNominal');
    const tipe = this.value;

    inputPersen.readOnly = false;
    inputNominal.readOnly = false;
    inputPersen.style.opacity = "1";
    inputNominal.style.opacity = "1";
    inputPersen.style.backgroundColor = "";
    inputNominal.style.backgroundColor = "";

    if (tipe === 'diskon') {
        inputNominal.readOnly = true;
        inputNominal.value = "";
        inputNominal.style.opacity = "0.5"; 
        inputNominal.style.backgroundColor = "#e3d6cc"; 
    } else if (tipe === 'cashback' || tipe === 'ongkir') {
        inputPersen.readOnly = true;
        inputPersen.value = "";
        inputPersen.style.opacity = "0.5"; 
        inputPersen.style.backgroundColor = "#e3d6cc"; 
    }
});
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>