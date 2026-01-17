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
                  kontakPelanggan AS kontak, alamatPelanggan AS alamat, 
                  fotoPelanggan AS foto_profil, kategoriAkun
                  FROM tbPelanggan WHERE usernamePelanggan = ?";
        break;
    case 'penjual':
        $query = "SELECT usernamePenjual AS username, namaPenjual AS nama, 
                  kontakPenjual AS kontak, alamatPenjual AS alamat, kategoriToko, fotoPenjual AS foto_profil
                  FROM tbPenjual WHERE usernamePenjual = ?";
        break;
    case 'admin':
        $query = "SELECT username AS username, namaAdmin AS nama, 
                  email AS kontak, alamat AS alamat, foto AS foto_profil,
                  shiftStart, shiftEnd, specification 
                  FROM tbAdmin WHERE username = ?";
        break;
    default:
        header("Location: sign-in-page.php");
        exit;
}

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$kategori = $user['kategoriAkun'] ?? 'Bronze';
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
        <div class="profile-card"> 
          <div class="profile-header text-center mb-5">
            <h2>My Profile</h2>
            <?php 
              if (isset($_SESSION['role']) && $_SESSION['role'] == 'pelanggan') : 
                  $kategori = $userData['kategoriAkun'] ?? 'Bronze'; 
                  
                  $icon = "🥉";
                  if (strtolower($kategori) == 'gold') $icon = "✨";
                  elseif (strtolower($kategori) == 'silver') $icon = "🛡️";
            ?>
                <div class="buyer-badge badge-<?= strtolower($kategori) ?> mx-auto" style="width: fit-content;">
                    <?= $icon ?> <?= ucfirst($kategori) ?> Member
                </div>

            <?php endif;?>
          </div>

          <div class="row align-items-start">
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