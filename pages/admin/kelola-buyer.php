<?php
$pageCSS = '../../css/admin/kelola-buyer.css';
include __DIR__ . '/../../includes/header-admin.php';
require __DIR__ . '/../../includes/dbOnlinePOS.php';

$sql = "SELECT idPelanggan, namaPelanggan, kategoriAkun, statusAktif, fotoPelanggan AS foto_profil FROM tbpelanggan";
$result = mysqli_query($conn, $sql);
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            $namaFile = $row['foto_profil']; 
            $fotoDefault = "../../foto/default.png"; 

            if (empty($namaFile) || !file_exists("../../foto/" . $namaFile)) {
                $pathFoto = $fotoDefault;
            } else {
                $pathFoto = "../../foto/" . $namaFile;
            }
            
            $isNonaktif = ($status === 'N');
            
            $icon = "";
            if (strtolower($kategori) == 'gold') $icon = "✨";
            elseif (strtolower($kategori) == 'silver') $icon = "🛡️";
            else $icon = "🥉";
        ?>

        <div class="buyer-card <?= $isNonaktif ? 'is-nonaktif' : '' ?>">
            <div class="menu-container">
                <button class="menu-dots" onclick="toggleDropdown(event, 'b-<?= $id ?>')">⋮</button>
                <div id="b-<?= $id ?>" class="dropdown-content">
                    <?php if ($isNonaktif) : ?>
                        <a href="javascript:void(0)" class="btn-aktifkan" onclick="confirmAction('<?= $id ?>', 'aktifkan')">
                            Aktifkan Kembali
                        </a>
                    <?php else : ?>
                        <a href="javascript:void(0)" class="btn-nonaktif" onclick="confirmAction('<?= $id ?>', 'nonaktifkan')">
                            Nonaktifkan
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="buyer-avatar">
                <img src="<?= $pathFoto; ?>" alt="Profile">
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
function confirmAction(id, type) {
    const isAktifkan = (type === 'aktifkan');
    
    Swal.fire({
        title: isAktifkan ? 'Aktifkan Kembali?' : 'Nonaktifkan Pengguna?',
        text: isAktifkan ? "Akun pengguna akan dapat digunakan kembali." : "Pengguna ini tidak akan bisa melakukan transaksi.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isAktifkan ? '#28a745' : '#d33',
        cancelButtonColor: '#6e7881',
        confirmButtonText: isAktifkan ? 'Ya, Aktifkan!' : 'Ya, Nonaktifkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = (isAktifkan ? 'proses-aktifkan-buyer.php?id=' : 'proses-nonaktifkan-buyer.php?id=') + id;
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