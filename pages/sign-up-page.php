<?php 
$authPage = 'signup';
include 'includes/header-auth.php';
?>

<div class="container signup-container">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 text-center order-1 order-md-2">
            <img src="foto/signin.png" class="img-fluid auth-img" alt="Sign Up Illustration">
        </div>

        <div class="col-12 col-md-6 order-2 order-md-1">
            <h1 class="auth-title mb-4">Sign Up</h1>

            <form>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" placeholder="Full Name">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" placeholder="Create your username">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact</label>
                        <input type="text" class="form-control" placeholder="Phone number/email">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" placeholder="Address">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" placeholder="Password">
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" placeholder="Confirm password">
                </div>

                <button type="submit" class="btn w-50 py-2 d-block mx-auto">
                    Sign Up
                </button>

                <p class="mt-3">
                    Already have account?
                    <a href="sign-in.php" class="fw-semibold">Sign In</a>
                </p>
            </form>
        </div>
    </div>
</div>

<? include 'includes/footer.php'; ?>