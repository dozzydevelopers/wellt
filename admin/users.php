<?php
require_once '../includes/auth.php';
requireAdmin();
$pageTitle = 'All Users';

$search = sanitize($_GET['search'] ?? '');
$status = sanitize($_GET['status'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 25;
$offset = ($page - 1) * $perPage;

$where = "is_admin = 0";
$params = [];
if ($search) {
  $where .= " AND (username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
  $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}
if ($status) {
  $where .= " AND status = ?";
  $params[] = $status;
}

$total = fetchOne("SELECT COUNT(*) as c FROM users WHERE $where", $params)['c'];
$users = fetchAll("SELECT * FROM users WHERE $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset", $params);
$pages = ceil($total / $perPage);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users - Admin</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
  <?php include 'includes/admin-sidebar.php'; ?>
  <div class="admin-main">
    <?php include 'includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <div class="admin-page-header">
        <h2 class="admin-page-title">All Users (<?= number_format($total) ?>)</h2>
        <a href="add-user.php" class="btn btn-primary">+ Add User</a>
      </div>

      <div class="admin-filters">
        <form method="GET" class="filter-form">
          <input type="text" name="search" placeholder="Search username, email, name..." value="<?= $search ?>">
          <select name="status">
            <option value="">All Status</option>
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
            <option value="banned" <?= $status === 'banned' ? 'selected' : '' ?>>Banned</option>
          </select>
          <button type="submit" class="btn btn-primary">Filter</button>
          <a href="users.php" class="btn btn-outline">Reset</a>
        </form>
      </div>

      <div class="admin-card">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Email</th>
              <th>Balance</th>
              <th>KYC</th>
              <th>Status</th>
              <th>Joined</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <tr>
                <td><?= $u['id'] ?></td>
                <td><strong><?= sanitize($u['username']) ?></strong><br><small><?= sanitize($u['full_name']) ?></small>
                </td>
                <td><?= sanitize($u['email']) ?></td>
                <td><?= formatMoney($u['balance']) ?></td>
                <td><span class="badge badge-<?= $u['kyc_status'] ?>"><?= ucfirst($u['kyc_status']) ?></span></td>
                <td><span class="badge badge-<?= $u['status'] ?>"><?= ucfirst($u['status']) ?></span></td>
                <td><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                <td class="action-btns">
                  <a href="user-edit.php?id=<?= $u['id'] ?>" class="btn-table">Edit</a>
                  <a href="add-funds.php?user_id=<?= $u['id'] ?>" class="btn-table green">Funds</a>
                  <a href="user-transactions.php?user_id=<?= $u['id'] ?>" class="btn-table blue">Txns</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
              <tr>
                <td colspan="8" class="empty-row">No users found</td>
              </tr><?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($pages > 1): ?>
        <div class="pagination">
          <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?search=<?= urlencode($search) ?>&status=<?= $status ?>&page=<?= $i ?>"
              class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>

</html>