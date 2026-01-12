<?php
$pageCSS = '../css/notifikasi.css';
include '../includes/header-main.php';

session_start();

if (!isset($_SESSION['idPelanggan'])) {
    header("Location: sign-in-page.php");
    exit;
}

include '../includes/dbOnlinePOS.php';

$idPelanggan = $_SESSION['idPelanggan'];

$stmtNotif = mysqli_prepare($conn, "
    SELECT judul, isiPesan, tglNotifikasi
    FROM tbnotifikasi
    WHERE idPelanggan = ?
    ORDER BY tglNotifikasi DESC
");

if (!$stmtNotif) {
    die(mysqli_error($conn));
}

mysqli_stmt_bind_param($stmtNotif, "s", $idPelanggan);
mysqli_stmt_execute($stmtNotif);
$notifs = mysqli_stmt_get_result($stmtNotif);
?>

<div class="notif-wrapper">
    <div class="notif-sidebar">
        <aside class="col-md-2">
            <div class="sidebar">
                <a href="profile.php" class="icon" title="Profile">
                    <i class="bi bi-person"></i>
                </a>
                <a href="notifikasi.php" class="icon active" title="Notifications">
                    <i class="bi bi-bell"></i>
                </a>
                <a href="sign-out.php" class="icon logout" id="btnLogout" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </aside>
    </div>

    <section class="notif-content">
        <h2>Notification</h2>

        <?php if (mysqli_num_rows($notifs) === 0): ?>
            <p class="text-muted">No notifications yet.</p>
        <?php endif; ?>

        <?php while ($notif = mysqli_fetch_assoc($notifs)) { ?>
            <div class="notif-card">
                <div class="notif-text">
                    <h4><?= htmlspecialchars($notif['judul']) ?></h4>
                    <p><?= htmlspecialchars($notif['isiPesan']) ?></p>
                    <span>
                        <?= date('d-m-Y H:i', strtotime($notif['tglNotifikasi'])) ?>
                    </span>
                </div>
            </div>
        <?php } ?>
    </section>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Confirm Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        Are you sure you want to logout?
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          Cancel
        </button>
        <a href="sign-out.php" class="btn btn-danger">
          Logout
        </a>
      </div>

    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>