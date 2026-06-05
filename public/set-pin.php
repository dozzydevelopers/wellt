<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = empty($user['transaction_pin']) ? 'Set Transaction PIN' : 'Change Transaction PIN';
$isChange = !empty($user['transaction_pin']);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $pin = $_POST['pin'] ?? '';
  $pin2 = $_POST['pin2'] ?? '';

  // Verify current PIN if changing
  if ($isChange) {
    $current = $_POST['current_pin'] ?? '';
    if (!verifyPin($user['transaction_pin'], $current)) {
      $error = 'Current PIN is incorrect.';
    }
  }

  if (!$error) {
    if (!preg_match('/^\d{4}$/', $pin)) {
      $error = 'PIN must be exactly 4 digits.';
    } elseif ($pin !== $pin2) {
      $error = 'PINs do not match. Please try again.';
    } else {
      query("UPDATE users SET transaction_pin=?, pin_set=1 WHERE id=?", [hashPin($pin), $user['id']]);
      $dest = $_SESSION['pin_redirect'] ?? null;
      unset($_SESSION['pin_redirect']);
      if (!$isChange) {
        $_SESSION['bio_redirect'] = $dest ?? '/public/dashboard.php';
        redirect(SITE_BASE . '/biometric-setup.php');
      }
      redirect($dest ?? '/public/dashboard.php');
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?> - Welthflow</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/style.css') ?>">
  <link rel="stylesheet" href="<?= assetUrl('assets/css/animations.css') ?>">
  <style>
    body {
      background: #0F172A;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Inter', sans-serif;
    }

    .pin-setup-wrap {
      width: 420px;
      max-width: 96vw;
    }

    .pin-setup-card {
      background: #fff;
      border-radius: 24px;
      padding: 40px 36px;
      box-shadow: 0 24px 80px rgba(0, 0, 0, .4);
    }

    .pin-logo {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 28px;
      justify-content: center;
    }

    .pin-logo-bar {
      width: 4px;
      height: 32px;
      background: #F97316;
      border-radius: 2px;
    }

    .pin-logo-text {
      font-size: 20px;
      font-weight: 900;
      letter-spacing: 3px;
      color: #0F172A;
    }

    .pin-icon-big {
      font-size: 52px;
      text-align: center;
      display: block;
      margin-bottom: 12px;
    }

    .pin-setup-title {
      font-size: 22px;
      font-weight: 800;
      color: #0F172A;
      text-align: center;
      margin: 0 0 6px;
    }

    .pin-setup-sub {
      color: #64748B;
      font-size: 14px;
      text-align: center;
      margin: 0 0 28px;
    }

    .pin-dots-row {
      display: flex;
      gap: 18px;
      justify-content: center;
      margin-bottom: 6px;
    }

    .pd {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      border: 2px solid #CBD5E1;
      background: #F8FAFC;
      transition: all .2s;
    }

    .pd.filled {
      background: #F97316;
      border-color: #F97316;
      transform: scale(1.15);
    }

    .pd.confirm-mode {
      border-color: #8B5CF6;
    }

    .pd.confirm-mode.filled {
      background: #8B5CF6;
      border-color: #8B5CF6;
    }

    .pin-step-label {
      text-align: center;
      font-size: 12px;
      color: #94A3B8;
      margin-bottom: 16px;
    }

    .pin-step-label strong {
      color: #0F172A;
    }

    .pin-keypad-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-bottom: 20px;
    }

    .pk {
      height: 60px;
      border-radius: 14px;
      border: 2px solid #E2E8F0;
      background: #F8FAFC;
      font-size: 22px;
      font-weight: 700;
      color: #0F172A;
      cursor: pointer;
      transition: all .12s;
      font-family: inherit;
    }

    .pk:hover {
      background: #F1F5F9;
      border-color: #CBD5E1;
      transform: translateY(-1px);
    }

    .pk:active {
      transform: scale(.93);
      background: #E2E8F0;
    }

    .pk-clr {
      background: #FEF2F2;
      color: #EF4444;
      font-size: 13px;
      font-weight: 700;
    }

    .pk-bk {
      background: #FFF7ED;
      color: #F97316;
      font-size: 20px;
    }

    .pin-err {
      background: #FEF2F2;
      color: #EF4444;
      border-radius: 10px;
      padding: 12px;
      font-size: 13px;
      margin-bottom: 16px;
      text-align: center;
    }

    .pin-info {
      background: #F0FDF4;
      border: 1px solid #86EFAC;
      border-radius: 10px;
      padding: 12px 16px;
      margin-bottom: 24px;
      font-size: 13px;
      color: #166534;
    }

    .pin-info-icon {
      font-size: 16px;
      margin-right: 6px;
    }

    .pin-back-link {
      display: block;
      text-align: center;
      margin-top: 16px;
      color: #64748B;
      font-size: 13px;
      text-decoration: none;
    }

    .pin-back-link:hover {
      color: #F97316;
    }

    .pin-progress {
      display: flex;
      gap: 8px;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
    }

    .pp-step {
      width: 32px;
      height: 4px;
      border-radius: 2px;
      background: #E2E8F0;
    }

    .pp-step.active {
      background: #F97316;
    }
  </style>
</head>

<body>
  <div class="pin-setup-wrap">
    <div class="pin-setup-card" style="animation:scaleIn .4s cubic-bezier(.22,1,.36,1) both">

      <div class="pin-logo">
        <div class="pin-logo-bar"></div>
        <div class="pin-logo-text">WELTHFLOW</div>
      </div>

      <span class="pin-icon-big">🔒</span>
      <h2 class="pin-setup-title"><?= $isChange ? 'Change Your PIN' : 'Secure Your Account' ?></h2>
      <p class="pin-setup-sub">
        <?= $isChange ? 'Update your 4-digit transaction PIN' : 'Create a 4-digit PIN to protect all transactions' ?>
      </p>

      <?php if ($error): ?>
        <div class="pin-err">⚠ <?= sanitize($error) ?></div>
      <?php endif; ?>

      <?php if (!$isChange): ?>
        <div class="pin-info">
          <span class="pin-info-icon">🛡️</span>
          Your PIN will be required for all deposits, withdrawals and investments. Keep it secret.
        </div>
      <?php endif; ?>

      <!-- Dots display -->
      <div id="stepLabel" class="pin-step-label"><strong>Step 1:</strong> Set your new PIN</div>
      <div class="pin-dots-row" id="pDots">
        <span class="pd" id="ppd0"></span>
        <span class="pd" id="ppd1"></span>
        <span class="pd" id="ppd2"></span>
        <span class="pd" id="ppd3"></span>
      </div>
      <br>

      <!-- Keypad -->
      <div class="pin-keypad-grid">
        <?php foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 'CLR', 0, '⌫'] as $k): ?>
          <button type="button" class="pk<?= $k === 'CLR' ? ' pk-clr' : ($k === '⌫' ? ' pk-bk' : '') ?>"
            onclick="spkPress('<?= $k ?>')"><?= $k ?></button>
        <?php endforeach; ?>
      </div>

      <!-- Hidden form -->
      <form method="POST" id="pinSetupForm" style="display:none">
        <?php if ($isChange): ?><input type="hidden" name="current_pin" id="fCurrentPin"><?php endif; ?>
        <input type="hidden" name="pin" id="fPin">
        <input type="hidden" name="pin2" id="fPin2">
      </form>

      <?php if ($isChange): ?>
        <a href="<?= SITE_BASE ?>/dashboard.php" class="pin-back-link">← Back to Dashboard</a>
      <?php else: ?>
        <a href="<?= SITE_BASE ?>/dashboard.php" class="pin-back-link">Skip for now →</a>
      <?php endif; ?>

    </div>
  </div>

  <script>
    var SP_STAGE = <?= $isChange ? '0' : '1' ?>; // 0=current, 1=new, 2=confirm
    var SP_CURRENT = '', SP_NEW = '', SP_CONFIRM = '';
    var SP_LABELS = <?= $isChange
      ? '["Step 1: Enter <strong>current</strong> PIN","Step 2: Set your <strong>new</strong> PIN","Step 3: <strong>Confirm</strong> new PIN"]'
      : '["","Step 1: Set your <strong>new</strong> PIN","Step 2: <strong>Confirm</strong> your PIN"]' ?>;

    function _val() {
      if (SP_STAGE === 0) return SP_CURRENT;
      if (SP_STAGE === 1) return SP_NEW;
      return SP_CONFIRM;
    }
    function _setVal(v) {
      if (SP_STAGE === 0) { SP_CURRENT = v; }
      else if (SP_STAGE === 1) { SP_NEW = v; }
      else { SP_CONFIRM = v; }
    }

    function spkPress(k) {
      var v = _val();
      if (k === 'CLR') { _setVal(''); }
      else if (k === '⌫') { _setVal(v.slice(0, -1)); }
      else if (v.length < 4) { _setVal(v + String(k)); }
      _updateDots();
      if (_val().length === 4) setTimeout(_nextStage, 300);
    }

    function _nextStage() {
      if (SP_STAGE === 0) {
        SP_STAGE = 1;
        _setVal('');
        _updateDots();
      } else if (SP_STAGE === 1) {
        SP_STAGE = 2;
        _setVal('');
        _updateDots();
      } else {
        // Validate + submit
        if (SP_NEW !== SP_CONFIRM) {
          // Mismatch - go back to step 1
          SP_STAGE = 1; SP_NEW = ''; SP_CONFIRM = '';
          _setVal('');
          _updateDots();
          var lbl = document.getElementById('stepLabel');
          if (lbl) { lbl.innerHTML = '<span style="color:#EF4444">⚠ PINs did not match. Try again:</span>'; }
          return;
        }
        // Submit form
        <?php if ($isChange): ?>
          document.getElementById('fCurrentPin').value = SP_CURRENT;
        <?php endif; ?>
        document.getElementById('fPin').value = SP_NEW;
        document.getElementById('fPin2').value = SP_CONFIRM;
        document.getElementById('pinSetupForm').submit();
      }
      var lbl = document.getElementById('stepLabel');
      if (lbl && SP_LABELS[SP_STAGE]) lbl.innerHTML = SP_LABELS[SP_STAGE];
    }

    function _updateDots() {
      var v = _val(), isConfirm = (SP_STAGE === 2);
      for (var i = 0; i < 4; i++) {
        var d = document.getElementById('ppd' + i);
        if (d) {
          d.className = 'pd' + (v.length > i ? ' filled' : '') + (isConfirm ? ' confirm-mode' : '');
        }
      }
    }

    document.addEventListener('keydown', function (e) {
      if (e.key >= '0' && e.key <= '9') spkPress(e.key);
      else if (e.key === 'Backspace') spkPress('⌫');
    });

    // Set initial label
    (function () {
      var lbl = document.getElementById('stepLabel');
      if (lbl && SP_LABELS[SP_STAGE]) lbl.innerHTML = SP_LABELS[SP_STAGE];
    })();
  </script>
<script type="text/javascript">
  var _smartsupp = _smartsupp || {};
  _smartsupp.key = 'e1f6488ccb5c061e24ad09e6ec82da06eead2ef8';
  window.smartsupp||(function(d) {
    var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
    s=d.getElementsByTagName('script')[0];c=d.createElement('script');
    c.type='text/javascript';c.charset='utf-8';c.async=true;
    c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
  })(document);
</script>
</body>
</html>