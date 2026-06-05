<?php
require_once '../includes/auth.php';
requireAdmin();
$pageTitle = 'Investments';

$status = sanitize($_GET['status'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;
$where = "1=1";
$params = [];
if ($status) {
  $where .= " AND i.status=?";
  $params[] = $status;
}
$total = fetchOne("SELECT COUNT(*) as c FROM investments i WHERE $where", $params)['c'];
$invs = fetchAll("SELECT i.*,u.username,p.name as plan_name,p.daily_roi FROM investments i JOIN users u ON i.user_id=u.id JOIN plans p ON i.plan_id=p.id WHERE $where ORDER BY i.start_date DESC LIMIT $perPage OFFSET $offset", $params);
$pages = ceil($total / $perPage);
$summary = fetchOne("SELECT COUNT(*) as active_count, SUM(amount) as total_invested, SUM(daily_profit) as daily_payout, SUM(total_profit) as total_profit_paid FROM investments WHERE status='active'");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Investments - Admin</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
  <?php include 'includes/admin-sidebar.php'; ?>
  <div class="admin-main">
    <?php include 'includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <h2 class="admin-page-title">Investments (<?= number_format($total) ?>)</h2>
      <div class="admin-stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:16px">
        <div class="admin-stat-card green">
          <div class="asc-val"><?= number_format($summary['active_count']) ?></div>
          <div class="asc-label">Active</div>
        </div>
        <div class="admin-stat-card blue">
          <div class="asc-val"><?= formatMoney($summary['total_invested'] ?? 0) ?></div>
          <div class="asc-label">Total Invested</div>
        </div>
        <div class="admin-stat-card orange">
          <div class="asc-val"><?= formatMoney($summary['daily_payout'] ?? 0) ?></div>
          <div class="asc-label">Daily Payout</div>
        </div>
        <div class="admin-stat-card purple">
          <div class="asc-val"><?= formatMoney($summary['total_profit_paid'] ?? 0) ?></div>
          <div class="asc-label">Total Paid Out</div>
        </div>
      </div>
      <div class="admin-filters">
        <form method="GET" class="filter-form">
          <select name="status">
            <option value="">All</option><?php foreach (['active', 'completed', 'cancelled'] as $s): ?>
              <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-primary">Filter</button>
        </form>
      </div>
      <div class="admin-card">
        <table class="admin-table">
          <thead>
            <tr>
              <th>User</th>
              <th>Plan</th>
              <th>Amount</th>
              <th>Daily Profit</th>
              <th>Total Profit</th>
              <th>Start</th>
              <th>End</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($invs as $inv): ?>
              <tr>
                <td><?= sanitize($inv['username']) ?></td>
                <td><?= sanitize($inv['plan_name']) ?> <small>(<?= $inv['daily_roi'] ?>%)</small></td>
                <td><?= formatMoney($inv['amount']) ?></td>
                <td class="positive"><?= formatMoney($inv['daily_profit']) ?></td>
                <td class="positive"><?= formatMoney($inv['total_profit']) ?></td>
                <td><?= date('M j, Y', strtotime($inv['start_date'])) ?></td>
                <td><?= date('M j, Y', strtotime($inv['end_date'])) ?></td>
                <td><span
                    class="badge badge-<?= $inv['status'] === 'active' ? 'approved' : ($inv['status'] === 'completed' ? 'approved' : 'rejected') ?>"><?= ucfirst($inv['status']) ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($invs)): ?>
              <tr>
                <td colspan="8" class="empty-row">No investments found</td>
              </tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>

</html>