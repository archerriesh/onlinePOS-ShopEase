<?php
$pageCSS = '../../css/addPromo-seller.css';
include("../../includes/header-seller.php");
?>
<div class="button-container">
    <button class="btn btn-secondary" onclick="window.location.href='promo-seller.php'">Your voucher</button>
    <button class="btn btn-primary">Add new voucher</button>
</div>

<div class="form-container">
    <form id="promoForm">
        <div class="form-group">
            <label for="promoName">Promo name</label>
            <input type="text" id="promoName" name="promoName" required>
        </div>
        
        <div class="form-group">
            <label for="promoType">Promo type</label>
            <input type="text" id="promoType" name="promoType" required>
        </div>
        
        <div class="date-group">
            <div class="form-group">
                <label for="startDate">Start Date</label>
                <input type="date" id="startDate" name="startDate" required>
            </div>
            
            <div class="form-group">
                <label for="endDate">End Date</label>
                <input type="date" id="endDate" name="endDate" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="terms">Terms and Condition</label>
            <textarea id="terms" name="terms" rows="4" required></textarea>
        </div>
        
        <button type="submit" class="btn-add">Add</button>
    </form>
</div>

<script>
document.getElementById('promoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        promoName: document.getElementById('promoName').value,
        promoType: document.getElementById('promoType').value,
        startDate: document.getElementById('startDate').value,
        endDate: document.getElementById('endDate').value,
        terms: document.getElementById('terms').value
    };
    
    console.log('Form submitted:', formData);
    alert('Promo berhasil ditambahkan!');
    
    // Redirect ke halaman voucher setelah submit
    // window.location.href = 'promo-seller.html';
});
</script>
<?php include '../../includes/footer.php'; ?>