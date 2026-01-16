<?php
$pageCSS = '../../css/admin/liat-toko.css';

include __DIR__ . '/../../includes/header-admin.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';

$sql = "SELECT idPenjual, namaPenjual, 
        (SELECT AVG(fn_rating_produk(idProduk)) 
         FROM tbproduk 
         WHERE tbproduk.idPenjual = tbpenjual.idPenjual) as ratingToko,
        (SELECT SUM(fn_Total_terjual(idProduk)) 
         FROM tbproduk 
         WHERE tbproduk.idPenjual = tbpenjual.idPenjual) as totalTerjual
        FROM tbpenjual";
$result = mysqli_query($conn, $sql);
?>

<div class="toko-container">

    <div class="toko-header">
        <h1 class="section-title">Daftar Seller</h1>
    </div>

    <div class="product-grid">

        <?php while ($row = mysqli_fetch_assoc($result)) { 
            $idPenjual = $row['idPenjual'];
            $pathFotoToko = "../../foto/default-seller.jpg";
        ?>
            <div class="product-card-wrapper">
                
                <div class="menu-container">
                    <button class="menu-dots" onclick="toggleDropdown(event, 'menu-<?= $idPenjual ?>')">⋮</button>
                    <div id="menu-<?= $idPenjual ?>" class="dropdown-content">
                        <a href="proses-nonaktifkan.php?id=<?= $idPenjual ?>" class="btn-nonaktif" onclick="return confirm('Yakin ingin menonaktifkan toko ini?')">Nonaktifkan</a>
                    </div>
                </div>

                <a href="produk-per-toko.php?idPenjual=<?= $idPenjual; ?>" class="product-card">

                    <div class="product-image">
                        <img src="<?= $pathFotoToko; ?>" alt="Logo <?= htmlspecialchars($row['namaPenjual']); ?>" style="width:100%; height:100%; object-fit:cover;">
                    </div>

                    <div class="product-info">
                        <p class="product-name">
                            <?= htmlspecialchars($row['namaPenjual']); ?>
                        </p>
                        
                        <div class="product-stats">
                            <span class="stat-rating">⭐ <?= number_format($row['ratingToko'] ?? 0, 1); ?></span>
                            <span class="stat-divider">|</span>
                            <span class="stat-sold"><?= number_format($row['totalTerjual'] ?? 0, 0, ',', '.'); ?> Terjual</span>
                        </div>
                    </div>

                </a>
            </div>
        <?php } ?>

    </div>

</div>

<script>
function toggleDropdown(event, menuId) {
    event.preventDefault(); 
    event.stopPropagation(); 
    document.querySelectorAll('.dropdown-content').forEach(d => {
        if (d.id !== menuId) d.classList.remove('show');
    });

    document.getElementById(menuId).classList.toggle('show');
}

window.onclick = function(event) {
    if (!event.target.matches('.menu-dots')) {
        document.querySelectorAll('.dropdown-content').forEach(d => d.classList.remove('show'));
    }
}
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>