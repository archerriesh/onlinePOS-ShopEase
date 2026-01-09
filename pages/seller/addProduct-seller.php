<?php
$pageCSS = '../../css/addProduct-seller.css';
include("../../includes/header-seller.php");
?>

<div class="page-wrapper">
    <form action="addProductProsesSeller.php" method="POST" enctype="multipart/form-data">

        <main class="container">

            <!-- LEFT -->
            <div class="left">

                <div class="card">
                    <h2>General Information</h2>

                    <label>Product Name</label>
                    <input type="text" name="namaProduk" required>

                    <label>Product Description</label>
                    <textarea name="keterangan" rows="8" required></textarea>
                </div>

                <div class="card row">
                    <div>
                        <label>Category</label>
                        <input type="text" name="kategoriProduk" required>
                    </div>
                    <div>
                        <label>Stock</label>
                        <input type="number" name="stok" min="0" required>
                    </div>
                </div>

            </div>

            <!-- RIGHT -->
            <div class="right">

                <div class="card">
                    <h2>Upload Image</h2>
                    <input type="file" name="gambar" accept="image/*">
                </div>

                <div class="card">
                    <label>Set Price</label>
                    <input type="number" name="harga" min="0" required>
                </div>

                <button type="submit" class="btn">Add Product</button>

            </div>

        </main>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>
