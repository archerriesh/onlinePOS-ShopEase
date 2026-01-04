<?php
$pageCSS = '../../css/editProduct-seller.css';  
include("../../includes/header-main.php");
?>


<main class="container">
    <div class="left">
        <div class="card">
            <h2>General Information</h2>

            <label>Product name</label>
            <input type="text" value="Nike Free Metcon 6">

            <label>Product Description</label>
            <textarea rows="8">
Lorem Ipsum is simply dummy text of the printing and typesetting industry...
            </textarea>
        </div>

        <div class="card row">
            <div>
                <label>Category</label>
                <input type="text" value="Shoes">
            </div>
            <div>
                <label>Stock</label>
                <input type="number" value="45">
            </div>
        </div>
    </div>

    <div class="right">
        <div class="card">
            <h2>Upload Image</h2>

            <div class="image-box"></div>

            <div class="thumbs">
                <div class="thumb"></div>
                <div class="thumb"></div>
                <div class="thumb add">+</div>
            </div>
        </div>

        <div class="card">
            <label>Set price</label>
            <input type="text" value="Rp. 1.999.999">
        </div>

        <button class="btn">Save Changes</button>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>
