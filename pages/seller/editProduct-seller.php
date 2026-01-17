<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../includes/dbOnlinePOS.php';

$pageCSS = '../../css/editProduct-seller.css';

$idPenjual = $_SESSION['idPenjual'] ?? '';
$idProduk = $_GET['id'] ?? '';

if ($idPenjual == '' || $idProduk == '') {
    die("Akses ditolak. Produk atau Penjual tidak valid.");
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

$files = glob("../../foto/produk/" . $product['idProduk'] . ".*");
$imgPath = $files[0] ?? "../../assets/img/default.jpg";

$updateSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaProduk      = trim($_POST['namaProduk']);
    $kategoriProduk  = trim($_POST['kategoriProduk']);
    $stokBaru        = (int) $_POST['stok'];
    $stokLama        = (int) $product['stok']; 
    $harga           = (int) $_POST['harga_raw']; 
    $keterangan      = trim($_POST['keterangan']);

    $spSql = "CALL sp_update_produk(?, ?, ?, ?, ?)";
    $updateStmt = $conn->prepare($spSql);
    $updateStmt->bind_param("sssis", $idProduk, $namaProduk, $kategoriProduk, $harga, $keterangan);

    if ($updateStmt->execute()) {
        $updateStmt->close(); 

        $selisihStok = $stokBaru - $stokLama;
        
        $spStokSql = "CALL sp_update_stok(?, ?)";
        $stmtStok = $conn->prepare($spStokSql);
        $stmtStok->bind_param("si", $idProduk, $selisihStok);
        
        try {
            if ($stmtStok->execute()) {
                // 3. Proses Update Gambar
                if (!empty($_FILES['gambar']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                    if (in_array($ext, $allowed)) {
                        $folder = "../../foto/produk/";
                        if (!is_dir($folder)) mkdir($folder, 0777, true);
                        $oldFiles = glob($folder . $idProduk . ".*");
                        foreach ($oldFiles as $oldFile) @unlink($oldFile);
                        move_uploaded_file($_FILES['gambar']['tmp_name'], $folder . $idProduk . "." . $ext);
                    }
                }
                $updateSuccess = true;
            }
        } catch (mysqli_sql_exception $e) {
            $error = "Gagal: " . $e->getMessage();
        }
    } else {
        $error = "Gagal memperbarui data: " . $updateStmt->error;
    }
}

include __DIR__ . '/../../includes/header-seller.php';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<main class="seller-edit-container">
    <form method="POST" enctype="multipart/form-data">
        <?php if (isset($error)): ?>
            <div style="background: #fee; color:red; padding:10px; border:1px solid red; margin-bottom:15px; border-radius:5px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="left">
            <div class="card-seller">
                <h2>General Information</h2>
                <label>Product Name</label>
                <input type="text" name="namaProduk" value="<?= htmlspecialchars($product['namaProduk']) ?>" required>
                <label>Product Description</label>
                <textarea name="keterangan" rows="8" required><?= htmlspecialchars($product['keterangan']) ?></textarea>
            </div>
            <div class="card-seller baris">
                <div>
                    <label>Category</label>
                    <input type="text" name="kategoriProduk" value="<?= htmlspecialchars($product['kategoriProduk']) ?>" required>
                </div>
                <div>
                    <label>Stock</label>
                    <input type="number" name="stok" value="<?= (int)$product['stok'] ?>" required>
                </div>
            </div>
        </div>

        <div class="right">
            <div class="card-seller baris">
                <h2>Change Image</h2>
                <input type="file" id="gambarInput" name="gambar" accept="image/*">
                <div class="image-box">
                    <div class="main-image" id="mainImagePreview" style="background-image: url('<?= htmlspecialchars($imgPath) ?>');"></div>  
                </div>
            </div>
            <div class="card-seller baris">
                <label>Set Price</label>
                <input type="text" id="harga_display" value="Rp. <?= number_format($product['harga'], 0, ',', '.') ?>" required>
                <input type="hidden" name="harga_raw" id="harga_raw" value="<?= (int)$product['harga'] ?>">
            </div>
            <button type="submit" class="btn">Save Changes</button>
            <a href="index-seller.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</main>

<script>
document.getElementById('gambarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (event) => document.getElementById('mainImagePreview').style.backgroundImage = `url(${event.target.result})`;
        reader.readAsDataURL(file);
    }
});

const hargaDisplay = document.getElementById('harga_display');
const hargaRaw = document.getElementById('harga_raw');
hargaDisplay.addEventListener('input', function(e) {
    let value = this.value.replace(/[^0-9]/g, '');
    hargaRaw.value = value;
    this.value = value ? 'Rp. ' + new Intl.NumberFormat('id-ID').format(value) : '';
});

<?php if ($updateSuccess): ?>
    Swal.fire({
        title: "Berhasil!",
        text: "Data produk telah diperbarui.",
        icon: "success",
        timer: 2000,
        showConfirmButton: false
    }).then(() => {
        window.location.href = "index-seller.php";
    });
<?php endif; ?>
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>