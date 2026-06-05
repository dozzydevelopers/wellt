<?php
require_once '../includes/auth.php';
requireAdmin();
$pageTitle = 'Broadcast Notification';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = sanitize($_POST['title'] ?? '');
  $message = sanitize($_POST['message'] ?? '');
  $type = sanitize($_POST['type'] ?? 'info');
  $target = sanitize($_POST['target'] ?? 'all');

  if ($title && $message) {
    $where = "is_admin=0";
    if ($target === 'active')
      $where .= " AND status='active'";
    $users = fetchAll("SELECT id FROM users WHERE $where");
    foreach ($users as $u) {
      insert(
        "INSERT INTO notifications (user_id,title,message,type) VALUES (?,?,?,?)",
        [$u['id'], $title, $message, $type]
      );
    }
    $success = "Notification sent to " . count($users) . " users!";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Broadcast - Admin</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
  <?php include 'includes/admin-sidebar.php'; ?>
  <div class="admin-main">
    <?php include 'includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <h2 class="admin-page-title">Broadcast Notification</h2>
      <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div><?php endif; ?>
      <div class="admin-card" style="max-width:600px">
        <form method="POST">
          <div class="form-group"><label>Title</label><input type="text" name="title" required></div>
          <div class="form-group"><label>Message</label><textarea name="message" rows="4" required></textarea></div>
          <div class="form-row">
            <div class="form-group"><label>Type</label>
              <select name="type">
                <option value="info">Info</option>
                <option value="success">Success</option>
                <option value="warning">Warning</option>
                <option value="error">Alert</option>
              </select>
            </div>
            <div class="form-group"><label>Target</label>
              <select name="target">
                <option value="all">All Users</option>
                <option value="active">Active Only</option>
              </select>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Send Broadcast</button>
        </form>
      </div>
    </div>
  </div>
</body>

</html>