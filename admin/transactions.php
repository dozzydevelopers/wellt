<?php
require_once '../includes/auth.php';
requireAdmin();
$pageTitle = 'All Transactions';

$type = sanitize($_GET['type'] ?? '');
$status = sanitize($_GET['status'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 25;
$offset = ($page - 1) * $perPage;

$where = "1=1";
$params = [];
if ($type) {
  $where .= " AND t.type=?";
  $params[] = $type;
}
if ($status) {
  $where .= " AND t.status=?";
  $params[] = $status;
}
if ($search) {
  $where .= " AND (u.username LIKE ? OR u.email LIKE ? OR t.reference LIKE ?)";
  $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}

$total = fetchOne("SELECT COUNT(*) as c FROM transactions t JOIN users u ON t.user_id=u.id WHERE $where", $params)['c'];
$txns = fetchAll("SELECT t.*,u.username,u.email FROM transactions t JOIN users u ON t.user_id=u.id WHERE $where ORDER BY t.created_at DESC LIMIT $perPage OFFSET $offset", $params);
$pages = ceil($total / $perPage);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Transactions - Admin</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
  <?php include 'includes/admin-sidebar.php'; ?>
  <div class="admin-main">
    <?php include 'includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <h2 class="admin-page-title">All Transactions (<?= number_format($total) ?>)</h2>
      <div class="admin-filters">
        <form method="GET" class="filter-form">
          <input type="text" name="search" placeholder="Search username, email, ref..." value="<?= $search ?>">
          <select name="type">
            <option value="">All Types</option>
            <?php foreach (['deposit', 'withdrawal', 'profit', 'bonus', 'referral'] as $t): ?>
              <option value="<?= $t ?>" <?= $type === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option><?php endforeach; ?>
          </select>
          <select name="status">
            <option value="">All Status</option><?php foreach (['pending', 'approved', 'rejected', 'completed'] as $s): ?>
              <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-primary">Filter</button>
          <a href="transactions.php" class="btn btn-outline">Reset</a>
        </form>
      </div>
      <div class="admin-card">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>User</th>
              <th>Type</th>
              <th>Amount</th>
              <th>Reference</th>
              <th>Method</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($txns as $t): ?>
              <tr>
                <td><?= date('M j, Y H:i', strtotime($t['created_at'])) ?></td>
                <td><?= sanitize($t['username']) ?><br><small><?= sanitize($t['email']) ?></small></td>
                <td><?= ucfirst($t['type']) ?></td>
                <td class="<?= in_array($t['type'], ['deposit', 'profit', 'bonus', 'referral']) ? 'positive' : 'negative' ?>">
                  <?= formatMoney($t['amount']) ?></td>
                <td><small><?= sanitize($t['reference']) ?></small></td>
                <td><?= sanitize($t['payment_method'] ?? '-') ?></td>
                <td><span class="badge badge-<?= $t['status'] ?>"><?= ucfirst($t['status']) ?></span></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($txns)): ?>
              <tr>
                <td colspan="7" class="empty-row">No transactions found</td>
              </tr><?php endif; ?>
          </tbody>
        </table>
      </div>
      <?php if ($pages > 1): ?>
        <div class="pagination"><?php for ($i = 1; $i <= $pages; $i++): ?><a
              href="?type=<?= $type ?>&status=<?= $status ?>&search=<?= urlencode($search) ?>&page=<?= $i ?>"
              class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a><?php endfor; ?></div><?php endif; ?>
    </div>
  </div>
</body>

</html>