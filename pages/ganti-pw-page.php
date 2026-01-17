<?php
session_start();
require '../includes/dbOnlinePOS.php';

if (!isset($_SESSION['login'])) {
    header("Location: sign-in-page.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

switch ($role) {
case 'pelanggan':
        $query = "SELECT usernamePelanggan AS username, namaPelanggan AS nama, fotoPelanggan AS foto_profil FROM tbPelanggan WHERE usernamePelanggan = ?";
        break;
    case 'penjual':
        $query = "SELECT usernamePenjual AS username, namaPenjual AS nama, fotoPenjual AS foto_profil FROM tbPenjual WHERE usernamePenjual = ?";
        break;
    case 'admin':
        $query = "SELECT username AS username, namaAdmin AS nama, foto AS foto_profil FROM tbAdmin WHERE username = ?";
        break;
}

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$headerFile = '../includes/header-main.php'; 
if ($role === 'admin') $headerFile = '../includes/header-admin.php';
elseif ($role === 'penjual') $headerFile = '../includes/header-seller.php';

$from = $_GET['from'] ?? 'profile'; 
$pageCSS = '../css/profile.css';
include $headerFile;
?>

<main class="profile-page container-fluid px-5 py-4">
    <div class="row g-4 align-items-start">
        <aside class="col-md-1">
            <div class="sidebar">
                <a href="profile.php" class="icon active" title="Profile"><i class="bi bi-person"></i></a>
                <a href="notifikasi.php" class="icon" title="Notifications"><i class="bi bi-bell"></i></a>
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
                                    <span class="label">Name</span>
                                    <span class="value"><?= htmlspecialchars($user['nama']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Username</span>
                                    <span class="value"><?= htmlspecialchars($user['username']); ?></span>
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
                            <?php 
                                $namaFile = $user['foto_profil']; 
                                $fotoDefault = "../foto/default.png"; 

                                if (isset($_SESSION['idPenjual'])) {
                                    $fotoDefault = "../foto/default-seller.jpg";
                                } elseif (isset($_SESSION['idAdmin'])) {
                                    $fotoDefault = "../foto/default-admin.jpg";
                                }

                                if (empty($namaFile) || !file_exists("../foto/" . $namaFile)) {
                                    $pathFoto = $fotoDefault;
                                } else {
                                    $pathFoto = "../foto/" . $namaFile;
                                }
                            ?>
                            
                            <div class="position-relative d-inline-block">
                                <img src="<?= $pathFoto; ?>" alt="Profile" 
                                class="rounded-circle img-thumbnail mb-3" 
                                style="width:200px; height:200px; object-fit:cover;">
                                
                                <form action="proses-ganti-pfp.php" method="POST" enctype="multipart/form-data" id="photoForm">
                                    <input type="hidden" name="redirect_to" value="<?= basename($_SERVER['PHP_SELF']); ?>">
                                    
                                    <input type="file" name="profile_img" id="profile_img" 
                                        style="display: none;" accept="image/*" 
                                        onchange="document.getElementById('photoForm').submit();">
                                    
                                    <button type="button" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 rounded-circle p-2" 
                                            onclick="document.getElementById('profile_img').click();">
                                        <i class="bi bi-camera"></i>
                                    </button>
                                </form>
                            </div>
                            
                            <div class="mt-2">
                                <button type="button" class="btn custom-btn btn-sm" onclick="document.getElementById('profile_img').click();">
                                    Change Image
                                </button>
                            </div>
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