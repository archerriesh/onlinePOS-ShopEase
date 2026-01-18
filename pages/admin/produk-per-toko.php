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

$sqlProduk = "SELECT idProduk, namaProduk, harga, statusAktif FROM tbproduk WHERE idPenjual = ?";
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="seller-products">
    <div class="seller-header">
        <span class="seller-tag">TOKO</span>
        <h1><?= htmlspecialchars($toko['namaPenjual'] ?? 'Nama Toko Tidak Ditemukan'); ?></h1>
    </div>

    <main class="content">
        <div class="product-grid">
            <?php if (count($daftarProduk) > 0): ?>
                <?php foreach ($daftarProduk as $produk): ?>
                    
                    <div class="product-card <?= (trim($produk['statusAktif']) === 'N') ? 'is-nonaktif' : '' ?>">
                        
                        <div class="options-menu">
                            <button class="dots-btn" onclick="toggleMenu(event, 'menu-<?= $produk['idProduk'] ?>')">⋮</button>
                            <div class="menu-dropdown" id="menu-<?= $produk['idProduk'] ?>">
                                <?php if ($produk['statusAktif'] === 'Y'): ?>
                                    <button class="menu-item btn-nonaktif" onclick="prosesStatus('<?= $produk['idProduk'] ?>', 'nonaktif')">Nonaktifkan</button>
                                <?php else: ?>
                                    <button class="menu-item btn-aktifkan" onclick="prosesStatus('<?= $produk['idProduk'] ?>', 'aktifkan')">Aktifkan Kembali</button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <a href="liat-produk.php?id=<?= $produk['idProduk']; ?>">
                            <img src="<?= $produk['gambarPath']; ?>" alt="<?= htmlspecialchars($produk['namaProduk']); ?>">
                            <h3><?= htmlspecialchars($produk['namaProduk']); ?></h3>
                            <p>Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></p>
                        </a>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty">This shop has no products yet.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
function toggleMenu(e, menuId) {
    e.preventDefault();
    e.stopPropagation();
    document.querySelectorAll('.menu-dropdown').forEach(m => {
        if(m.id !== menuId) m.classList.remove('show');
    });
    document.getElementById(menuId).classList.toggle('show');
}

document.addEventListener('click', () => {
    document.querySelectorAll('.menu-dropdown').forEach(m => m.classList.remove('show'));
});

function prosesStatus(id, tipe) {
    const isAktifkan = (tipe === 'aktifkan');
    
    Swal.fire({
        title: isAktifkan ? 'Aktifkan Produk?' : 'Nonaktifkan Produk?',
        text: isAktifkan ? "Produk akan muncul kembali di toko." : "Produk tidak akan terlihat oleh pelanggan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isAktifkan ? '#28a745' : '#d33',
        cancelButtonColor: '#ba704a',
        confirmButtonText: isAktifkan ? 'Ya, Aktifkan!' : 'Ya, Nonaktifkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('update_produk_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&action=${tipe}`
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#ba704a'
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', 'Gagal update: ' + data.message, 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Koneksi bermasalah', 'error'));
        }
    });
}
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>