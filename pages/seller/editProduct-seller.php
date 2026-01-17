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

// Cari foto produk
$files = glob("../../foto/produk/" . $product['idProduk'] . ".*");
$imgPath = $files[0] ?? "../../assets/img/default.jpg";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $namaProduk = trim($_POST['namaProduk']);
    $kategoriProduk = trim($_POST['kategoriProduk']);
    $stok = (int) $_POST['stok'];
    $harga = (int) $_POST['harga_raw']; 
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
        // Handle upload gambar (opsional)
        if (!empty($_FILES['gambar']['name'])) {
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ext, $allowed)) {
                $folder = "../../foto/produk/";
                if (!is_dir($folder)) {
                    mkdir($folder, 0777, true);
                }

                // Hapus file gambar lama jika ada
                $oldFiles = glob($folder . $product['idProduk'] . ".*");
                foreach ($oldFiles as $oldFile) {
                    @unlink($oldFile);
                }

                // Upload gambar baru
                move_uploaded_file(
                    $_FILES['gambar']['tmp_name'],
                    $folder . $product['idProduk'] . "." . $ext
                );
            }
        }

        header("Location: index-seller.php?msg=updated");
        exit;
    } else {
        $error = "Update gagal: " . $updateStmt->error;
    }
}

include __DIR__ . '/../../includes/header-seller.php';
?>

<main class="seller-edit-container">

<form method="POST" enctype="multipart/form-data">

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
    <h2>Change Image</h2>
    <input type="file" id="gambarInput" name="gambar" accept="image/*">
    <div class="image-box">
        <div class="main-image" id="mainImagePreview"
             style="background-image: url('<?= htmlspecialchars($imgPath) ?>');">
        </div>  
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
<a href="index-seller.php" class="btn btn-cancel">Cancel</a>

</div>

</form>
</main>

<script>
const gambarInput = document.getElementById('gambarInput');
const mainImagePreview = document.getElementById('mainImagePreview');

gambarInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(event) {
            mainImagePreview.style.backgroundImage = 'url(' + event.target.result + ')';
        };
        
        reader.readAsDataURL(file);
    }
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

