<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Copy History';
$history=fetchAll("SELECT ac.*,ct.name as trader_name,ct.roi FROM active_copies ac JOIN copy_traders ct ON ac.trader_id=ct.id WHERE ac.user_id=? ORDER BY ac.started_at DESC",[$user['id']]);
include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">Copy History</h2>
      <div class="table-card">
        <table class="data-table">
          <thead><tr><th>Trader</th><th>Amount</th><th>Profit</th><th>Status</th><th>Started</th><th>Stopped</th></tr></thead>
          <tbody>
          <?php foreach($history as $h): ?>
          <tr>
            <td><?=sanitize($h['trader_name'])?></td>
            <td><?=formatMoney($h['amount'])?></td>
            <td class="positive"><?=formatMoney($h['profit'])?></td>
            <td><span class="badge badge-<?=$h['status']==='active'?'approved':'stopped'?>"><?=ucfirst($h['status'])?></span></td>
            <td><?=date('M j, Y',strtotime($h['started_at']))?></td>
            <td><?=$h['stopped_at']?date('M j, Y',strtotime($h['stopped_at'])):'-'?></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($history)): ?><tr><td colspan="6" class="empty-row">No copy history yet</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
