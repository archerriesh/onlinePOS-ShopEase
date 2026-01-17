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
                    <label class="form-label d-block">Signing up as:</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="role" value="pelanggan" checked> Buyer
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="role" value="penjual"> Seller
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="role" value="admin"> Admin
                    </div>
                </div>

                
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="nama" class="form-control" placeholder="Enter your full name" required>
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
                
                <div id="sectionPenjual" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label">Shop Category</label>
                        <input type="text" name="kategoriToko" class="form-control" placeholder="Shop Category">
                    </div>
                </div>

                <div id="sectionAdmin" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label">Specification</label>
                        <input type="text" name="specification" class="form-control" placeholder="Specification">
                    </div>
                    <div class="alert alert-info py-2">
                        <small>
                            <i class="fas fa-clock me-1"></i> 
                            <strong>Standard Working Hours:</strong> 09:00 AM - 05:00 PM
                        </small>
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

<script>
document.querySelectorAll('input[name="role"]').forEach((elem) => {
    elem.addEventListener("change", function(event) {
        const role = event.target.value;
        const sectionPenjual = document.getElementById('sectionPenjual');
        const sectionAdmin = document.getElementById('sectionAdmin');
        
        const inputKategori = document.querySelector('input[name="kategoriToko"]');
        const inputSpec = document.querySelector('input[name="specification"]');

        sectionPenjual.style.display = 'none';
        sectionAdmin.style.display = 'none';
        inputKategori.required = false;
        inputSpec.required = false;

        if (role === 'penjual') {
            sectionPenjual.style.display = 'block';
            inputKategori.required = true;
        } else if (role === 'admin') {
            sectionAdmin.style.display = 'block';
            inputSpec.required = true;
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>