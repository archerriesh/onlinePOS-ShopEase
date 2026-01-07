<?php
session_start();
require '../includes/dbOnlinePOS.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: sign-in-page.php");
    exit;
}

$username = $_SESSION['username'];

$query = "SELECT 
            usernamePelanggan,
            namaPelanggan,
            kontakPelanggan,
            alamatPelanggan
          FROM tbPelanggan
          WHERE usernamePelanggan = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

$pageCSS = '../css/profile.css';
include '../includes/header-main.php';
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

        <a href="history.php" class="icon" title="Orders">
          <i class="bi bi-box-seam"></i>
        </a>

        <a href="liat-review.php" class="icon" title="Reviews">
          <i class="bi bi-chat-left-text"></i>
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
              <span class="label">Username</span>
              <span class="value"><?= $user['usernamePelanggan']; ?></span>
            </div>

            <div class="info-item">
              <span class="label">Name</span>
              <span class="value"><?= $user['namaPelanggan']; ?></span>
            </div>

            <div class="info-item">
              <span class="label">Phone number</span>
              <span class="value"><?= $user['kontakPelanggan']; ?></span>
            </div>

            <div class="info-item">
              <span class="label">Address</span>
              <p class="value"><?= $user['alamatPelanggan']; ?></p>
            </div>

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