<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Stock History';
// Stock transaction history based on user's holdings created date (simplified)
$holdings=fetchAll("SELECT sh.*,s.symbol,s.name FROM stock_holdings sh JOIN stocks s ON sh.stock_id=s.id WHERE sh.user_id=? ORDER BY sh.created_at DESC LIMIT 50",[$user['id']]);
include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">Stock History</h2>
      <div class="table-card">
        <table class="data-table">
          <thead><tr><th>Stock</th><th>Shares</th><th>Avg Buy Price</th><th>Date</th></tr></thead>
          <tbody>
          <?php foreach($holdings as $h): ?>
          <tr>
            <td><strong><?=sanitize($h['symbol'])?></strong> - <?=sanitize($h['name'])?></td>
            <td><?=number_format($h['shares'],4)?></td>
            <td>$<?=number_format($h['avg_buy_price'],2)?></td>
            <td><?=date('M j, Y',strtotime($h['created_at']))?></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($holdings)): ?><tr><td colspan="4" class="empty-row">No stock history yet</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
