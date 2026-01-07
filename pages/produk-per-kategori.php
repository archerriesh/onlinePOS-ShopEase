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
} else {
    $sql = "
        SELECT idProduk, namaProduk, harga
        FROM tbproduk
        WHERE kategoriProduk = '$kategori'
    ";
}

$result = mysqli_query($conn, $sql);
?>

<div class="shopease-wrapper">
  <aside class="sidebar">
    <div class="menu-title">☰ Categories</div>
    <ul class="category-list">
      <li><a href="?kategori=Elektronik">electronics</a></li>
      <li><a href="?kategori=Fashion Pria">Man clothes</a></li>
      <li><a href="?kategori=Fashion Wanita">Woman clothes</a></li>
      <li><a href="?kategori=Home & Living">Home & Living</a></li>
      <li><a href="?kategori=Alat Musik">Music</a></li>
      <li><a href="?kategori=Tas & Dompet">Bags & Wallet</a></li>
      <li><a href="?kategori=Kecantikan">Skincare & Makeup</a></li>
      <li><a href="?kategori=Hobi & Koleksi">Hobbies</a></li>
      <li><a href="?kategori=Olahraga">Sport</a></li>
    </ul>
  </aside>

  <main class="content">
    <div class="product-grid">
      <?php if ($result && mysqli_num_rows($result) > 0) { ?>
  
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
          <div class="product-card">
            <a href="liat-produk.php?id=<?= $row['idProduk']; ?>">

              <img 
                src="../foto/produk/<?= $row['idProduk']; ?>.jpg"
                onerror="this.src='../assets/img/default.jpg'"
              >

              <h3><?= $row['namaProduk']; ?></h3>
              <p>Rp. <?= number_format($row['harga'], 0, ',', '.'); ?></p>

            </a>
          </div>
        <?php } ?>

      <?php } else { ?>
        <p>Categories not found</p>
      <?php } ?>

    </div>
  </main>
</div>


<?php include '../includes/footer.php'; ?>