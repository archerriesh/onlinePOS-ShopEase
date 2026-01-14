<?php
$pageCSS = '../../css/admin/produk-per-toko.css';
include __DIR__ . '/../../includes/header-admin.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';

// 1. Ambil ID Penjual dari URL
$idPenjual = $_GET['idPenjual'] ?? '';

if ($idPenjual === '') {
    echo "<div class='container'><h1>Toko tidak ditemukan.</h1></div>";
    include __DIR__ . '/../../includes/footer.php';
    exit;
}

// 2. Ambil Informasi Toko
$sqlToko = "SELECT namaPenjual FROM tbpenjual WHERE idPenjual = ?";
$stmtToko = mysqli_prepare($conn, $sqlToko);
mysqli_stmt_bind_param($stmtToko, "s", $idPenjual);
mysqli_stmt_execute($stmtToko);
$resToko = mysqli_stmt_get_result($stmtToko);
$toko = mysqli_fetch_assoc($resToko);

// 3. Ambil Semua Produk dari Toko Tersebut
$sqlProduk = "SELECT idProduk, namaProduk, harga FROM tbproduk WHERE idPenjual = ?";
$stmtProduk = mysqli_prepare($conn, $sqlProduk);
mysqli_stmt_bind_param($stmtProduk, "s", $idPenjual);
mysqli_stmt_execute($stmtProduk);
$resultProduk = mysqli_stmt_get_result($stmtProduk);

/* =========================================================
   LOGIKA GAMBAR (Disimpan di array agar rapi di bawah)
   ========================================================= */
$daftarProduk = [];
$basePath = "../../foto/produk/"; // Mundur 2x dari pages/admin/
$extensions = ['webp', 'jpg', 'jpeg', 'png'];

while ($row = mysqli_fetch_assoc($resultProduk)) {
    $gambarProduk = "../../assets/img/default.jpg"; // Path default

    foreach ($extensions as $ext) {
        $file = $basePath . $row['idProduk'] . "." . $ext;
        if (file_exists($file)) {
            $gambarProduk = $file;
            break;
        }
    }
    
    // Simpan hasil pencarian gambar ke dalam array data produk
    $row['gambar'] = $gambarProduk;
    $daftarProduk[] = $row;
}
?>

<div class="seller-products">

    <div class="seller-header">
        <span class="seller-tag">TOKO</span>
        <h1><?= htmlspecialchars($toko['namaPenjual'] ?? 'Nama Toko Tidak Diketahui'); ?></h1>
        <p>Kategori: Makanan & Minuman</p>
    </div>

    <div class="product-grid">

        <?php if (count($daftarProduk) > 0): ?>
            <?php foreach ($daftarProduk as $produk): ?>
                
                <a href="liat-produk.php?id=<?= $produk['idProduk']; ?>" class="product-card">
                    <div class="product-image">
                        <img src="<?= $produk['gambar']; ?>" alt="<?= htmlspecialchars($produk['namaProduk']); ?>" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div class="product-info">
                        <h3><?= htmlspecialchars($produk['namaProduk']); ?></h3>
                        <span class="price">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></span>
                    </div>
                </a>

            <?php endforeach; ?>
        <?php else: ?>
            <p>Toko ini belum memiliki produk.</p>
        <?php endif; ?>

    </div>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>