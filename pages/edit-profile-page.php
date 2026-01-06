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

<form method="POST" action="update-profile.php" class="edit-profile-page container mt-5">
    <div class="info-item">
        <label>Username</label>
        <input type="text" name="usernamePelanggan" value="<?=$user['usernamePelanggan']; ?>" class="form-control">
    </div>

    <div class="info-item">
        <label>Name</label>
        <input type="text" name="namaPelanggan" value="<?= $user['namaPelanggan']; ?>" class="form-control">
    </div>

    <div class="info-item">
        <label>Phone number</label>
        <input type="text" name="kontakPelanggan" value="<?= $user['kontakPelanggan']; ?>" class="form-control">
    </div>

    <div class="info-item">
        <label>Address</label>
        <textarea name="alamatPelanggan" class="form-control"><?= $user['alamatPelanggan']; ?></textarea>
    </div>

    <div class="info-item">
        <span class="label">Password</span>
        <span class="value">••••••••</span>
        <a href="change-password.php" class="change-link">change password</a>
     </div>

    <button type="submit" class="btn btn-primary">
        Save Changes
    </button>

</form>

<?php include '../includes/footer.php'; ?>