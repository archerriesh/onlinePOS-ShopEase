<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$isIndex = isset($index) && $index === true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ShopEase</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/OnlinePOS/css/style.css' ?>">

    <?php if (!empty($pageCSS)) : ?>
        <link rel="stylesheet" href="<?= $pageCSS ?>">
    <?php endif; ?>
</head>

<body>

<nav class="navbar navbar-expand-lg px-5 pt-4">
    <a class="navbar-brand fw-bold" href="/onlinePOS/pages/seller/home-seller.php">
        ShopEase
    </a>

    <?php if (!$isIndex) : ?>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto gap-3">
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'home-seller.php') ? 'active' : '' ?>"
                       href="/onlinePOS/pages/seller/home-seller.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'index-seller.php') ? 'active' : '' ?>"
                       href="/onlinePOS/pages/seller/index-seller.php">Products</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'promo-seller.php') ? 'active' : '' ?>"
                       href="/onlinePOS/pages/seller/promo-seller.php">Vouchers</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'history-seller.php') ? 'active' : '' ?>"
                       href="/onlinePOS/pages/seller/orders.php">Orders</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'profile.php') ? 'active' : '' ?>"
                       href="/onlinePOS/pages/profile.php">Profile</a>
                </li>
            </ul>
        </div>
    <?php endif; ?>
</nav>