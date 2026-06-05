<?php
$kycStatus = $user['kyc_status'] ?? 'pending';
$kycClass = $kycStatus === 'approved' ? 'kyc-approved' : ($kycStatus === 'submitted' ? 'kyc-pending' : 'kyc-none');
$kycLabel = strtoupper($kycStatus === 'approved' ? 'KYC' : ($kycStatus === 'submitted' ? 'PENDING' : 'KYC'));
$b = SITE_BASE;
?>
<div class="topbar">
  <button class="menu-toggle" onclick="toggleSidebar()">&#9776;</button>
  <div class="topbar-right">
    <a href="<?= $b ?>/kyc.php" class="kyc-badge <?= $kycClass ?>">
      <span class="kyc-dot"></span> <?= $kycLabel ?>
    </a>
    <div class="notif-wrap">
      <a href="<?= $b ?>/notifications.php" class="notif-btn">
        &#128276;
        <?php if ($_notifCount > 0): ?>
          <span class="notif-count"><?= $_notifCount ?></span>
        <?php endif; ?>
      </a>
    </div>
    <div class="avatar-btn" onclick="toggleUserMenu()"><?= strtoupper(substr($user['full_name'],0,1)) ?></div>
    <div class="user-menu" id="userMenu">
      <a href="<?= $b ?>/profile.php">Profile</a>
      <a href="<?= $b ?>/kyc.php">KYC Verification</a>
      <a href="<?= $b ?>/change-password.php">Change Password</a>
      <?php if ($user['is_admin']): ?><a href="<?= $b ?>/admin/index.php">Admin Panel</a><?php endif; ?>
      <a href="<?= $b ?>/logout.php" class="logout-link">Logout</a>
    </div>
  </div>
</div>
