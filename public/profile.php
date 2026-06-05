<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Profile';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitize($_POST['full_name'] ?? '');
    $phone    = sanitize($_POST['phone'] ?? '');
    $country  = sanitize($_POST['country'] ?? '');

    if (!$fullName || !$phone || !$country) {
        $error = 'All fields are required.';
    } else {
        $avatarPath = $user['avatar'];
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                $filename = 'avatar_' . $user['id'] . '.' . $ext;
                $dir = '../uploads/avatars/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                move_uploaded_file($_FILES['avatar']['tmp_name'], $dir . $filename);
                $avatarPath = $filename;
            }
        }
        query("UPDATE users SET full_name=?, phone=?, country=?, avatar=? WHERE id=?",
            [$fullName, $phone, $country, $avatarPath, $user['id']]);
        $_SESSION['full_name'] = $fullName;
        $user = getCurrentUser();
        $success = 'Profile updated successfully!';
    }
}

$countries = ['Afghanistan','Albania','Algeria','Argentina','Australia','Austria','Bangladesh','Belgium','Brazil','Canada','China','Colombia','Czech Republic','Denmark','Egypt','Ethiopia','Finland','France','Germany','Ghana','Greece','Hungary','India','Indonesia','Iran','Iraq','Ireland','Israel','Italy','Japan','Jordan','Kazakhstan','Kenya','Kuwait','Malaysia','Mexico','Morocco','Netherlands','New Zealand','Nigeria','Norway','Pakistan','Philippines','Poland','Portugal','Romania','Russia','Saudi Arabia','Singapore','South Africa','South Korea','Spain','Sri Lanka','Sweden','Switzerland','Taiwan','Thailand','Turkey','Uganda','Ukraine','United Arab Emirates','United Kingdom','United States','Venezuela','Vietnam','Zimbabwe'];
include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">My Profile</h2>
      <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

      <div class="card profile-card">
        <div class="profile-avatar-section">
          <?php if ($user['avatar']): ?>
          <img src="/uploads/avatars/<?= $user['avatar'] ?>" class="profile-avatar-img" alt="Avatar">
          <?php else: ?>
          <div class="profile-avatar-placeholder"><?= strtoupper(substr($user['full_name'],0,1)) ?></div>
          <?php endif; ?>
          <div>
            <h3><?= sanitize($user['full_name']) ?></h3>
            <p>@<?= sanitize($user['username']) ?></p>
            <p>Referral ID: <strong><?= $user['referral_id'] ?></strong></p>
          </div>
        </div>

        <form method="POST" enctype="multipart/form-data">
          <div class="form-row">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" name="full_name" value="<?= sanitize($user['full_name']) ?>" required>
            </div>
            <div class="form-group">
              <label>Username</label>
              <input type="text" value="<?= sanitize($user['username']) ?>" disabled>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Email</label>
              <input type="email" value="<?= sanitize($user['email']) ?>" disabled>
            </div>
            <div class="form-group">
              <label>Phone</label>
              <input type="tel" name="phone" value="<?= sanitize($user['phone']) ?>" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Country</label>
              <select name="country" required>
                <?php foreach ($countries as $c): ?>
                <option value="<?= $c ?>" <?= $user['country']===$c?'selected':'' ?>><?= $c ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Profile Photo</label>
              <input type="file" name="avatar" accept="image/*">
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
