<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../includes/dbOnlinePOS.php';

$pageCSS = '../../css/editProduct-seller.css';

$idPenjual = $_SESSION['idPenjual'] ?? 0;
if ($idPenjual <= 0) {
    die("Penjual tidak ditemukan");
}

$idProduk = $_GET['id'] ?? 0;
if ($idProduk <= 0) {
    die("Produk tidak ditemukan");
}

$sql = "SELECT * FROM tbproduk WHERE idProduk = ? AND idPenjual = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $idProduk, $idPenjual);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Produk tidak ditemukan atau Anda tidak memiliki akses.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $namaProduk = trim($_POST['namaProduk']);
    $kategoriProduk = trim($_POST['kategoriProduk']);
    $stok = (int) $_POST['stok'];
    $harga = (int) $_POST['harga_raw']; // <-- FIX: ambil angka asli
    $keterangan = trim($_POST['keterangan']);

    $updateSql = "
        UPDATE tbproduk 
        SET namaProduk = ?, 
            kategoriProduk = ?, 
            stok = ?, 
            harga = ?, 
            keterangan = ?
        WHERE idProduk = ? AND idPenjual = ?
    ";

    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param(
        "ssiisss",
        $namaProduk,
        $kategoriProduk,
        $stok,
        $harga,
        $keterangan,
        $idProduk,
        $idPenjual
    );

    if ($updateStmt->execute()) {
        header("Location: index-seller.php?msg=updated");
        exit;
    } else {
        $error = "Update gagal: " . $updateStmt->error;
    }
}

include __DIR__ . '/../../includes/header-seller.php';
?>

<main class="seller-edit-container">

<form method="POST">

<?php if (isset($error)): ?>
<div style="color:red; margin-bottom:15px;">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<div class="left">
    <div class="card-seller">
        <h2>General Information</h2>

        <label>Product name</label>
        <input type="text" 
               name="namaProduk" 
               value="<?= htmlspecialchars($product['namaProduk']) ?>" 
               required>

        <label>Product Description</label>
        <textarea name="keterangan" rows="8" required><?= 
            htmlspecialchars($product['keterangan']) 
        ?></textarea>
    </div>

    <div class="card-seller baris">
        <div>
            <label>Category</label>
            <input type="text" 
                   name="kategoriProduk" 
                   value="<?= htmlspecialchars($product['kategoriProduk']) ?>" 
                   required>
        </div>

        <div>
            <label>Stock</label>
            <input type="number" 
                   name="stok" 
                   value="<?= (int)$product['stok'] ?>" 
                   required>
        </div>
    </div>
</div>

<div class="right">

<div class="card-seller baris">
    <h2>Upload Image</h2>

    <div class="image-box"></div>

    <div class="thumbs">
        <div class="thumb"></div>
        <div class="thumb"></div>
        <div class="thumb add">+</div>
    </div>
</div>

<div class="card-seller baris">
    <label>Set price</label>

    <!-- TAMPILAN UNTUK USER -->
    <input type="text" 
           id="harga_display"
           value="Rp. <?= number_format($product['harga'], 0, ',', '.') ?>"
           required>

    <!-- NILAI ASLI UNTUK DATABASE -->
    <input type="hidden" 
           name="harga_raw" 
           id="harga_raw"
           value="<?= (int)$product['harga'] ?>">
</div>

<button type="submit" class="btn">Save Changes</button>

</div>

</form>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

