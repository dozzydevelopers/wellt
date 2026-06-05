<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Profit History';

$profits = fetchAll("SELECT t.*, i.plan_id, p.name as plan_name FROM transactions t LEFT JOIN investments i ON t.description LIKE '%' || i.id || '%' LEFT JOIN plans p ON i.plan_id=p.id WHERE t.user_id=? AND t.type='profit' ORDER BY t.created_at DESC", [$user['id']]);
$totalProfit = fetchOne("SELECT SUM(amount) as total FROM transactions WHERE user_id=? AND type='profit'", [$user['id']]);
include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">Profit History</h2>
      <div class="stat-box mb-4">
        <div class="stat-val positive"><?= formatMoney($totalProfit['total'] ?? 0) ?></div>
        <div class="stat-label">Total Profit Earned</div>
      </div>
      <div class="table-card">
        <table class="data-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Description</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($profits as $p): ?>
              <tr>
                <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
                <td><?= sanitize($p['description'] ?? 'Daily profit') ?></td>
                <td class="positive">+<?= formatMoney($p['amount']) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($profits)): ?>
              <tr>
                <td colspan="3" class="empty-row">No profits yet. Start investing!</td>
              </tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>