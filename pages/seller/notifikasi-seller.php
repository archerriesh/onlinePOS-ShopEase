<?php
session_start();
require '../../includes/dbOnlinePOS.php';

if (!isset($_SESSION['idPenjual'])) {
    header("Location: sign-in-page.php");
    exit;
}

$idPenjual = $_SESSION['idPenjual'];

$stmtNotif = mysqli_prepare($conn, "
    SELECT judul, isiPesan, tglNotifikasi
    FROM tbnotifikasi
    WHERE idPenjual = ? AND idPelanggan IS NULL
    ORDER BY tglNotifikasi DESC
");

mysqli_stmt_bind_param($stmtNotif, "s", $idPenjual);
mysqli_stmt_execute($stmtNotif);
$notifs = mysqli_stmt_get_result($stmtNotif);

$pageCSS = '../../css/notifikasi.css';
include '../../includes/header-seller.php'; 
?>

<div class="notif-wrapper">
    <div class="notif-sidebar">
        <aside class="col-md-2">
            <div class="sidebar">
                <a href="../profile.php" class="icon" title="Profile">
                    <i class="bi bi-person"></i>
                </a>
                <a href="notifikasi-seller.php" class="icon active" title="Notifications">
                    <i class="bi bi-bell"></i>
                </a>
                <a href="../sign-out.php" class="icon logout" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </aside>
    </div>

    <section class="notif-content">
        <h2>Seller Notifications</h2>

        <?php if (mysqli_num_rows($notifs) === 0): ?>
            <div class="text-center mt-5">
                <i class="bi bi-mailbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-2">There's no notification yet.</p>
            </div>
        <?php endif; ?>

        <?php while ($notif = mysqli_fetch_assoc($notifs)) : ?>
            <div class="notif-card seller-variant">
                <div class="notif-text">
                    <h4><i class="bi bi-bag-check-fill me-2"></i><?= htmlspecialchars($notif['judul']) ?></h4>
                    <p><?= htmlspecialchars($notif['isiPesan']) ?></p>
                    <span class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        <?= date('d M Y, H:i', strtotime($notif['tglNotifikasi'])) ?>
                    </span>
                </div>
            </div>
        <?php endwhile; ?>
    </section>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to logout?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="../sign-out.php" class="btn btn-danger">Logout</a>
      </div>
    </div>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>