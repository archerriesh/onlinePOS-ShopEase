<?php
$user = [
    "username" => "Keano",
    "name" => "Keano",
    "phone" => "(+62) 80814022008",
    "address" => "Jalan Surya Sumantri 37 (masuk gang sebelah Indomaret), Sukagalih, Sukajadi, Kota Bandung, Bandung Kulon, Jawa Barat (40212)",
];

$pageCSS = '../css/profile.css';
include '../includes/header-main.php';
?>

<main class="profile-page container-fluid px-5 py-4">
  <div class="row g-4">

    <aside class="col-auto">
      <div class="sidebar">
        <div class="icon active">👤</div>
        <div class="icon">🔔</div>
        <div class="icon">📦</div>
        <div class="icon">📄</div>
      </div>
    </aside>

    <section class="profile-content">
        <div class="profile-card row">
          <h2 class="text-center mb-4">My Profile</h2>
            <div class="col-md-7 profile-info">

            <div class="info-item">
                <span class="label">Username</span>
                <span class="value"><?= $user['username']; ?></span>
            </div>

            <div class="info-item">
                <span class="label">Name</span>
                <span class="value"><?= $user['name']; ?></span>
            </div>

            <div class="info-item">
                <span class="label">Phone number</span>
                <span class="value"><?= $user['phone']; ?></span>
            </div>

            <div class="info-item">
                <span class="label">Address</span>
                <p class="value"><?= $user['address']; ?></p>
            </div>

            <div class="info-item">
                <span class="label">Password</span>
                <span class="value">••••••••</span>
                <a href="change-password.php" class="change-link">change password</a>
            </div>

            <a href="edit-profile.php" class="btn custom-btn mt-4">
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
</main>

<?php include '../includes/footer.php'; ?>