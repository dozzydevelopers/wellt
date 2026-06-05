<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Active Copies';
$success='';

if ($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['stop_id'])) {
    $copyId=intval($_POST['stop_id']);
    $copy=fetchOne("SELECT * FROM active_copies WHERE id=? AND user_id=? AND status='active'",[$copyId,$user['id']]);
    if ($copy) {
        query("UPDATE active_copies SET status='stopped',stopped_at=datetime('now') WHERE id=?",[$copyId]);
        query("UPDATE users SET balance=balance+? WHERE id=?",[$copy['amount'],$user['id']]);
        query("UPDATE copy_traders SET followers=MAX(0,followers-1) WHERE id=?",[$copy['trader_id']]);
        sendNotification($user['id'],'Copy Stopped','You have stopped copying the trader. Amount returned to balance.','info');
        $success='Copy trading stopped. Funds returned to balance.';
        $user=getCurrentUser();
    }
}

$copies=fetchAll("SELECT ac.*,ct.name as trader_name,ct.roi FROM active_copies ac JOIN copy_traders ct ON ac.trader_id=ct.id WHERE ac.user_id=? AND ac.status='active'",[$user['id']]);
include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">Active Copies</h2>
      <?php if ($success): ?><div class="alert alert-success"><?=$success?></div><?php endif; ?>
      <div class="table-card">
        <table class="data-table">
          <thead><tr><th>Trader</th><th>Amount</th><th>Profit</th><th>ROI</th><th>Started</th><th>Action</th></tr></thead>
          <tbody>
          <?php foreach($copies as $c): ?>
          <tr>
            <td><?=sanitize($c['trader_name'])?></td>
            <td><?=formatMoney($c['amount'])?></td>
            <td class="positive"><?=formatMoney($c['profit'])?></td>
            <td class="positive"><?=$c['roi']?>%</td>
            <td><?=date('M j, Y',strtotime($c['started_at']))?></td>
            <td>
              <form method="POST" style="display:inline">
                <input type="hidden" name="stop_id" value="<?=$c['id']?>">
                <button type="submit" class="btn btn-sm btn-outline" onclick="return confirm('Stop copying?')">Stop</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($copies)): ?><tr><td colspan="6" class="empty-row">No active copies. <a href="<?= SITE_BASE ?>/copy-trading.php">Start copying</a></td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
