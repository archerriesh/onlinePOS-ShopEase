<?php
$pageCSS = '../../css/admin/kelola-buyer.css';
include __DIR__ . '/../../includes/header-admin.php';
require __DIR__ . '/../../includes/dbOnlinePOS.php';

$sql = "SELECT idPelanggan, namaPelanggan, kategoriAkun, statusAktif FROM tbpelanggan";
$result = mysqli_query($conn, $sql);
?>

<div class="toko-container">
    <div class="toko-header">
        <h1 class="section-title">Daftar Buyer</h1>
    </div>

    <div class="buyer-grid">

        <?php while ($row = mysqli_fetch_assoc($result)) : 
            $id = $row['idPelanggan'];
            $nama = $row['namaPelanggan'];
            $kategori = $row['kategoriAkun']; 
            $status = $row['statusAktif']; 
            $icon = "";
            if (strtolower($kategori) == 'gold') $icon = "✨";
            elseif (strtolower($kategori) == 'silver') $icon = "🛡️";
            else $icon = "🥉";
        ?>

        <div class="buyer-card">
            <div class="menu-container">
                <button class="menu-dots" onclick="toggleDropdown(event, 'b-<?= $id ?>')">⋮</button>
                <div id="b-<?= $id ?>" class="dropdown-content">
                    <a href="#">Nonaktifkan</a>
                </div>
            </div>
            <div class="buyer-avatar">
                <img src="../../foto/default-user.jpg" alt="Buyer">
            </div>
            <p class="buyer-name"><?= htmlspecialchars($nama) ?></p>
            <div class="buyer-badge badge-<?= strtolower($kategori) ?>">
                <?= $icon ?> <?= $kategori ?> Member
            </div>
        </div>

        <?php endwhile; ?>

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