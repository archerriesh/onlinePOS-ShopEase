

<form method="POST" action="process-change-password.php" class="profile-info">

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

  <button type="submit" class="btn btn-primary mt-3">
    Update Password
  </button>

</form>
