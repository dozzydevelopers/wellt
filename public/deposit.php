<?php
require_once '../includes/auth.php';
requireLogin();
requirePinSetup();
$user = getCurrentUser();
$pageTitle = 'Deposit';

$success = '';
$error = '';

$settings = [];
$rows = fetchAll("SELECT setting_key,setting_value FROM settings");
foreach ($rows as $r)
  $settings[$r['setting_key']] = $r['setting_value'];

$WALLETS = [
  'Bitcoin (BTC)' => [
    'symbol' => 'BTC',
    'icon' => '₿',
    'color' => '#F7931A',
    'bg' => '#FFF7ED',
    'addr' => $settings['btc_wallet'] ?? 'bc1qf4fwmd848wqzpjs4747tvmkds2kchhmqfftke2',
    'network' => 'Bitcoin Network',
    'warn' => 'Send only BTC to this address. Do not send BEP-20 or other tokens.',
  ],
  'Ethereum (ETH)' => [
    'symbol' => 'ETH',
    'icon' => 'Ξ',
    'color' => '#627EEA',
    'bg' => '#EEF2FF',
    'addr' => $settings['eth_wallet'] ?? '0x7204739349596445BCeB53926EdFb22c21D3BACf',
    'network' => 'ERC20 Network',
    'warn' => 'Send only ETH (ERC20) to this address.',
  ],
  'XRP' => [
    'symbol' => 'XRP',
    'icon' => '◈',
    'color' => '#006097',
    'bg' => '#EFF6FF',
    'addr' => $settings['xrp_wallet'] ?? 'rU3SDRW16ebgjJUeCiXE927BdcY4nwrCWX',
    'network' => 'XRP Ledger',
    'warn' => 'Send only XRP to this address. Incorrect tag or network will result in permanent loss.',
  ],
  'USDT (ERC20)' => [
    'symbol' => 'USDT',
    'icon' => '₮',
    'color' => '#26A17B',
    'bg' => '#F0FDF4',
    'addr' => $settings['usdt_wallet'] ?? '0x7204739349596445BCeB53926EdFb22c21D3BACf',
    'network' => 'ERC20 Network',
    'warn' => 'Send only USDT (ERC20) to this address. USDT-TRC20 is not supported here.',
  ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $amount = floatval($_POST['amount'] ?? 0);
  $method = sanitize($_POST['payment_method'] ?? '');
  $address = sanitize($_POST['payment_address'] ?? '');
  $txPin = $_POST['transaction_pin'] ?? '';

  $minDep = floatval($settings['min_deposit'] ?? 100);
  $maxDep = floatval($settings['max_deposit'] ?? 100000);

  // PIN check
  if (!verifyPin($user['transaction_pin'], $txPin)) {
    $error = 'Invalid transaction PIN. Please try again.';
  } elseif ($amount < $minDep) {
    $error = "Minimum deposit is " . formatMoney($minDep);
  } elseif ($amount > $maxDep) {
    $error = "Maximum deposit is " . formatMoney($maxDep);
  } elseif (!$method || !isset($WALLETS[$method])) {
    $error = "Please select a valid payment method.";
  } else {
    $ref = generateReference();
    $proofPath = null;
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] === 0) {
      $ext = strtolower(pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION));
      $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
      if (in_array($ext, $allowed)) {
        $dir = '../uploads/deposits/';
        if (!is_dir($dir))
          mkdir($dir, 0755, true);
        $filename = $ref . '.' . $ext;
        move_uploaded_file($_FILES['proof']['tmp_name'], $dir . $filename);
        $proofPath = $filename;
      }
    }
    insert(
      "INSERT INTO transactions (user_id,type,amount,description,status,reference,payment_method,payment_address,proof_image) VALUES (?,?,?,?,?,?,?,?,?)",
      [$user['id'], 'deposit', $amount, "Deposit via $method", 'pending', $ref, $method, $address, $proofPath]
    );
    sendNotification($user['id'], 'Deposit Submitted', "Your deposit of " . formatMoney($amount) . " via $method is under review.", 'info');
    $depositTxn = fetchOne("SELECT * FROM transactions WHERE reference=?", [$ref]);
    if ($depositTxn)
      sendAdminNewDepositEmail($user, $depositTxn);
    $success = "Deposit submitted successfully! Reference: <strong>$ref</strong>. Our team will review within 1–24 hours.";
  }
}

