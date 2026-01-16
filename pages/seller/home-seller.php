<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../includes/dbOnlinePOS.php';

// cek login
$idPenjual = $_SESSION['idPenjual'] ?? 0;
if ($idPenjual <= 0) {
    die("Penjual tidak ditemukan");
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

$namaPenjual = '';
$products = [];

while ($row = $result->fetch_assoc()) {
    $namaPenjual = $row['namaPenjual'];
    $products[] = $row;
}

$basePathServer = "../../foto/produk/";   
$basePathURL    = "../../foto/produk/";   

$pageCSS = '../../css/home-seller.css';
include("../../includes/header-seller.php");
?>

<div class="page-wrapper">
  <div class="seller-container">
    <div class="left">
      <h1>Welcome to ShopEase <?= htmlspecialchars($namaPenjual) ?>!</h1>
      <p>
        ShopEase is a web-based Online Point of Sale (POS) information system developed to support the digitization of business processes in the trade sector. This system allows sellers to manage product data, control stock, and record sales transactions. ShopEase is designed with a user-centered design approach so that it is easy to use by various types of users, both MSMEs and modern stores.
      </p>
    </div>
    <div class="right">
      <img src="../../foto/landing.png" class="auth-img">
    </div>
  </div>
</div>

<div class="container-home">
<h1>My Products</h1>

<?php if (empty($products)): ?>
<p>Belum ada produk</p>
<?php else: ?>

<div class="carousel-wrapper">

<img 
  id="carouselImg"
  src="../../foto/produk/default.jpg"
  class="carousel-main-img"
>

<p id="productName" class="product-name-carousel">
<?= htmlspecialchars($products[0]['namaProduk']) ?>
</p>

<div class="carousel-dots">
<?php foreach ($products as $i => $p): ?>
  <span class="dot <?= $i===0?'active':'' ?>" onclick="currentProduct(<?= $i ?>)"></span>
<?php endforeach; ?>
</div>

</div>

<script>
const products = [
<?php foreach ($products as $p): ?>
{
  id: "<?= $p['idProduk'] ?>",
  nama: "<?= addslashes($p['namaProduk']) ?>"
},
<?php endforeach; ?>
];

const basePath = "<?= $basePathURL ?>";


const imagePaths = {};
<?php foreach ($products as $p): ?>
<?php
$img = "default.jpg";
foreach (['webp','jpg','jpeg'] as $ext) {
  if (file_exists($basePathServer.$p['idProduk'].".".$ext)) {
    $img = $p['idProduk'].".".$ext;
    break;
  }
}
?>
imagePaths["<?= $p['idProduk'] ?>"] = basePath + "<?= $img ?>";
<?php endforeach; ?>

let currentIndex = 0;

function updateCarousel(index){
  currentIndex = (index + products.length) % products.length;
  const p = products[currentIndex];

  const img = document.getElementById("carouselImg");
  const name = document.getElementById("productName");

  img.classList.remove("slide-right");
  void img.offsetWidth;   
  img.src = imagePaths[p.id] || basePath + "default.jpg";
  img.classList.add("slide-right");

  name.textContent = p.nama;

  document.querySelectorAll(".dot").forEach((d,i)=>{
    d.classList.toggle("active", i===currentIndex);
  });
}

function nextProduct(){
  updateCarousel(currentIndex + 1);
}

function currentProduct(i){
  updateCarousel(i);
}

window.onload = function(){
  updateCarousel(0);
  setInterval(nextProduct, 3500);
};
</script>
<?php endif; ?>
</div>



<?php include __DIR__ . '/../../includes/footer.php'; ?>
