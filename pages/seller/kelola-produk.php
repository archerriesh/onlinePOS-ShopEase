<?php

$pageCSS = '../../css/kelola-produk.css';

include __DIR__ . '/../../includes/header-seller.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';

$idPenjual = $_SESSION['idPenjual'] ?? '';

if ($idPenjual === '') {
    echo "Penjual tidak ditemukan";
    exit;
}

$sql = "
    SELECT 
        pj.namaPenjual,
        pr.idProduk,
        pr.namaProduk,
        pr.harga
    FROM tbpenjual pj
    JOIN tbproduk pr ON pj.idPenjual = pr.idPenjual
    WHERE pj.idPenjual = ?
    ORDER BY pr.namaProduk ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $idPenjual);
$stmt->execute();
$result = $stmt->get_result();

$namaToko = '';
$produk = [];

while ($row = $result->fetch_assoc()) {
    $namaToko = $row['namaPenjual']; // sama untuk semua produk
    $produk[] = $row;
}

?>

<div class="toko-container">

    <div class="toko-header">
        <h1 class="section-title">
            <?= htmlspecialchars($namaToko ?: 'Toko Saya') ?>
        </h1>
        <button class="add-product"></button>
    </div>

    <div class="product-grid">

        <?php if (count($produk) > 0): ?>
            <?php foreach ($produk as $item): ?>
                <div class="product-card">

                    <div class="product-image"></div>

                    <div class="product-info">
                        <p class="product-name">
                            <?= htmlspecialchars($item['namaProduk']) ?>
                        </p>
                        <p class="product-price">
                            Rp <?= number_format($item['harga'], 0, ',', '.') ?>
                        </p>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-text">Belum ada produk</p>
        <?php endif; ?>

    </div>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
