<?php
$pageCSS = '../css/produk-per-kategori.css';
include '../includes/header-main.php';
include '../includes/dbOnlinePOS.php';

$kategori = $_GET['kategori'] ?? '';

if ($kategori == '') {
    $sql = "
        SELECT idProduk, namaProduk, harga
        FROM tbproduk
        ORDER BY RAND()
        LIMIT 8
    ";
    $stmt = mysqli_prepare($conn, $sql);
} else {
    $sql = "
        SELECT idProduk, namaProduk, harga
        FROM tbproduk
        WHERE kategoriProduk = ?
    ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $kategori);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$basePath = "../foto/produk/";
$extensions = ['webp', 'jpg', 'jpeg'];
$defaultImage = "../assets/img/default.jpg";
?>

<div class="shopease-wrapper">

  <aside class="sidebar">
    <div class="menu-title">☰ Categories</div>
    <ul class="category-list">
      <li><a href="?kategori=Elektronik">Electronics</a></li>
      <li><a href="?kategori=Fashion Pria">Man clothes</a></li>
      <li><a href="?kategori=Fashion Wanita">Woman clothes</a></li>
      <li><a href="?kategori=Fashion Muslim">Muslim Fashion</a></li>
      <li><a href="?kategori=Alat Musik">Music</a></li>
      <li><a href="?kategori=Alat Tulis">Stationery</a></li>
      <li><a href="?kategori=Kecantikan">Skincare & Makeup</a></li>
      <li><a href="?kategori=Hobi & Koleksi">Hobbies</a></li>
      <li><a href="?kategori=Olahraga">Sport</a></li>
    </ul>
  </aside>

  <main class="content">
    <div class="product-grid">

      <?php if ($result && mysqli_num_rows($result) > 0) { ?>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

          <?php
            $gambarProduk = $defaultImage;

            foreach ($extensions as $ext) {
                $file = $basePath . $row['idProduk'] . "." . $ext;
                if (file_exists($file)) {
                    $gambarProduk = $file;
                    break;
                }
            }
          ?>

          <div class="product-card">
            <a href="liat-produk.php?id=<?= $row['idProduk']; ?>">
              <img src="<?= $gambarProduk; ?>" alt="<?= htmlspecialchars($row['namaProduk']); ?>">
              <h3><?= htmlspecialchars($row['namaProduk']); ?></h3>
              <p>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
            </a>
          </div>

        <?php } ?>
      <?php } else { ?>
        <p class="empty">Produk tidak ditemukan</p>
      <?php } ?>

    </div>
  </main>
</div>

<?php include '../includes/footer.php'; ?>
