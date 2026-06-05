<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'P2P Trading';
$holdings=fetchAll("SELECT sh.*,s.symbol,s.name,s.price,s.change_percent FROM stock_holdings sh JOIN stocks s ON sh.stock_id=s.id WHERE sh.user_id=? ORDER BY s.symbol",[$user['id']]);
$totalVal=0; foreach($holdings as &$h){ $h['current_value']=$h['shares']*$h['price']; $h['profit_loss']=$h['current_value']-($h['shares']*$h['avg_buy_price']); $totalVal+=$h['current_value']; }
include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">P2P Trading</h2>
      <div class="stat-box mb-4"><div class="stat-val"><?=formatMoney($totalVal)?></div><div class="stat-label">P2P Trading Value</div></div>
      <div class="table-card">
        <table class="data-table">
          <thead><tr><th>Symbol</th><th>Name</th><th>Shares</th><th>Avg Buy</th><th>Current</th><th>Value</th><th>P/L</th></tr></thead>
          <tbody>
          <?php foreach($holdings as $h): ?>
          <tr>
            <td><strong><?=sanitize($h['symbol'])?></strong></td>
            <td><?=sanitize($h['name'])?></td>
            <td><?=number_format($h['shares'],4)?></td>
            <td>$<?=number_format($h['avg_buy_price'],2)?></td>
            <td>$<?=number_format($h['price'],2)?> <small class="<?=$h['change_percent']>=0?'positive':'negative'?>"><?=$h['change_percent']>=0?'+':''?><?=number_format($h['change_percent'],2)?>%</small></td>
            <td><?=formatMoney($h['current_value'])?></td>
            <td class="<?=$h['profit_loss']>=0?'positive':'negative'?>"><?=$h['profit_loss']>=0?'+':''?><?=formatMoney($h['profit_loss'])?></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($holdings)): ?><tr><td colspan="7" class="empty-row">No P2P trades yet. Start by browsing multiple verified sellers and choosing a trusted offer that fits your budget.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
