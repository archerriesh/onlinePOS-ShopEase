<?php
$pageCSS = '../../css/admin/liat-promo.css';

include __DIR__ . '/../../includes/header-admin.php';
include __DIR__ . '/../../includes/dbOnlinePOS.php';
?>

<div class="promo-page">

    <div class="button-container">
        <button class="btn btn-primary" onclick="window.location.href='promo-seller.php'">
            Your voucher
        </button>
        <button class="btn btn-secondary"
            onclick="window.location.href='tambah-promo.php'">
            Add new voucher
        </button>
    </div>

    <div class="voucher-container">
        <button class="arrow arrow-left" id="prevBtn">&lt;</button>
        <button class="arrow arrow-right" id="nextBtn">&gt;</button>

        <div class="voucher-wrapper" id="voucherWrapper">
            <div class="voucher-grid">

                <?php
                $query = "SELECT * FROM tbpromo ORDER BY idPromo DESC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        
                        // Logika Potongan
                        $isPersen = ($row['persentasePotongan'] > 0);
                        $valueDisplay = $isPersen ? $row['persentasePotongan'] . '%' : 'Rp' . number_format($row['nominalPotongan'] / 1000, 0) . 'k';
                        $icon = $isPersen ? "%" : "Rp";
                        
                        $formattedDate = date('d M Y', strtotime($row['endDate']));
                ?>

                <div class="voucher-card"onclick="window.location.href='kelola-promo.php?id=<?php echo $row['idPromo']; ?>'">
                    <div class="voucher-icon"><?php echo $icon; ?></div>
                    <div class="voucher-divider"></div>
                    <div class="voucher-content">
                        <div class="voucher-title"><?php echo htmlspecialchars($row['namaPromo']); ?></div>
                        <div class="voucher-value"><?php echo $valueDisplay; ?></div>
                        <div class="voucher-terms">Valid until: <?php echo $formattedDate; ?></div>
                    </div>
                    
                    <div class="voucher-actions">
                        <a href="editPromo.php?id=<?php echo $row['idPromo']; ?>" class="act-edit"><i class="fas fa-edit"></i></a>
                        <a href="deletePromo.php?id=<?php echo $row['idPromo']; ?>" class="act-delete" onclick="return confirm('Hapus promo ini?')"><i class="fas fa-trash"></i></a>
                    </div>
                </div>

                <?php 
                    } 
                } else {
                    echo "<p style='color: #61593d; grid-column: 1/-1; text-align: center;'>No vouchers found.</p>";
                }
                ?>

            </div> 
        </div> 
    </div> 

</div> 

<script>
    const wrapper = document.getElementById('voucherWrapper');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');

    nextBtn.addEventListener('click', () => {
        wrapper.scrollLeft += 350;
    });

    prevBtn.addEventListener('click', () => {
        wrapper.scrollLeft -= 350;
    });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>