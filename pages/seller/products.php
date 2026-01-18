<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../includes/dbOnlinePOS.php';

$idPenjual = $_SESSION['idPenjual'] ?? 0;

if ($idPenjual <= 0) {
    echo "Penjual tidak ditemukan";
    exit;
}

$sql = "
    SELECT pj.namaPenjual, pr.idProduk, pr.namaProduk, pr.harga, pr.statusAktif
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
$products = [];
while ($row = $result->fetch_assoc()) {
    $namaToko = $row['namaPenjual'];
    $products[] = $row;
}

$basePath   = "../../foto/produk/";
$extensions = ['webp', 'jpg', 'jpeg'];
$pageCSS = '../../css/seller-index.css';
include("../../includes/header-seller.php");
?>

<section class="container">
    <div class="store-header">
        <span>My products</span>
        <h1 class="section-title"><?= htmlspecialchars($namaToko ?: 'Toko Saya') ?></h1>
        <a href="addProduct-seller.php" class="add-btn">Add new product +</a>
    </div>

    <div class="product-grid">
        <?php if (empty($products)): ?>
            <p>There's no product yet.</p>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <?php
                    $isInactive = ($p['statusAktif'] === 'N');
                    $gambarProduk = "../../assets/img/default.jpg";
                    foreach ($extensions as $ext) {
                        $path = $basePath . $p['idProduk'] . '.' . $ext;
                        if (file_exists($path)) { $gambarProduk = $path; break; }
                    }
                    ?>
                    <div class="card <?= $isInactive ? 'is-inactive' : '' ?>">
                        <div class="card-actions">
                            <a href="editProduct-seller.php?id=<?= $p['idProduk'] ?>" class="edit-btn">✎</a>
                            
                            <div class="dropdown">
                                <button class="dropbtn">⋮</button>
                                <div class="dropdown-content">
                                    <?php if (!$isInactive): ?>
                                        <a href="#" class="text-danger" onclick="confirmAction('<?= $p['idProduk'] ?>', 'nonaktif')">Non-activate</a>
                                    <?php else: ?>
                                        <a href="#" class="text-success" onclick="confirmAction('<?= $p['idProduk'] ?>', 'aktif')">Activate</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <a href="liat-produk.php?id=<?= $p['idProduk'] ?>">
                            <?php if ($isInactive): ?>
                                <div class="status-badge">Non-active</div>
                            <?php endif; ?>
                                
                            <img src="<?= $gambarProduk ?>" alt="<?= htmlspecialchars($p['namaProduk']) ?>">
                            <h4><?= htmlspecialchars($p['namaProduk']) ?></h4>
                            <p>Rp <?= number_format($p['harga'], 0, ',', '.') ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('click', function(e) {
    const isDropdownButton = e.target.matches('.dropbtn');
    
    if (isDropdownButton) {
        const currentDropdown = e.target.closest('.dropdown');
        currentDropdown.classList.toggle('active');
        
        document.querySelectorAll('.dropdown.active').forEach(dropdown => {
            if (dropdown !== currentDropdown) dropdown.classList.remove('active');
        });
    } else {
        if (!e.target.closest('.dropdown-content')) {
            document.querySelectorAll('.dropdown.active').forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
    }
});

function confirmAction(id, mode) {
    const isDeactivating = (mode === 'nonaktif');
    Swal.fire({
        title: isDeactivating ? 'Nonaktifkan Produk?' : 'Aktifkan Produk Kembali?',
        text: isDeactivating ? 'Produk ini tidak akan ditampilkan ke pembeli.' : 'Produk ini akan kembali muncul di toko Anda.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isDeactivating ? '#d9534f' : '#8d9b7a',
        cancelButtonColor: '#aaa',
        confirmButtonText: isDeactivating ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan!'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = isDeactivating ? 'deleteProduct-seller.php' : 'restoreProduct-seller.php';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'idProduk';
            input.value = id;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

const notification = document.getElementById('notification');
if (notification) {
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>

<?php if (isset($_SESSION['success'])): ?>
    <div class="notification success" id="notification"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="notification error" id="notification"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/footer.php'; ?>