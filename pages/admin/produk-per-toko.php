<?php
$pageCSS = '../../css/admin/produk-per-toko.css';
include __DIR__ . '/../../includes/header-admin.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';

$idPenjual = $_GET['idPenjual'] ?? '';

if ($idPenjual === '') {
    echo "<div class='container'><h1>Toko tidak ditemukan.</h1></div>";
    include __DIR__ . '/../../includes/footer.php';
    exit;
}

$sqlToko = "SELECT namaPenjual FROM tbpenjual WHERE idPenjual = ?";
$stmtToko = mysqli_prepare($conn, $sqlToko);
mysqli_stmt_bind_param($stmtToko, "s", $idPenjual);
mysqli_stmt_execute($stmtToko);
$resToko = mysqli_stmt_get_result($stmtToko);
$toko = mysqli_fetch_assoc($resToko);

$sqlProduk = "SELECT idProduk, namaProduk, harga FROM tbproduk WHERE idPenjual = ?";
$stmtProduk = mysqli_prepare($conn, $sqlProduk);
mysqli_stmt_bind_param($stmtProduk, "s", $idPenjual);
mysqli_stmt_execute($stmtProduk);
$resultProduk = mysqli_stmt_get_result($stmtProduk);

$daftarProduk = [];
$basePath = "../../foto/produk/"; 
$extensions = ['webp', 'jpg', 'jpeg', 'png'];

while ($row = mysqli_fetch_assoc($resultProduk)) {
    $gambarFinal = "../../assets/img/default.jpg"; 

    foreach ($extensions as $ext) {
        $fileCek = $basePath . $row['idProduk'] . "." . $ext;
        if (file_exists($fileCek)) {
            $gambarFinal = $fileCek;
            break; 
        }
    }
    
    $row['gambarPath'] = $gambarFinal;
    $daftarProduk[] = $row;
}
?>

<div class="seller-products">

    <div class="seller-header">
        <span class="seller-tag">TOKO</span>
        <h1><?= htmlspecialchars($toko['namaPenjual'] ?? 'Nama Toko Tidak Ditemukan'); ?></h1>
    </div>

    <main class="content">
        <div class="product-grid">

            <?php if (count($daftarProduk) > 0): ?>
                <?php foreach ($daftarProduk as $produk): ?>
                    
                    <div class="product-card">
                        <a href="liat-produk.php?id=<?= $produk['idProduk']; ?>">
                            <img src="<?= $produk['gambarPath']; ?>" alt="<?= htmlspecialchars($produk['namaProduk']); ?>">
                            <h3><?= htmlspecialchars($produk['namaProduk']); ?></h3>
                        </a>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty">Toko ini belum memiliki produk.</p>
            <?php endif; ?>

        </div>
    </main>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>