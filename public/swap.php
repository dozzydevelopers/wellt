<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Swap Crypto';
$success=''; $error='';

$rates=['BTC'=>67450,'ETH'=>3512,'USDT'=>1,'BNB'=>385,'XRP'=>0.58,'SOL'=>142,'ADA'=>0.45,'DOGE'=>0.09,'LTC'=>85];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $from=sanitize($_POST['from_currency']??''); $to=sanitize($_POST['to_currency']??'');
    $amount=floatval($_POST['amount']??0);
    if (!isset($rates[$from])||!isset($rates[$to])) { $error='Invalid currency.'; }
    elseif ($from===$to) { $error='Cannot swap same currency.'; }
    elseif ($amount<=0) { $error='Invalid amount.'; }
    else {
        // Calculate in USD then convert to target
        $usdVal=$amount*$rates[$from];
        $toAmount=$usdVal/$rates[$to];
        $fee=$toAmount*0.005; // 0.5% fee
        $net=$toAmount-$fee;
        insert("INSERT INTO crypto_swaps (user_id,from_currency,to_currency,from_amount,to_amount,rate,fee) VALUES (?,?,?,?,?,?,?)",
            [$user['id'],$from,$to,$amount,$net,$rates[$to],$fee]);
        sendNotification($user['id'],'Swap Completed',"Swapped $amount $from to ".number_format($net,6)." $to",'success');
        $success="Swap successful! $amount $from → ".number_format($net,6)." $to (fee: ".number_format($fee,6)." $to)";
    }
}

$history=fetchAll("SELECT * FROM crypto_swaps WHERE user_id=? ORDER BY created_at DESC LIMIT 20",[$user['id']]);
include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">Swap Crypto</h2>
      <?php if ($success): ?><div class="alert alert-success"><?=$success?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-error"><?=$error?></div><?php endif; ?>

      <div class="card mb-4" style="max-width:480px">
        <h3>Swap</h3>
        <form method="POST">
          <div class="form-group"><label>From</label>
            <select name="from_currency" onchange="updateRate()" id="fromCur">
              <?php foreach(array_keys($rates) as $c): ?><option value="<?=$c?>"><?=$c?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="form-group"><label>Amount</label>
            <input type="number" name="amount" step="0.00000001" min="0" required id="swapAmt" oninput="updateRate()">
          </div>
          <div class="swap-arrow">&#8645;</div>
          <div class="form-group"><label>To</label>
            <select name="to_currency" onchange="updateRate()" id="toCur">
              <?php foreach(array_keys($rates) as $c): ?><option value="<?=$c?>" <?=$c==='USDT'?'selected':''?>><?=$c?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="swap-estimate" id="swapEst">Enter amount to see estimate</div>
          <button type="submit" class="btn btn-primary btn-full mt-4">Swap Now</button>
        </form>
      </div>

      <div class="table-card">
        <table class="data-table">
          <thead><tr><th>Date</th><th>From</th><th>To</th><th>From Amount</th><th>To Amount</th><th>Status</th></tr></thead>
          <tbody>
          <?php foreach($history as $h): ?>
          <tr>
            <td><?=date('M j, Y',strtotime($h['created_at']))?></td>
            <td><?=sanitize($h['from_currency'])?></td><td><?=sanitize($h['to_currency'])?></td>
            <td><?=number_format($h['from_amount'],8)?></td>
            <td class="positive"><?=number_format($h['to_amount'],8)?></td>
            <td><span class="badge badge-approved">Completed</span></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($history)): ?><tr><td colspan="6" class="empty-row">No swaps yet</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<style>
.swap-arrow{text-align:center;font-size:24px;margin:4px 0;color:#94A3B8}
.swap-estimate{background:#F1F5F9;padding:10px 14px;border-radius:8px;font-size:13px;color:#475569}
.mt-4{margin-top:16px}
</style>
<script>
var rates={BTC:67450,ETH:3512,USDT:1,BNB:385,XRP:0.58,SOL:142,ADA:0.45,DOGE:0.09,LTC:85};
function updateRate(){
  var from=document.getElementById('fromCur').value;
  var to=document.getElementById('toCur').value;
  var amt=parseFloat(document.getElementById('swapAmt').value)||0;
  if(amt>0&&from!==to){
    var usd=amt*rates[from];var toAmt=usd/rates[to];var fee=toAmt*0.005;
    document.getElementById('swapEst').textContent='≈ '+(toAmt-fee).toFixed(8)+' '+to+' (0.5% fee)';
  }
}
</script>
<?php include '../includes/footer.php'; ?>
