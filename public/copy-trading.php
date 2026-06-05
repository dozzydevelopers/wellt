<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Copy Trading';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $traderId = intval($_POST['trader_id'] ?? 0);
    $amount   = floatval($_POST['amount'] ?? 0);
    $trader = fetchOne("SELECT * FROM copy_traders WHERE id=? AND status='active'", [$traderId]);
    if (!$trader) { $error = "Invalid trader."; }
    elseif ($amount < 50) { $error = "Minimum copy amount is $50."; }
    elseif ($amount > $user['balance']) { $error = "Insufficient balance."; }
    else {
        query("UPDATE users SET balance = balance - ? WHERE id = ?", [$amount, $user['id']]);
        insert("INSERT INTO active_copies (user_id, trader_id, amount) VALUES (?,?,?)", [$user['id'], $traderId, $amount]);
        query("UPDATE copy_traders SET followers = followers + 1 WHERE id = ?", [$traderId]);
        sendNotification($user['id'], 'Copy Trade Started', "You are now copying {$trader['name']} with ".formatMoney($amount), 'success');
        $user = getCurrentUser();
        $success = "You are now copying {$trader['name']}!";
    }
}

$traders = fetchAll("SELECT * FROM copy_traders WHERE status='active' ORDER BY roi DESC");
include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">Copy Trading</h2>
      <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
      <p>Balance: <strong><?= formatMoney($user['balance']) ?></strong></p>

      <div class="traders-grid">
        <?php foreach ($traders as $t): ?>
        <div class="trader-card">
          <div class="trader-info">
            <div class="trader-avatar big"><?= strtoupper(substr($t['name'],0,1)) ?></div>
            <div>
              <div class="trader-name"><?= sanitize($t['name']) ?></div>
              <div class="trader-followers"><?= number_format($t['followers']) ?> followers</div>
            </div>
          </div>
          <?php if ($t['bio']): ?><p class="trader-bio"><?= sanitize($t['bio']) ?></p><?php endif; ?>
          <div class="trader-stats">
            <div class="trader-stat"><span>ROI</span><span class="positive"><?= $t['roi'] ?>%</span></div>
            <div class="trader-stat"><span>Profit</span><span class="positive">+<?= $t['profit_percent'] ?>%</span></div>
            <div class="trader-stat"><span>Fee</span><span><?= $t['fee_percent'] ?>%</span></div>
            <div class="trader-stat"><span>Period</span><span><?= sanitize($t['period']) ?></span></div>
          </div>
          <button class="btn btn-primary" onclick="openCopyModal(<?= $t['id'] ?>, '<?= addslashes($t['name']) ?>', <?= $t['fee_percent'] ?>)">Copy Trader</button>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="copyModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Copy <span id="copyTraderName"></span></h3>
      <button onclick="closeModal()">&times;</button>
    </div>
    <form method="POST">
      <input type="hidden" name="trader_id" id="copyTraderId">
      <div class="plan-detail-row"><span>Fee:</span><span id="copyFee"></span></div>
      <div class="plan-detail-row"><span>Balance:</span><span><?= formatMoney($user['balance']) ?></span></div>
      <div class="form-group">
        <label>Amount to Copy (min $50)</label>
        <input type="number" name="amount" min="50" step="0.01" required>
      </div>
      <button type="submit" class="btn btn-primary btn-full">Start Copying</button>
    </form>
  </div>
</div>
<div class="modal-overlay" id="modalOverlay" onclick="closeModal()"></div>
<script>
function openCopyModal(id, name, fee) {
  document.getElementById('copyTraderId').value = id;
  document.getElementById('copyTraderName').textContent = name;
  document.getElementById('copyFee').textContent = fee + '%';
  document.getElementById('copyModal').style.display = 'flex';
  document.getElementById('modalOverlay').style.display = 'block';
}
function closeModal() {
  document.getElementById('copyModal').style.display = 'none';
  document.getElementById('modalOverlay').style.display = 'none';
}
</script>
<?php include '../includes/footer.php'; ?>
