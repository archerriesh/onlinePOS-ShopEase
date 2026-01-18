<?php
$pageCSS = '../../css/admin/home.css';

include __DIR__ . '/../../includes/header-admin.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';

$sqlSeller = "SELECT COUNT(*) AS totalSeller FROM tbPenjual";
$resSeller = mysqli_query($conn, $sqlSeller);

$totalSeller = 0;
if ($resSeller) {
    $rowSeller = mysqli_fetch_assoc($resSeller);
    $totalSeller = $rowSeller['totalSeller'] ?? 0;
}

$sqlProduk = "SELECT COUNT(*) AS totalProduk FROM tbProduk";
$resProduk = mysqli_query($conn, $sqlProduk);

$totalProduk = 0;
if ($resProduk) {
    $rowProduk = mysqli_fetch_assoc($resProduk);
    $totalProduk = $rowProduk['totalProduk'] ?? 0;
}
?>

<div class="admin-home">

    <div class="admin-hero">

        <span class="admin-tag">ADMIN DASHBOARD</span>

            <h1>Manage ShopEase</h1>
            <p>
                Take full control of the sales system,<br>
                manage users, and monitor transactions in real-time.
            </p>

            <div class="admin-stats">
                <div class="stat-box">
                    <span class="stat-label">Seller Total</span>
                    <span class="stat-value">
                        <?= number_format($totalSeller); ?>
                    </span>
                </div>

                <div class="stat-box">
                    <span class="stat-label">Product Total</span>
                    <span class="stat-value">
                        <?= number_format($totalProduk); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="admin-visual">
            <img src="../../foto/landing.png" alt="Admin Landing">
        </div>

    </div>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
