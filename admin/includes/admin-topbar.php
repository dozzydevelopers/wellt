<?php
$adminUser = getCurrentUser();
$pendingCount = 0;
$r1 = fetchOne("SELECT COUNT(*) as c FROM transactions WHERE status='pending'");
$pendingCount = $r1['c'] ?? 0;
$b = SITE_BASE;
?>
<div class="admin-topbar">
  <button class="admin-menu-toggle" onclick="toggleAdminSidebar()">&#9776;</button>
  <div class="admin-topbar-title"><?= isset($pageTitle) ? sanitize($pageTitle) : 'Admin Panel' ?></div>
  <div class="admin-topbar-right">
    <?php if ($pendingCount > 0): ?>
    <a href="<?= $b ?>/admin/transactions.php?status=pending" class="admin-alert-badge">
      &#9203; <?= $pendingCount ?> Pending
    </a>
    <?php endif; ?>
    <span class="admin-user-label">&#128100; <?= sanitize($adminUser['username']) ?></span>
    <a href="<?= $b ?>/logout.php" class="admin-logout-btn">Logout</a>
  </div>
</div>
