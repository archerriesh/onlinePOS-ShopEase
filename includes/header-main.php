<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ShopEase</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">           

    <?php if (!empty($pageCSS)) : ?>
    <link rel="stylesheet" href="<?= $pageCSS ?>">
    <?php endif; ?>

</head>
<body>
<nav class="navbar navbar-expand-lg px-5 pt-4">
    <a class="navbar-brand fw-bold" href="#">ShopEase</a>

    <button class="navbar-toggler" type="button"
        data-bs-toggle="collapse"
        data-bs-target="#mainNavbar">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto gap-3">
            <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="../pages/liat-produk.php">Products</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Categories</a></li>
            <li class="nav-item"><a class="nav-link" href="../pages/co-keranjang.php">Cart</a></li>
            <li class="nav-item"><a class="nav-link" href="#">History</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Profile</a></li>
        </ul>
    </div>
</nav>

