<?php
require_once '../includes/auth.php';
requireLogin();
requirePinSetup();
$user = getCurrentUser();
$pageTitle = 'Withdraw';

$success = '';
$error = '';

$settings = [];
$rows = fetchAll("SELECT setting_key, setting_value FROM settings");
foreach ($rows as $r)
  $settings[$r['setting_key']] = $r['setting_value'];

$WITHDRAW_METHODS = [
  'Bitcoin (BTC)' => [
    'symbol' => 'BTC',
    'icon' => '₿',
    'color' => '#F7931A',
    'bg' => '#FFF7ED',
    'hint' => 'Use your Bitcoin wallet address for the payout. Double-check the network before submitting.',
    'placeholder' => 'Enter your BTC wallet address',
  ],
  'Ethereum (ETH)' => [
    'symbol' => 'ETH',
    'icon' => 'Ξ',
    'color' => '#627EEA',
    'bg' => '#EEF2FF',
    'hint' => 'Use your Ethereum (ERC20) wallet address only. Do not use TRC20 or other networks.',
    'placeholder' => 'Enter your ETH wallet address',
  ],
  'USDT (TRC20)' => [
    'symbol' => 'USDT',
    'icon' => '₮',
    'color' => '#26A17B',
    'bg' => '#F0FDF4',
    'hint' => 'Use your USDT TRC20 address for fast payouts. Make sure the network matches the method selected.',
    'placeholder' => 'Enter your USDT (TRC20) wallet address',
  ],
  'Bank Transfer' => [
    'symbol' => 'BANK',
    'icon' => '🏦',
    'color' => '#0F766E',
    'bg' => '#ECFEFF',
    'hint' => 'Use your bank account details exactly as registered. This method may take longer to process.',
    'placeholder' => 'Enter your bank account / IBAN / routing details',
  ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $amount = floatval($_POST['amount'] ?? 0);
  $method = sanitize($_POST['payment_method'] ?? '');
  $address = sanitize($_POST['wallet_address'] ?? '');

  $minW = floatval($settings['min_withdrawal'] ?? 50);
  $maxW = floatval($settings['max_withdrawal'] ?? 50000);
  $fee = floatval($settings['withdrawal_fee_percent'] ?? 2) / 100;

  // PIN verification
  $txPin = $_POST['transaction_pin'] ?? '';
  if (!verifyPin($user['transaction_pin'], $txPin)) {
    $error = 'Invalid transaction PIN. Please try again.';
  } elseif ($amount < $minW) {
    $error = "Minimum withdrawal is " . formatMoney($minW);
  } elseif ($amount > $maxW) {
    $error = "Maximum withdrawal is " . formatMoney($maxW);
  } elseif ($amount > $user['balance']) {
    $error = "Insufficient balance. Your available balance is " . formatMoney($user['balance']);
  } elseif (!$address) {
    $error = "Please enter your wallet address.";
  } else {
    $feeAmt = $amount * $fee;
    $netAmt = $amount - $feeAmt;
    $ref = generateReference();
    // Deduct from balance immediately (pending)
    query("UPDATE users SET balance = balance - ? WHERE id = ?", [$amount, $user['id']]);
    insert(
      "INSERT INTO transactions (user_id, type, amount, description, status, reference, payment_method, payment_address) VALUES (?,?,?,?,?,?,?,?)",
      [$user['id'], 'withdrawal', $amount, "Withdrawal via $method (Fee: " . formatMoney($feeAmt) . ", Net: " . formatMoney($netAmt) . ")", 'pending', $ref, $method, $address]
    );
    sendNotification($user['id'], 'Withdrawal Request', "Your withdrawal of " . formatMoney($amount) . " is under review. Net payout: " . formatMoney($netAmt), 'info');
    $withdrawTxn = fetchOne("SELECT * FROM transactions WHERE reference=?", [$ref]);
    if ($withdrawTxn)
      sendAdminNewWithdrawalEmail($user, $withdrawTxn);
    $user = getCurrentUser(); // refresh
    $success = "Withdrawal request submitted. Net amount: " . formatMoney($netAmt) . " (fee: " . formatMoney($feeAmt) . "). Reference: $ref";
  }
}

include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">Withdraw Funds</h2>
      <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div><?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div><?php endif; ?>

      <div class="balance-notice">Available Balance: <strong><?= formatMoney($user['balance']) ?></strong></div>

      <div class="card">
        <form method="POST" id="withdrawForm">
          <div class="form-group">
            <label>Amount (USD) *</label>
            <div class="input-wrap"><span class="input-icon">$</span>
              <input type="number" name="amount" step="0.01" min="<?= $settings['min_withdrawal'] ?? 50 ?>"
                placeholder="Enter amount" required>
            </div>
            <small>Min: <?= formatMoney($settings['min_withdrawal'] ?? 50) ?> | Fee:
              <?= $settings['withdrawal_fee_percent'] ?? 2 ?>%</small>
          </div>
          <div class="form-group">
            <label>Payment Method *</label>
            <div class="crypto-methods-grid">
              <?php foreach ($WITHDRAW_METHODS as $name => $m): ?>
                <button type="button" class="crypto-card" id="wd_<?= $m['symbol'] ?>"
                  onclick='selectWithdrawMethod(<?= json_encode($name) ?>, <?= json_encode($m['hint']) ?>, <?= json_encode($m['placeholder']) ?>, <?= json_encode($m['color']) ?>)'
                  style="--cc:#<?= ltrim($m['color'], '#') ?>">
                  <div class="crypto-icon-wrap" style="background:<?= $m['bg'] ?>;color:<?= $m['color'] ?>">
                    <?= $m['icon'] ?></div>
                  <span class="crypto-card-name"><?= $m['symbol'] ?></span>
                  <span class="crypto-card-full"><?= $name ?></span>
                </button>
              <?php endforeach; ?>
            </div>
            <input type="hidden" name="payment_method" id="selectedWithdrawMethod" required>
          </div>

          <div id="withdrawDetailBox" style="display:none" class="wallet-detail-box">
            <div class="wdb-header">
              <div class="wdb-icon" id="wdIcon"></div>
              <div>
                <div class="wdb-method-name" id="wdMethodName">Withdrawal Method</div>
                <div class="wdb-network" id="wdMethodHint">Choose a payout method to see the instructions.</div>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Wallet / Bank Address *</label>
            <input type="text" name="wallet_address" id="walletAddressInput" placeholder="Select a payment method first"
              required>
          </div>
          <input type="hidden" name="transaction_pin" value="">
          <button type="button" class="btn btn-primary" onclick="showPinModal('withdrawForm')">🔐 Submit
            Withdrawal</button>
        </form>
      </div>

      <?php include '../includes/pin-modal.php'; ?>
      <style>
        .crypto-methods-grid {
          display: grid;
          grid-template-columns: repeat(4, 1fr);
          gap: 12px;
          margin-top: 8px;
        }

        @media(max-width:600px) {
          .crypto-methods-grid {
            grid-template-columns: repeat(2, 1fr);
          }
        }

        .crypto-card {
          border: 2px solid #E2E8F0;
          border-radius: 14px;
          padding: 16px 8px;
          background: #fff;
          cursor: pointer;
          display: flex;
          flex-direction: column;
          align-items: center;
          gap: 8px;
          transition: all .2s;
          font-family: inherit;
        }

        .crypto-card:hover {
          border-color: var(--primary);
          transform: translateY(-3px);
          box-shadow: 0 6px 20px rgba(0, 0, 0, .08);
        }

        .crypto-card.active {
          border-color: var(--cc, var(--primary));
          background: rgba(var(--cc-rgb, 249, 115, 22), .06);
          box-shadow: 0 0 0 3px rgba(249, 115, 22, .15);
        }

        .crypto-icon-wrap {
          width: 46px;
          height: 46px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 22px;
          font-weight: 900;
        }

        .crypto-card-name {
          font-size: 14px;
          font-weight: 700;
          color: #0F172A;
        }

        .crypto-card-full {
          font-size: 11px;
          color: #94A3B8;
        }

        .wallet-detail-box {
          background: #F8FAFC;
          border: 1px solid #E2E8F0;
          border-radius: 16px;
          padding: 18px;
          margin-top: 10px;
          animation: scaleIn .3s cubic-bezier(.22, 1, .36, 1);
        }

        .wdb-header {
          display: flex;
          align-items: center;
          gap: 14px;
          margin-bottom: 6px;
        }

        .wdb-icon {
          width: 44px;
          height: 44px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 22px;
          font-weight: 900;
        }

        .wdb-method-name {
          font-weight: 800;
          font-size: 16px;
          color: #0F172A;
        }

        .wdb-network {
          font-size: 12px;
          color: #64748B;
        }
      </style>
      <script>
        function selectWithdrawMethod(name, hint, placeholder, color) {
          document.getElementById('selectedWithdrawMethod').value = name;
          document.querySelectorAll('.crypto-card').forEach(function (card) { card.classList.remove('active'); });
          var card = document.getElementById('wd_' + (name === 'USDT (TRC20)' ? 'USDT' : name === 'Bitcoin (BTC)' ? 'BTC' : name === 'Ethereum (ETH)' ? 'ETH' : 'BANK'));
          if (card) card.classList.add('active');
          document.getElementById('withdrawDetailBox').style.display = 'block';
          document.getElementById('wdIcon').style.background = hexAlpha(color, 0.12);
          document.getElementById('wdIcon').style.color = color;
          document.getElementById('wdIcon').textContent = name === 'Bitcoin (BTC)' ? '₿' : name === 'Ethereum (ETH)' ? 'Ξ' : name === 'USDT (TRC20)' ? '₮' : '🏦';
          document.getElementById('wdMethodName').textContent = name;
          document.getElementById('wdMethodHint').textContent = hint;
          document.getElementById('walletAddressInput').placeholder = placeholder;
          document.getElementById('walletAddressInput').focus();
        }
        function hexAlpha(hex, alpha) {
          var r = 0, g = 0, b = 0;
          if (hex.length === 7) {
            r = parseInt(hex.slice(1, 3), 16);
            g = parseInt(hex.slice(3, 5), 16);
            b = parseInt(hex.slice(5, 7), 16);
          }
          return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
        }
      </script>

      <div class="card mt-4">
        <h3>Withdrawal History</h3>
        <table class="data-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Amount</th>
              <th>Method</th>
              <th>Reference</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $ws = fetchAll("SELECT * FROM transactions WHERE user_id=? AND type='withdrawal' ORDER BY created_at DESC LIMIT 20", [$user['id']]);
            foreach ($ws as $w):
              ?>
              <tr>
                <td><?= date('M j, Y', strtotime($w['created_at'])) ?></td>
                <td class="negative"><?= formatMoney($w['amount']) ?></td>
                <td><?= sanitize($w['payment_method'] ?? '-') ?></td>
                <td><small><?= sanitize($w['reference']) ?></small></td>
                <td><span class="badge badge-<?= $w['status'] ?>"><?= ucfirst($w['status']) ?></span></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($ws)): ?>
              <tr>
                <td colspan="5" class="empty-row">No withdrawals yet</td>
              </tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>