<?php
session_start();
require '../includes/dbOnlinePOS.php';

if (!isset($_SESSION['username'])) {
    header("Location: sign-in.php");
    exit;
}

$username = $_SESSION['username'];

$query = "SELECT * FROM tbPelanggan WHERE usernamePelanggan = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$from = $_GET['from'] ?? 'profile'; 
$pageCSS = '../css/profile.css';
include '../includes/header-main.php';
?>

<main class="profile-page container-fluid px-5 py-4">
    <div class="row g-4 align-items-start">
        <aside class="col-md-1">
            <div class="sidebar">
                <a href="profile.php" class="icon active" title="Profile"><i class="bi bi-person"></i></a>
                <a href="notifikasi.php" class="icon" title="Notifications"><i class="bi bi-bell"></i></a>
                <a href="history.php" class="icon" title="Orders"><i class="bi bi-box-seam"></i></a>
                <a href="liat-review.php" class="icon" title="Reviews"><i class="bi bi-chat-left-text"></i></a>
                <a href="sign-out.php" class="icon logout" id="btnLogout"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </aside>

        <div class="col-md-11">
            <section class="profile-content">
                <div class="profile-card">
                    <div class="row">
                        <div class="col-md-7 profile-info">
                            <?php if (isset($_GET['error'])): ?>
                                <div class="alert alert-danger mb-3">
                                    <?php
                                        switch ($_GET['error']) {
                                            case 'wrong': echo 'Current password is incorrect.'; break;
                                            case 'confirm': echo 'New password and confirmation do not match.'; break;
                                            case 'length': echo 'Password must be at least 8 characters.'; break;
                                            case 'updatefail': echo 'Gagal mengupdate database.'; break;
                                            default: echo 'Terjadi kesalahan sistem.'; break;
                                        }
                                    ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_GET['success'])): ?>
                                <div class="alert alert-success mb-3">
                                    Password successfully updated!
                                </div>
                            <?php endif; ?>

                            <h2 class="text-center mb-4"><?= ($from === 'edit') ? 'Change Password' : 'My Profile' ?></h2>

                            <?php if ($from === 'profile'): ?>
                                <div class="info-item">
                                    <span class="label">Username</span>
                                    <span class="value"><?= htmlspecialchars($user['usernamePelanggan']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Name</span>
                                    <span class="value"><?= htmlspecialchars($user['namaPelanggan']); ?></span>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="ganti-pw.php" class="profile-info mt-3">
                                <input type="hidden" name="from" value="<?= htmlspecialchars($from) ?>">
                                
                                <div class="info-item">
                                    <label>Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>

                                <div class="info-item">
                                    <label>New Password</label>
                                    <input type="password" name="new_password" class="form-control" required>
                                </div>

                                <div class="info-item">
                                    <label>Confirm New Password</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-primary mt-3 w-100">
                                    Update Password
                                </button>
                            </form>

                            <a href="<?= ($from === 'profile') ? 'profile.php' : 'edit-profile-page.php' ?>" class="btn btn-outline-secondary mt-4 w-100">
                                Back
                            </a>
                        </div>

                        <div class="col-md-5 image-section text-center">
                            <img src="../foto/keano.jpg" alt="Profile" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

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