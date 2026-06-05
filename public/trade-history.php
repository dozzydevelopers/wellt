<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Trade History';
$trades=fetchAll("SELECT * FROM binary_trades WHERE user_id=? ORDER BY created_at DESC LIMIT 50",[$user['id']]);
$wins=fetchOne("SELECT COUNT(*) as c FROM binary_trades WHERE user_id=? AND result='win'",[$user['id']])['c'];
$loses=fetchOne("SELECT COUNT(*) as c FROM binary_trades WHERE user_id=? AND result='lose'",[$user['id']])['c'];
$totalProfit=fetchOne("SELECT SUM(profit) as s FROM binary_trades WHERE user_id=? AND result='win'",[$user['id']])['s']??0;
include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">Trade History</h2>
      <div class="stats-row mb-4">
        <div class="stat-box"><div class="stat-val positive"><?=$wins?></div><div class="stat-label">Wins</div></div>
        <div class="stat-box"><div class="stat-val negative"><?=$loses?></div><div class="stat-label">Losses</div></div>
        <div class="stat-box"><div class="stat-val positive"><?=formatMoney($totalProfit)?></div><div class="stat-label">Total Profit</div></div>
      </div>
      <div class="table-card">
        <table class="data-table">
          <thead><tr><th>Asset</th><th>Direction</th><th>Amount</th><th>Entry</th><th>Close</th><th>Payout</th><th>Result</th><th>Date</th></tr></thead>
          <tbody>
          <?php foreach($trades as $t): ?>
          <tr>
            <td><?=sanitize($t['asset'])?></td>
            <td class="<?=$t['direction']==='up'?'positive':'negative'?>"><?=strtoupper($t['direction'])?></td>
            <td><?=formatMoney($t['amount'])?></td>
            <td>$<?=number_format($t['entry_price'],2)?></td>
            <td><?=$t['close_price']?'$'.number_format($t['close_price'],2):'-'?></td>
            <td class="positive">$<?=number_format($t['amount']*$t['payout_percent']/100,2)?></td>
            <td><span class="badge badge-<?=$t['result']==='win'?'approved':($t['result']==='lose'?'rejected':'pending')?>"><?=ucfirst($t['result'])?></span></td>
            <td><?=date('M j, Y',strtotime($t['created_at']))?></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($trades)): ?><tr><td colspan="8" class="empty-row">No trades yet</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
