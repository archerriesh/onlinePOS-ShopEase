<?php
$pageCSS = '../../css/admin/liat-toko.css';

include __DIR__ . '/../../includes/header-admin.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';

$sql = "SELECT idPenjual, namaPenjual FROM tbpenjual";
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
            <a href="produk-per-toko.php?idPenjual=<?= $idPenjual; ?>" class="product-card">

                <div class="product-image">
                    <img src="<?= $pathFotoToko; ?>" alt="Logo <?= htmlspecialchars($row['namaPenjual']); ?>" style="width:100%; height:100%; object-fit:cover;">
                </div>

                <div class="product-info">
                    <p class="product-name">
                        <?= htmlspecialchars($row['namaPenjual']); ?>
                    </p>
                </div>

            </a>
        <?php } ?>

    </div>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>