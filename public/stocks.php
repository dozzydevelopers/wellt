<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Stocks';
$success=''; $error='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $stockId=intval($_POST['stock_id']??0); $action=sanitize($_POST['action']??'buy'); $amount=floatval($_POST['amount']??0);
    $stock=fetchOne("SELECT * FROM stocks WHERE id=? AND status='active'",[$stockId]);
    if (!$stock) { $error='Invalid stock.'; }
    elseif ($amount<10) { $error='Minimum order is $10.'; }
    elseif ($action==='buy'&&$amount>$user['balance']) { $error='Insufficient balance.'; }
    else {
        $shares=$amount/$stock['price'];
        if ($action==='buy') {
            query("UPDATE users SET balance=balance-? WHERE id=?",[$amount,$user['id']]);
            $existing=fetchOne("SELECT * FROM stock_holdings WHERE user_id=? AND stock_id=?",[$user['id'],$stockId]);
            if ($existing) {
                $totalShares=$existing['shares']+$shares;
                $avgPrice=($existing['shares']*$existing['avg_buy_price']+$amount)/($totalShares);
                query("UPDATE stock_holdings SET shares=?,avg_buy_price=? WHERE id=?",[$totalShares,$avgPrice,$existing['id']]);
            } else {
                insert("INSERT INTO stock_holdings (user_id,stock_id,shares,avg_buy_price) VALUES (?,?,?,?)",[$user['id'],$stockId,$shares,$stock['price']]);
            }
            $success="Bought ".number_format($shares,4)." shares of {$stock['symbol']} for ".formatMoney($amount);
        } else {
            $holding=fetchOne("SELECT * FROM stock_holdings WHERE user_id=? AND stock_id=?",[$user['id'],$stockId]);
            if (!$holding||$holding['shares']<$shares) { $error='Insufficient shares.'; }
            else {
                $proceeds=$shares*$stock['price'];
                query("UPDATE users SET balance=balance+? WHERE id=?",[$proceeds,$user['id']]);
                $remaining=$holding['shares']-$shares;
                if ($remaining<0.00001) query("DELETE FROM stock_holdings WHERE id=?",[$holding['id']]);
                else query("UPDATE stock_holdings SET shares=? WHERE id=?",[$remaining,$holding['id']]);
                $success="Sold ".number_format($shares,4)." shares of {$stock['symbol']} for ".formatMoney($proceeds);
            }
        }
        $user=getCurrentUser();
    }
}

$stocks=fetchAll("SELECT * FROM stocks WHERE status='active' ORDER BY symbol ASC");
include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">Stocks</h2>
      <?php if ($success): ?><div class="alert alert-success"><?=$success?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-error"><?=$error?></div><?php endif; ?>
      <p>Balance: <strong><?=formatMoney($user['balance'])?></strong></p>

      <div class="table-card">
        <table class="data-table">
          <thead><tr><th>Symbol</th><th>Name</th><th>Price</th><th>Change</th><th>Actions</th></tr></thead>
          <tbody>
          <?php foreach($stocks as $s): ?>
          <tr>
            <td><strong><?=sanitize($s['symbol'])?></strong></td>
            <td><?=sanitize($s['name'])?></td>
            <td>$<?=number_format($s['price'],2)?></td>
            <td class="<?=$s['change_percent']>=0?'positive':'negative'?>">
              <?=$s['change_percent']>=0?'+':''?><?=number_format($s['change_percent'],2)?>%
            </td>
            <td>
              <button class="btn btn-sm btn-primary" onclick="openTradeModal(<?=$s['id']?>,'<?=sanitize($s['symbol'])?>',<?=$s['price']?>,'buy')">Buy</button>
              <button class="btn btn-sm btn-outline" onclick="openTradeModal(<?=$s['id']?>,'<?=sanitize($s['symbol'])?>',<?=$s['price']?>,'sell')">Sell</button>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="stockModal">
  <div class="modal-content">
    <div class="modal-header"><h3 id="stockModalTitle">Trade Stock</h3><button onclick="closeModal()">&times;</button></div>
    <form method="POST">
      <input type="hidden" name="stock_id" id="stockId">
      <input type="hidden" name="action" id="stockAction">
      <div class="plan-detail-row"><span>Price:</span><span id="stockPrice"></span></div>
      <div class="form-group">
        <label>Amount in USD (min $10)</label>
        <input type="number" name="amount" min="10" step="0.01" required>
      </div>
      <button type="submit" class="btn btn-primary btn-full" id="stockSubmitBtn">Confirm</button>
    </form>
  </div>
</div>
<div class="modal-overlay" id="modalOverlay" onclick="closeModal()"></div>
<script>
function openTradeModal(id,sym,price,action){
  document.getElementById('stockId').value=id;
  document.getElementById('stockAction').value=action;
  document.getElementById('stockModalTitle').textContent=(action==='buy'?'Buy ':'Sell ')+sym;
  document.getElementById('stockPrice').textContent='$'+parseFloat(price).toFixed(2);
  document.getElementById('stockSubmitBtn').textContent=(action==='buy'?'Buy':'Sell')+' '+sym;
  document.getElementById('stockModal').style.display='flex';
  document.getElementById('modalOverlay').style.display='block';
}
function closeModal(){document.getElementById('stockModal').style.display='none';document.getElementById('modalOverlay').style.display='none';}
</script>
<?php include '../includes/footer.php'; ?>
