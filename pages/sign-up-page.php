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

            <form method="POST" action="sign-up.php">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="nama" class="form-control" placeholder="Full Name" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Create your username" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact</label>
                        <input type="text" name="kontak" class="form-control" placeholder="Phone number/email" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="alamat" class="form-control" placeholder="Address" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                </div>

                <button type="submit" class="btn w-50 py-2 d-block mx-auto">
                    Sign Up
                </button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>