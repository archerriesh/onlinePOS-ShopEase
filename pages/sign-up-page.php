<?php 
$authPage = 'signup';
include '../includes/header-auth.php';
?>

<div class="container signup-container">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 text-center order-1 order-md-2">
            <img src="../foto/signin.png" class="img-fluid auth-img" alt="Sign Up Illustration">
        </div>

        <div class="col-12 col-md-6 order-2 order-md-1">
            <h1 class="auth-title mb-4">Sign Up</h1>

            <form method="POST" action="sign-in-process.php">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="namaPelanggan" class="form-control" placeholder="Full Name">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" mame="usernamePelanggan"class="form-control" placeholder="Create your username">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact</label>
                        <input type="text" name="kontakPelanggan"class="form-control" placeholder="Phone number/email">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="alamatPelanggan" class="form-control" placeholder="Address">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="passwordPelanggan" class="form-control" placeholder="Password">
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirmPassword" class="form-control" placeholder="Confirm password">
                </div>

                <button type="submit" action="sign-up.php" class="btn w-50 py-2 d-block mx-auto">
                    Sign Up
                </button>

                <p class="mt-3 text-center">
                    Already have account?
                    <a href="../pages/sign-in-page.php" class="auth-link">Sign In</a>
                </p>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>