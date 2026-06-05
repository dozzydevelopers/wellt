<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Binary Trade';
$success=''; $error='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $asset=sanitize($_POST['asset']??'BTC/USD'); $dir=sanitize($_POST['direction']??'up');
    $amount=floatval($_POST['amount']??0); $duration=intval($_POST['duration']??60);
    if ($amount<10) { $error='Minimum trade amount is $10.'; }
    elseif ($amount>$user['balance']) { $error='Insufficient balance.'; }
    elseif (!in_array($dir,['up','down'])) { $error='Invalid direction.'; }
    else {
        // Simulate entry price (in production, fetch from exchange API)
        $prices=['BTC/USD'=>67450,'ETH/USD'=>3512,'XRP/USD'=>0.58,'LTC/USD'=>85,'ADA/USD'=>0.45,'SOL/USD'=>142,'DOGE/USD'=>0.09];
        $entryPrice=$prices[$asset]??1000;
        $expiresAt=date('Y-m-d H:i:s',time()+$duration);
        query("UPDATE users SET balance=balance-? WHERE id=?",[$amount,$user['id']]);
        $tradeId=insert("INSERT INTO binary_trades (user_id,asset,direction,amount,entry_price,payout_percent,expires_at) VALUES (?,?,?,?,?,?,?)",
            [$user['id'],$asset,$dir,$amount,$entryPrice,85,$expiresAt]);
        $user=getCurrentUser();
        // Simulate result after duration (randomly win/lose for demo; use actual price comparison in prod)
        $success="Trade placed! Asset: $asset, Direction: ".strtoupper($dir).", Amount: ".formatMoney($amount).". Trade expires at $expiresAt.";
    }
}
$recentTrades=fetchAll("SELECT * FROM binary_trades WHERE user_id=? ORDER BY created_at DESC LIMIT 10",[$user['id']]);
include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">Binary Trade</h2>
      <?php if ($success): ?><div class="alert alert-success"><?=$success?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-error"><?=$error?></div><?php endif; ?>
      <p>Balance: <strong><?=formatMoney($user['balance'])?></strong></p>

      <div class="card mb-4">
        <h3>Place a Trade</h3>
        <form method="POST">
          <div class="form-row">
            <div class="form-group"><label>Asset</label>
              <select name="asset">
                <?php foreach(['BTC/USD','ETH/USD','XRP/USD','LTC/USD','ADA/USD','SOL/USD','DOGE/USD'] as $a): ?>
                <option value="<?=$a?>"><?=$a?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group"><label>Duration</label>
              <select name="duration">
                <option value="60">1 Minute</option>
                <option value="300">5 Minutes</option>
                <option value="900">15 Minutes</option>
                <option value="3600">1 Hour</option>
              </select>
            </div>
          </div>
          <div class="form-group"><label>Amount (min $10)</label>
            <div class="input-wrap"><span class="input-icon">$</span>
            <input type="number" name="amount" min="10" step="0.01" required></div>
          </div>
          <div class="trade-btns">
            <button type="submit" name="direction" value="up" class="btn btn-trade-up">&#9650; UP (85% payout)</button>
            <button type="submit" name="direction" value="down" class="btn btn-trade-down">&#9660; DOWN (85% payout)</button>
          </div>
        </form>
      </div>

      <div class="table-card">
        <table class="data-table">
          <thead><tr><th>Asset</th><th>Direction</th><th>Amount</th><th>Entry Price</th><th>Payout</th><th>Expires</th><th>Result</th></tr></thead>
          <tbody>
          <?php foreach($recentTrades as $t): ?>
          <tr>
            <td><?=sanitize($t['asset'])?></td>
            <td><span class="<?=$t['direction']==='up'?'positive':'negative'?>"><?=strtoupper($t['direction'])?></span></td>
            <td><?=formatMoney($t['amount'])?></td>
            <td>$<?=number_format($t['entry_price'],2)?></td>
            <td class="positive">$<?=number_format($t['amount']*0.85,2)?></td>
            <td><?=date('M j, H:i',strtotime($t['expires_at']))?></td>
            <td><span class="badge badge-<?=$t['result']==='win'?'approved':($t['result']==='lose'?'rejected':'pending')?>"><?=ucfirst($t['result'])?></span></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($recentTrades)): ?><tr><td colspan="7" class="empty-row">No trades yet</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<style>
.trade-btns{display:flex;gap:12px;margin-top:12px}
.btn-trade-up{flex:1;background:#22C55E;color:#fff;border:none;padding:14px;border-radius:8px;font-size:16px;font-weight:700;cursor:pointer}
.btn-trade-down{flex:1;background:#EF4444;color:#fff;border:none;padding:14px;border-radius:8px;font-size:16px;font-weight:700;cursor:pointer}
</style>
<?php include '../includes/footer.php'; ?>
