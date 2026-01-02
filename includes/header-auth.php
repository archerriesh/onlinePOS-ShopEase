<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ShopEase</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg px-5 pt-4">
    <a class="navbar-brand fw-bold" href="index.php">ShopEase</a>

    <button class="navbar-toggler" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#authNavbar"
        aria-controls="authNavbar"
        aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="authNavbar">
        <div class="ms-auto">
            <?php if ($authPage === 'signin'): ?>
                <a href="sign-up.php" class="btn btn-outline-dark">
                    Sign Up
                </a>
            <?php else: ?>
                <a href="sign-in.php" class="btn btn-outline-dark">
                    Sign In
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

