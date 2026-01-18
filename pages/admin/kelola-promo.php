<?php
$pageCSS = '../../css/admin/kelola-promo.css';
include __DIR__ . '/../../includes/header-admin.php';
require __DIR__ . '/../../includes/dbOnlinePOS.php';

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $idHapus = $_GET['id'];
    $queryDel = "CALL sp_delete_promo(?)";
    $stmtDel = mysqli_prepare($conn, $queryDel);
    mysqli_stmt_bind_param($stmtDel, "s", $idHapus);
    
    if (mysqli_stmt_execute($stmtDel)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Promo berhasil dihapus!',
                    icon: 'success',
                    confirmButtonColor: '#61593d',
                    background: '#faf7f5'
                }).then(() => {
                    window.location.href='liat-promo.php';
                });
            });
        </script>";
    }
}

if (isset($_POST['update'])) {
    $idPromo = $_POST['idPromo'];
    $namaPromo = $_POST['namaPromo'];
    $tipePromo = $_POST['tipePromo']; 
    $minimal = $_POST['minimalTransaksi'];
    $pembayaran = $_POST['jenisPembayaran'];
    $start = $_POST['startDate'];
    $end = $_POST['endDate'];

    $persen = !empty($_POST['persentasePotongan']) ? $_POST['persentasePotongan'] : 0;
    $nominal = !empty($_POST['nominalPotongan']) ? $_POST['nominalPotongan'] : 0;

    $queryUpd = "CALL sp_update_promo(?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtUpd = mysqli_prepare($conn, $queryUpd);

    mysqli_stmt_bind_param(
        $stmtUpd,
        "sssssssss", 
        $idPromo,
        $namaPromo,
        $tipePromo,
        $minimal,
        $pembayaran,
        $persen,
        $nominal,
        $start,
        $end
    );

    if (mysqli_stmt_execute($stmtUpd)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Perubahan berhasil disimpan!',
                    icon: 'success',
                    confirmButtonColor: '#61593d',
                    background: '#faf7f5'
                }).then(() => {
                    window.location.href='liat-promo.php';
                });
            });
        </script>";
    } else {
        die("Gagal update: " . mysqli_error($conn));
    }
}

if (!isset($_GET['id'])) {
    header("Location: liat-promo.php");
    exit;
}

$idPromo = $_GET['id'];
$query = "SELECT * FROM tbpromo WHERE idPromo = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $idPromo);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: liat-promo.php");
    exit;
}

$isSellerPromo = !empty($data['idPenjual']);

if ($isSellerPromo) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Akses Ditolak!',
                text: 'Admin tidak diperbolehkan mengelola promo milik Seller.',
                icon: 'error',
                confirmButtonColor: '#ba704a',
                background: '#faf7f5',
                allowOutsideClick: false
            }).then(() => {
                window.location.href='liat-promo.php';
            });
        });
    </script>";
}

$startDateFormatted = date('Y-m-d', strtotime($data['startDate']));
$endDateFormatted   = date('Y-m-d', strtotime($data['endDate']));
$badgeIcon = ($data['persentasePotongan'] > 0) ? '%' : 'Rp';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="kelola-promo-page">
    <div class="kelola-header">
        <h1>Manage Voucher</h1>
        <span class="subtitle">Edit, update, or delete your promo</span>
    </div>

    <div class="promo-card">
        <div class="promo-badge"><?= $badgeIcon ?></div>

        <form class="promo-form" method="POST" action="">
            <input type="hidden" name="idPromo" value="<?= $data['idPromo'] ?>">

            <div class="form-group">
                <label>Promo Name</label>
                <input type="text" name="namaPromo" value="<?= htmlspecialchars($data['namaPromo']) ?>" required>
            </div>

            <div class="form-group">
                <label>Promo Type</label>
                <select name="tipePromo" id="tipePromo" required>
                    <option value="">-- Select Type --</option>
                    <option value="diskon" <?= ($data['tipePromo'] == 'diskon') ? 'selected' : '' ?>>Diskon</option>
                    <option value="cashback" <?= ($data['tipePromo'] == 'cashback') ? 'selected' : '' ?>>Cashback</option>
                    <option value="ongkir" <?= ($data['tipePromo'] == 'ongkir') ? 'selected' : '' ?>>Gratis Ongkir</option>
                </select>
            </div>

            <div class="form-group">
                <label>Minimum Purchase (Rp)</label>
                <input type="number" name="minimalTransaksi" value="<?= $data['minimalTransaksi'] ?>" required>
            </div>

            <div class="form-group">
                <label>Payment Method</label>
                <select name="jenisPembayaran" required>
                    <option value="qris" <?= ($data['jenisPembayaran'] == 'qris') ? 'selected' : '' ?>>QRIS</option>
                    <option value="cod" <?= ($data['jenisPembayaran'] == 'cod') ? 'selected' : '' ?>>COD</option>
                    <option value="transferbank" <?= ($data['jenisPembayaran'] == 'transferbank') ? 'selected' : '' ?>>Transfer Bank</option>
                    <option value="emoney" <?= ($data['jenisPembayaran'] == 'emoney') ? 'selected' : '' ?>>E-Money</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Discount (%)</label>
                    <input type="number" name="persentasePotongan" id="persentasePotongan" value="<?= $data['persentasePotongan'] ?>">
                </div>
                <div class="form-group">
                    <label>Discount Amount (Rp)</label>
                    <input type="number" name="nominalPotongan" id="nominalPotongan" value="<?= $data['nominalPotongan'] ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="startDate" value="<?= $startDateFormatted ?>" required>
                </div>
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" name="endDate" value="<?= $endDateFormatted ?>" required>
                </div>
            </div>

            <div class="promo-actions">
                <?php if (!$isSellerPromo): ?>
                    <a href="#" class="btn-delete" style="text-decoration: none;" onclick="confirmDelete(event, 'kelola-promo.php?id=<?= $data['idPromo'] ?>&action=delete')">Delete</a>
                    <button type="submit" name="update" class="btn-save">Save Changes</button>
                <?php else: ?>
                    <button type="button" class="btn-save" style="background-color: #ccc; cursor: not-allowed;" disabled>Disabled for Seller Promo</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="back-wrapper">
        <a href="liat-promo.php" class="btn-back">← Back to Voucher List</a>
    </div>
</div>

<script>
const tipePromo = document.getElementById('tipePromo');
const inputPersen = document.getElementById('persentasePotongan');
const inputNominal = document.getElementById('nominalPotongan');

function toggleInputs() {
    if (tipePromo.value === 'diskon') {
        inputNominal.readOnly = true;
        inputPersen.readOnly = false;
        inputNominal.style.backgroundColor = '#eee';
        inputPersen.style.backgroundColor = '#fff';
    } else if (tipePromo.value === 'cashback' || tipePromo.value === 'ongkir') {
        inputPersen.readOnly = true;
        inputNominal.readOnly = false;
        inputPersen.style.backgroundColor = '#eee';
        inputNominal.style.backgroundColor = '#fff';
    }
}

tipePromo.addEventListener('change', toggleInputs);
window.addEventListener('load', toggleInputs);

function confirmDelete(event, url) {
    event.preventDefault();
    Swal.fire({
        title: 'Hapus Promo?',
        text: "Data tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ba704a', 
        cancelButtonColor: '#a6996b',  
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        background: '#faf7f5'          
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>