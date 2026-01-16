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
        $query = "SELECT usernamePelanggan AS username, namaPelanggan AS nama, 
                  kontakPelanggan AS kontak, alamatPelanggan AS alamat 
                  FROM tbPelanggan WHERE usernamePelanggan = ?";
        break;
    case 'penjual':
        $query = "SELECT usernamePenjual AS username, namaPenjual AS nama, 
                  kontakPenjual AS kontak, alamatPenjual AS alamat, kategoriToko 
                  FROM tbPenjual WHERE usernamePenjual = ?";
        break;
    case 'admin':
        $query = "SELECT username AS username, namaAdmin AS nama, 
                  email AS kontak, alamat AS alamat, shiftStart, shiftEnd, specification 
                  FROM tbAdmin WHERE username = ?";
        break;
    default:
        header("Location: sign-in-page.php");
        exit;
}

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

$headerFile = '../includes/header-main.php'; 
if ($role === 'admin') {
    $headerFile = '../includes/header-admin.php';
} elseif ($role === 'penjual') {
    $headerFile = '../includes/header-seller.php';
}

$pageCSS = '../css/profile.css';
include $headerFile; 
?>

<main class="profile-page container-fluid px-5 py-4">
  <div class="row g-4 align-items-start">
    <aside class="col-md-1">
      <div class="sidebar">
        <a href="profile.php" class="icon active" title="Profile">
          <i class="bi bi-person"></i>
        </a>
        <a href="notifikasi.php" class="icon" title="Notifications">
          <i class="bi bi-bell"></i>
        </a>
        <a href="sign-out.php" class="icon logout" id="btnLogout" data-bs-toggle="modal" data-bs-target="#logoutModal">
          <i class="bi bi-box-arrow-right"></i>
        </a>
      </div>
    </aside>

    <div class="col-md-11">
      <section class="profile-content">
        <div class="profile-card row">
          <h2 class="text-center mb-4">My Profile</h2>
          <div class="col-md-7 profile-info">
            
            <div class="info-item">
              <span class="label">Name</span>
              <span class="value"><?= $user['nama']; ?></span>
            </div>
            
            <div class="info-item">
              <span class="label">Username</span>
              <span class="value"><?= $user['username']; ?></span>
            </div>
            
            <div class="info-item">
              <span class="label">Contact Info</span>
              <span class="value"><?= $user['kontak']; ?></span>
            </div>

            <div class="info-item">
              <span class="label">Address</span>
              <p class="value"><?= $user['alamat']; ?></p>
            </div>

            <?php if ($role === 'admin') : ?>
                <div class="info-item">
                    <span class="label">Working Hours</span>
                    <span class="value"><?= substr($user['shiftStart'], 0, 5); ?> - <?= substr($user['shiftEnd'], 0, 5); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Specification</span>
                    <span class="value"><?= $user['specification']; ?></span>
                </div>
            <?php elseif ($role === 'penjual') : ?>
                <div class="info-item">
                    <span class="label">Store Category</span>
                    <span class="value"><?= $user['kategoriToko']; ?></span>
                </div>
            <?php endif; ?>

            <div class="info-item">
              <span class="label">Password</span>
              <span class="value">••••••••</span>
              <a href="ganti-pw-page.php?from=profile" class="change-link">change password</a>
            </div>

            <a href="edit-profile-page.php" class="btn custom-btn mt-4">
              Edit Profile
            </a>
          </div>

          <div class="col-md-5 image-section text-center">
            <img src="../foto/keano.jpg" alt="Profile">
            <br>
            <a href="change-photo.php" class="btn custom-btn mt-3">
              Change Image
            </a>
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