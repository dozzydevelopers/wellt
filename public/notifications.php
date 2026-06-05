<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Notifications';

// Mark all as read
query("UPDATE notifications SET is_read=1 WHERE user_id=?", [$user['id']]);

$notifs = fetchAll("SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 50", [$user['id']]);
include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">Notifications</h2>
      <div class="notif-list">
        <?php foreach ($notifs as $n): ?>
        <div class="notif-item notif-<?= $n['type'] ?>">
          <div class="notif-title"><?= sanitize($n['title']) ?></div>
          <div class="notif-msg"><?= sanitize($n['message']) ?></div>
          <div class="notif-time"><?= date('M j, Y H:i', strtotime($n['created_at'])) ?></div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($notifs)): ?><div class="empty-state">No notifications yet.</div><?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
