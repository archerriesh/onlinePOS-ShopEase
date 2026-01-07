<?php
session_start();
require '../includes/dbOnlinePOS.php';

$username = $_SESSION['username'];

$query = "SELECT * FROM tbPelanggan WHERE usernamePelanggan = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

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

                <a href="sign-out.php" class="icon logout" id="btnLogout">
                <i class="bi bi-box-arrow-right"></i>
                </a>

            </div>
        </aside>

        <div class="col-md-11"> 
            <section class="profile-content">
                <div class="profile-card">
                    <div class="row align-items-start">
                        <div class="col-md-7">
                            <h2 class="text-center mb-4">Change Profile</h2>
                            <form method="POST" action="update-profile.php" class="profile-info">
                                <div class="info-item">
                                <label>Username</label>
                                <input type="text" name="usernamePelanggan"
                                    value="<?= $user['usernamePelanggan']; ?>"
                                    class="form-control">
                                </div>

                                <div class="info-item">
                                    <label>Name</label>
                                    <input type="text" name="namaPelanggan"
                                    value="<?= $user['namaPelanggan']; ?>"
                                    class="form-control">
                                </div>

                                <div class="info-item">
                                <label>Phone number</label>
                                <input type="text" name="kontakPelanggan"
                                value="<?= $user['kontakPelanggan']; ?>"
                                class="form-control">
                                </div>
                                
                                <div class="info-item">
                                    <label>Address</label>
                                <textarea name="alamatPelanggan"
                                class="form-control"><?= $user['alamatPelanggan']; ?></textarea>
                            </div>
                            
                            <div class="info-item">
                                <span class="label">Password</span>
                                <span class="value">••••••••</span>
                                <a href="ganti-pw-page.php?from=edit" class="change-link">
                                    change password
                                </a>
                                </div>
                                
                                <button type="submit" class="btn btn-primary mt-3">
                                    Save Changes
                                </button>
                            </form>
                        </div>

                        <div class="col-md-5 image-section text-center">
                            <img src="../foto/keano.jpg" alt="Profile">
                            <br>
                            <a href="change-photo.php" class="btn custom-btn mt-3">
                                Change Image
                            </a>
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