include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">Deposit Funds</h2>

      <?php if ($success): ?>
        <div class="alert alert-success" style="animation:slideDown .4s both"><?= $success ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-error" style="animation:slideDown .4s both"><?= sanitize($error) ?></div>
      <?php endif; ?>

      <div class="card" style="animation:fadeInUp .45s .1s both">
        <form method="POST" enctype="multipart/form-data" id="depositForm">
          <input type="hidden" name="transaction_pin" id="depositPinField">

          <!-- Amount -->
          <div class="form-group">
            <label>Amount (USD) <span class="req">*</span></label>
            <div class="input-wrap"><span class="input-icon">$</span>
              <input type="number" name="amount" step="0.01" min="<?= $settings['min_deposit'] ?? 100 ?>"
                max="<?= $settings['max_deposit'] ?? 100000 ?>" placeholder="Enter amount" required>
            </div>
            <small>Min: <?= formatMoney($settings['min_deposit'] ?? 100) ?> &nbsp;|&nbsp; Max:
              <?= formatMoney($settings['max_deposit'] ?? 100000) ?></small>
          </div>

          <!-- Payment Method Selector -->
          <div class="form-group">
            <label>Select Payment Method <span class="req">*</span></label>
            <div class="crypto-methods-grid">
              <?php foreach ($WALLETS as $name => $w): ?>
                <button type="button" class="crypto-card" id="cc_<?= $w['symbol'] ?>"
                  onclick='selectCrypto(<?= json_encode($name) ?>, <?= json_encode($w['addr']) ?>, <?= json_encode($w['symbol']) ?>, <?= json_encode($w['color']) ?>, <?= json_encode($w['network']) ?>, <?= json_encode($w['warn']) ?>)'
                  style="--cc:#<?= ltrim($w['color'], '#') ?>">
                  <div class="crypto-icon-wrap" style="background:<?= $w['bg'] ?>;color:<?= $w['color'] ?>">
                    <?= $w['icon'] ?>
                  </div>
                  <span class="crypto-card-name"><?= $w['symbol'] ?></span>
                  <span class="crypto-card-full"><?= $name ?></span>
                </button>
              <?php endforeach; ?>
            </div>
            <input type="hidden" name="payment_method" id="selectedMethod" required>
          </div>

          <!-- Wallet Detail Box (shown after selection) -->
          <div id="walletDetailBox" style="display:none" class="wallet-detail-box">

            <!-- Method Header -->
            <div class="wdb-header">
              <div class="wdb-icon" id="wdbIcon"></div>
              <div>
                <div class="wdb-method-name" id="wdbMethodName">Bitcoin (BTC)</div>
                <div class="wdb-network" id="wdbNetwork">Bitcoin Network</div>
              </div>
            </div>

            <!-- 7-Minute Countdown Timer -->
            <div class="timer-block" id="timerBlock">
              <div class="timer-top">
                <span class="timer-label">⏱ Payment window expires in:</span>
                <span class="timer-display" id="timerDisplay">07:00</span>
              </div>
              <div class="timer-bar-track">
                <div class="timer-bar-fill" id="timerBarFill"></div>
              </div>
            </div>

            <!-- QR Code + Address -->
            <div class="wdb-address-section">
              <label>Send to this wallet address:</label>
              <div class="qr-and-addr">
                <img id="qrImg" src="" alt="QR Code" class="qr-code-img">
                <div class="addr-copy-wrap">
                  <div class="wallet-addr-row">
                    <input type="text" id="wdbAddr" readonly class="wallet-addr-input">
                    <button type="button" onclick="copyWallet()" class="btn-copy-addr">📋 Copy</button>
                  </div>
                  <p class="copied-msg" id="copiedMsg" style="display:none;color:#22C55E;font-size:12px">✓ Address
                    copied!</p>
                </div>
              </div>
            </div>

            <!-- Warning -->
            <div class="wallet-warning-box" id="wdbWarning"></div>

            <!-- Timer expired notice -->
            <div id="timerExpiredBox" style="display:none" class="timer-expired-notice">
              ⚠ Payment window expired. <a href="javascript:location.reload()">Click here to refresh</a> and start
              again.
            </div>
          </div>

          <!-- Sending Address + Proof -->
          <div id="afterSelectFields" style="display:none">
            <div class="form-group">
              <label>Your Sending Wallet Address <span class="req">*</span></label>
              <div class="input-wrap"><span class="input-icon">💳</span>
                <input type="text" name="payment_address" placeholder="The address you sent from" required>
              </div>
            </div>
            <div class="form-group">
              <label>Payment Proof (screenshot/receipt)</label>
              <input type="file" name="proof" accept="image/*,.pdf">
              <small>Upload a screenshot of your transaction confirmation (optional but recommended)</small>
            </div>
            <button type="button" class="btn btn-primary" id="submitDepBtn"
              onclick="showPinModal('depositForm','transaction_pin')">
              🔐 Confirm Deposit
            </button>
          </div>

        </form>
      </div>

      <!-- Deposit History -->
      <div class="card mt-4" style="animation:fadeInUp .5s .2s both">
        <h3>Deposit History</h3>
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
            $deps = fetchAll("SELECT * FROM transactions WHERE user_id=? AND type='deposit' ORDER BY created_at DESC LIMIT 20", [$user['id']]);
            foreach ($deps as $d): ?>
              <tr>
                <td><?= date('M j, Y', strtotime($d['created_at'])) ?></td>
                <td class="positive"><?= formatMoney($d['amount']) ?></td>
                <td><?= sanitize($d['payment_method'] ?? '-') ?></td>
                <td><small><?= sanitize($d['reference']) ?></small></td>
                <td><span class="badge badge-<?= $d['status'] ?>"><?= ucfirst($d['status']) ?></span></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($deps)): ?>
              <tr>
                <td colspan="5" class="empty-row">No deposits yet</td>
              </tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
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
    padding: 20px;
    margin-bottom: 20px;
    animation: scaleIn .3s cubic-bezier(.22, 1, .36, 1);
  }

  .wdb-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
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

  .timer-block {
    background: #0F172A;
    border-radius: 12px;
    padding: 14px 16px;
    margin-bottom: 16px;
  }

  .timer-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
  }

  .timer-label {
    color: rgba(255, 255, 255, .6);
    font-size: 12px;
  }

  .timer-display {
    color: #F97316;
    font-size: 22px;
    font-weight: 900;
    font-variant-numeric: tabular-nums;
    letter-spacing: 2px;
  }

  .timer-display.warn {
    color: #EF4444;
    animation: pulse 1s infinite;
  }

  .timer-bar-track {
    height: 4px;
    background: rgba(255, 255, 255, .12);
    border-radius: 2px;
  }

  .timer-bar-fill {
    height: 4px;
    background: linear-gradient(90deg, #F97316, #FCD34D);
    border-radius: 2px;
    transition: width .95s linear;
  }

  .wdb-address-section label {
    font-size: 13px;
    font-weight: 600;
    color: #0F172A;
    display: block;
    margin-bottom: 10px;
  }

  .qr-and-addr {
    display: flex;
    gap: 16px;
    align-items: flex-start;
  }

  .qr-code-img {
    width: 120px;
    height: 120px;
    border-radius: 10px;
    border: 3px solid #E2E8F0;
    background: #fff;
  }

  .addr-copy-wrap {
    flex: 1;
  }

  .wallet-addr-row {
    display: flex;
    gap: 8px;
    align-items: center;
  }

  .wallet-addr-input {
    flex: 1;
    background: #fff;
    border: 1.5px solid #CBD5E1;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 12px;
    font-family: monospace;
    color: #0F172A;
  }

  .btn-copy-addr {
    background: #F97316;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    white-space: nowrap;
    transition: filter .15s;
  }

  .btn-copy-addr:hover {
    filter: brightness(1.1);
  }

  .wallet-warning-box {
    background: #FEF3C7;
    border: 1px solid #F59E0B;
    border-radius: 10px;
    padding: 12px 14px;
    font-size: 13px;
    color: #92400E;
    margin-top: 12px;
  }

  .wallet-warning-box::before {
    content: '⚠ ';
  }

  .timer-expired-notice {
    background: #FEF2F2;
    border: 1px solid #FECACA;
    border-radius: 10px;
    padding: 14px;
    text-align: center;
    font-size: 14px;
    color: #DC2626;
    margin-top: 12px;
  }

  .timer-expired-notice a {
    color: #DC2626;
    font-weight: 700;
  }

  @media(max-width:560px) {
    .qr-and-addr {
      flex-direction: column;
    }

    .qr-code-img {
      width: 100%;
      height: 140px;
    }
  }
</style>

<script>
  var TIMER_DURATION = 420; // 7 minutes
  var timerInterval = null;
  var timerSeconds = TIMER_DURATION;

  function selectCrypto(name, addr, symbol, color, network, warning) {
    // Update hidden field
    document.getElementById('selectedMethod').value = name;

    // Highlight selected card
    document.querySelectorAll('.crypto-card').forEach(c => c.classList.remove('active'));
    document.getElementById('cc_' + symbol).classList.add('active');

    // Show wallet detail
    var box = document.getElementById('walletDetailBox');
    box.style.display = 'block';

    // Update header
    var icon = document.getElementById('wdbIcon');
    icon.style.background = hexAlpha(color, 0.12);
    icon.style.color = color;
    icon.textContent = { 'BTC': '₿', 'ETH': 'Ξ', 'XRP': '◈', 'USDT': '₮' }[symbol] || symbol;
    document.getElementById('wdbMethodName').textContent = name;
    document.getElementById('wdbNetwork').textContent = network;

    // Update address + QR
    document.getElementById('wdbAddr').value = addr;
    document.getElementById('qrImg').src = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + encodeURIComponent(addr);

    // Update warning
    document.getElementById('wdbWarning').textContent = warning;

    // Show after-select fields
    document.getElementById('afterSelectFields').style.display = 'block';
    document.getElementById('timerExpiredBox').style.display = 'none';
    document.getElementById('submitDepBtn').disabled = false;

    // Reset and start timer
    clearInterval(timerInterval);
    timerSeconds = TIMER_DURATION;
    _tickTimer();
    timerInterval = setInterval(_tickTimer, 1000);
  }

  function _tickTimer() {
    if (timerSeconds < 0) timerSeconds = 0;
    var m = Math.floor(timerSeconds / 60);
    var s = timerSeconds % 60;
    var disp = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    var el = document.getElementById('timerDisplay');
    el.textContent = disp;
    el.className = 'timer-display' + (timerSeconds <= 60 ? ' warn' : '');

    var pct = (timerSeconds / TIMER_DURATION) * 100;
    document.getElementById('timerBarFill').style.width = pct + '%';

    if (timerSeconds <= 0) {
      clearInterval(timerInterval);
      document.getElementById('timerDisplay').textContent = 'EXPIRED';
      document.getElementById('timerExpiredBox').style.display = 'block';
      document.getElementById('submitDepBtn').disabled = true;
    }
    timerSeconds--;
  }

  function copyWallet() {
    var el = document.getElementById('wdbAddr');
    el.select();
    navigator.clipboard ? navigator.clipboard.writeText(el.value) : document.execCommand('copy');
    var msg = document.getElementById('copiedMsg');
    msg.style.display = 'block';
    setTimeout(function () { msg.style.display = 'none'; }, 2500);
  }

  function hexAlpha(hex, alpha) {
    var r = 0, g = 0, b = 0;
    if (hex.length === 7) { r = parseInt(hex.slice(1, 3), 16); g = parseInt(hex.slice(3, 5), 16); b = parseInt(hex.slice(5, 7), 16); }
    return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
  }
</script>
<?php include '../includes/footer.php'; ?>