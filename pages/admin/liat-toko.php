<?php
$pageCSS = '../../css/admin/liat-toko.css';

include __DIR__ . '/../../includes/header-admin.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';

$sql = "SELECT idPenjual, namaPenjual, statusAktif,
        (SELECT AVG(fn_rating_produk(idProduk)) 
         FROM tbproduk 
         WHERE tbproduk.idPenjual = tbpenjual.idPenjual) as ratingToko,
        (SELECT SUM(fn_Total_terjual(idProduk)) 
         FROM tbproduk 
         WHERE tbproduk.idPenjual = tbpenjual.idPenjual) as totalTerjual
        FROM tbpenjual";
$result = mysqli_query($conn, $sql);
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="toko-container">

    <div class="toko-header">
        <h1 class="section-title">Daftar Seller</h1>
    </div>

    <div class="product-grid">

        <?php while ($row = mysqli_fetch_assoc($result)) { 
            $idPenjual = $row['idPenjual'];
            $status = $row['statusAktif']; 
            $pathFotoToko = "../../foto/default-seller.jpg";
            $isNonaktif = ($status === 'N');
            $wrapperClass = $isNonaktif ? 'product-card-wrapper is-nonaktif' : 'product-card-wrapper';
        ?>
            <div class="<?= $wrapperClass ?>">
                
                <div class="menu-container">
                    <button class="menu-dots" onclick="toggleDropdown(event, 'menu-<?= $idPenjual ?>')">⋮</button>
                    <div id="menu-<?= $idPenjual ?>" class="dropdown-content">
                        <?php if ($isNonaktif): ?>
                            <a href="javascript:void(0)" class="btn-aktifkan" onclick="confirmAction('<?= $idPenjual ?>', 'aktifkan')">
                                Aktifkan Kembali
                            </a>
                        <?php else: ?>
                            <a href="javascript:void(0)" class="btn-nonaktif" onclick="confirmAction('<?= $idPenjual ?>', 'nonaktifkan')">
                                Nonaktifkan
                            </a>
                        <?php endif; ?>
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
function confirmAction(id, type) {
    const isAktifkan = (type === 'aktifkan');
    
    Swal.fire({
        title: isAktifkan ? 'Aktifkan Kembali?' : 'Nonaktifkan Toko?',
        text: isAktifkan ? "Toko akan terlihat kembali oleh pelanggan." : "Toko akan disembunyikan dan menjadi tidak aktif.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isAktifkan ? '#28a745' : '#d33', 
        cancelButtonColor: '#6e7881',
        confirmButtonText: isAktifkan ? 'Ya, Aktifkan!' : 'Ya, Nonaktifkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = (isAktifkan ? 'proses-aktifkan.php?id=' : 'proses-nonaktifkan.php?id=') + id;
        }
    });
}

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