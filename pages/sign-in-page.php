<?php
$authPage = 'signin';
include '../includes/header-auth.php';
?>

<div class="container login-container pt-5 mt-5">
    <div class="row w-100">
        <div class="col-12 col-md-6 order-1 order-md-1 text-center mb-10 mb-md-0">
            <img src="../foto/signin.png" class="img-fluid" alt="Shopping Illustration">
        </div>

        <div class="col-12 col-md-5 offset-md-1 order-2 order-md-2">
            <h1 class="auth-title mb-4">Sign In</h1>

            <form>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" placeholder="Username">
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" placeholder="Password">
                </div>

                <button type="submit" class="btn w-75 py-2 mx-auto d-block">
                    Sign In
                </button>

                <p class="mt-3 text-center">
                    Don’t have account?
                    <a href="../pages/sign-up-page.php" class="fw-semibold text-decoration-none">
                        Sign Up
                    </a>
                </p>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>