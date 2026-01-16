<?php
session_start();
$pageCSS = '../../css/my-voucher.css';
include __DIR__ . '/../../includes/header-seller.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';

$idPenjualLogin = isset($_SESSION['idPenjual']) ? $_SESSION['idPenjual'] : ''; 
?>

<div class="promo-page">
    <div class="button-container">
        <button class="btn btn-secondary" onclick="window.location.href='promo-seller.php'">ShopEase voucher</button>
        <button class="btn btn-primary" onclick="window.location.href='my-voucher.php'">My voucher</button>
        <button class="btn btn-secondary" onclick="window.location.href='addPromo-seller.php'">Add new voucher</button>
    </div>

    <div class="voucher-container">
        <button class="arrow arrow-left" id="prevBtn">&lt;</button>
        <button class="arrow arrow-right" id="nextBtn">&gt;</button>

        <div class="voucher-wrapper" id="voucherWrapper">
            <div class="voucher-grid">
                <?php
                $query = "SELECT * FROM tbpromo WHERE idPenjual = '$idPenjualLogin' ORDER BY idPromo DESC";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $isPersen = ($row['persentasePotongan'] > 0);
                        $valueDisplay = $isPersen ? $row['persentasePotongan'] . '%' : 'Rp' . number_format($row['nominalPotongan'], 0, ',', '.');
                        $icon = $isPersen ? "%" : "Rp";
                        $formattedDate = date('d M Y', strtotime($row['endDate']));
                ?>
                <div class="voucher-card" style="cursor: pointer;" onclick="window.location.href='kelola-voucher.php?id=<?php echo $row['idPromo']; ?>'">
                    <div class="voucher-icon"><?php echo $icon; ?></div>
                    <div class="voucher-divider"></div>
                    <div class="voucher-content">
                        <div class="voucher-title"><?php echo htmlspecialchars($row['namaPromo']); ?></div>
                        <div class="voucher-value"><?php echo $valueDisplay; ?></div>
                        <div class="voucher-terms">Valid until: <?php echo $formattedDate; ?></div>
                    </div>
                </div>
                <?php 
                        } 
                    } else {
                        echo "<p style='grid-column: 1/-1; text-align: center;'>Voucher Anda belum ditemukan.</p>";
                    }
                ?>
            </div> 
        </div> 
    </div> 
</div>

<script>
    const wrapper = document.getElementById('voucherWrapper');
    document.getElementById('nextBtn').onclick = () => wrapper.scrollLeft += 350;
    document.getElementById('prevBtn').onclick = () => wrapper.scrollLeft -= 350;
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>