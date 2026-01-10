<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: pages/sign-in-page.php");
    exit;
}

$pageCSS = '../css/home-page.css';
include '../includes/header-main.php';
include '../includes/dbOnlinePOS.php';

$sql = "
    SELECT idProduk, namaProduk, harga
    FROM tbproduk
    ORDER BY RAND()
    LIMIT 5
";
$result = mysqli_query($conn, $sql);

$basePath = "../foto/produk/";
$extensions = ['webp', 'jpg', 'jpeg','png'];
$defaultImage = "../assets/img/default.jpg";
?>

<main class="home-page">

    <section class="hero">
        <div class="home-container">
            <h1 class="fw-bold">Welcome to ShopEase</h1>
            <p class="subtitle">
                an online shopping platform designed to deliver convenience at every step.
            </p>
        </div>
    </section>

    <section class="trending">
        <div class="home-container">
            <h2 class="section-title">For you</h2>

            <div class="cards">
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

                    <article class="card text-decoration-none">
                        <a href="liat-produk.php?id=<?= $row['idProduk']; ?>">
                            <img src="<?= $gambarProduk; ?>" alt="<?= $row['namaProduk']; ?>">
                            <div class="card-content">
                                <h3><?= htmlspecialchars($row['namaProduk']); ?></h3>
                                <p class="price">
                                    Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                                </p>
                            </div>
                        </a>
                    </article>

                <?php } ?>
            </div>

        </div>
    </section>

</main>

<?php include '../includes/footer.php'; ?>
