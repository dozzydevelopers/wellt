<?php
require_once '../includes/auth.php';
requireAdmin();
$pageTitle = 'Site Settings';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $keys = ['site_name', 'site_email', 'site_phone', 'site_url', 'min_deposit', 'max_deposit', 'min_withdrawal', 'max_withdrawal', 'withdrawal_fee_percent', 'referral_bonus_percent', 'registration_bonus', 'btc_wallet', 'eth_wallet', 'usdt_wallet', 'maintenance_mode', 'profit_cron_key', 'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_from_name', 'smtp_from_email', 'smtp_encryption'];
  foreach ($keys as $key) {
    if (isset($_POST[$key])) {
      $val = sanitize($_POST[$key]);
      $existing = fetchOne("SELECT id FROM settings WHERE setting_key=?", [$key]);
      if ($existing)
        query("UPDATE settings SET setting_value=? WHERE setting_key=?", [$val, $key]);
      else
        insert("INSERT INTO settings (setting_key,setting_value) VALUES (?,?)", [$key, $val]);
    }
  }
  $success = 'Settings saved successfully!';
}

$settings = [];
foreach (fetchAll("SELECT setting_key, setting_value FROM settings") as $r)
  $settings[$r['setting_key']] = $r['setting_value'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Settings - Admin</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
  <?php include 'includes/admin-sidebar.php'; ?>
  <div class="admin-main">
    <?php include 'includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <h2 class="admin-page-title">Site Settings</h2>
      <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div><?php endif; ?>
      <form method="POST">
        <div class="admin-two-col">
          <div>
            <div class="admin-card mb-4">
              <h3>General</h3>
              <div class="form-group"><label>Site Name</label><input type="text" name="site_name"
                  value="<?= sanitize($settings['site_name'] ?? '') ?>"></div>
              <div class="form-group"><label>Support Email</label><input type="email" name="site_email"
                  value="<?= sanitize($settings['site_email'] ?? '') ?>"></div>
              <div class="form-group"><label>Support Phone</label><input type="text" name="site_phone"
                  value="<?= sanitize($settings['site_phone'] ?? '') ?>"></div>
              <div class="form-group"><label>Maintenance Mode</label><select name="maintenance_mode">
                  <option value="0" <?= ($settings['maintenance_mode'] ?? '0') === '0' ? 'selected' : '' ?>>Off</option>
                  <option value="1" <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'selected' : '' ?>>On</option>
                </select></div>
            </div>
            <div class="admin-card mb-4">
              <h3>Finance</h3>
              <div class="form-row">
                <div class="form-group"><label>Min Deposit ($)</label><input type="number" name="min_deposit"
                    value="<?= sanitize($settings['min_deposit'] ?? '100') ?>"></div>
                <div class="form-group"><label>Max Deposit ($)</label><input type="number" name="max_deposit"
                    value="<?= sanitize($settings['max_deposit'] ?? '100000') ?>"></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label>Min Withdrawal ($)</label><input type="number" name="min_withdrawal"
                    value="<?= sanitize($settings['min_withdrawal'] ?? '50') ?>"></div>
                <div class="form-group"><label>Max Withdrawal ($)</label><input type="number" name="max_withdrawal"
                    value="<?= sanitize($settings['max_withdrawal'] ?? '50000') ?>"></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label>Withdrawal Fee (%)</label><input type="number"
                    name="withdrawal_fee_percent" step="0.01"
                    value="<?= sanitize($settings['withdrawal_fee_percent'] ?? '2') ?>"></div>
                <div class="form-group"><label>Referral Bonus (%)</label><input type="number"
                    name="referral_bonus_percent" step="0.01"
                    value="<?= sanitize($settings['referral_bonus_percent'] ?? '5') ?>"></div>
              </div>
              <div class="form-group"><label>Registration Bonus ($)</label><input type="number"
                  name="registration_bonus" step="0.01" value="<?= sanitize($settings['registration_bonus'] ?? '8') ?>">
              </div>
            </div>
          </div>
          <div>
            <div class="admin-card mb-4">
              <h3>Crypto Wallets</h3>
              <div class="form-group"><label>Bitcoin (BTC) Address</label><input type="text" name="btc_wallet"
                  value="<?= sanitize($settings['btc_wallet'] ?? '') ?>"></div>
              <div class="form-group"><label>Ethereum (ETH) Address</label><input type="text" name="eth_wallet"
                  value="<?= sanitize($settings['eth_wallet'] ?? '') ?>"></div>
              <div class="form-group"><label>USDT (TRC20) Address</label><input type="text" name="usdt_wallet"
                  value="<?= sanitize($settings['usdt_wallet'] ?? '') ?>"></div>
            </div>
            <div class="admin-card mb-4">
              <h3>&#128231; Email / SMTP</h3>
              <p class="form-hint" style="margin-bottom:12px">Configure SMTP to send automated emails. Works with Gmail,
                SendGrid, Mailgun, etc.</p>
              <div class="form-group"><label>Site URL (for email links)</label><input type="text" name="site_url"
                  placeholder="https://yourdomain.com" value="<?= sanitize($settings['site_url'] ?? '') ?>"></div>
              <div class="form-row">
                <div class="form-group"><label>SMTP Host</label><input type="text" name="smtp_host"
                    placeholder="smtp.gmail.com" value="<?= sanitize($settings['smtp_host'] ?? '') ?>"></div>
                <div class="form-group"><label>SMTP Port</label><input type="number" name="smtp_port" placeholder="587"
                    value="<?= sanitize($settings['smtp_port'] ?? '587') ?>"></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label>SMTP Username</label><input type="text" name="smtp_user"
                    placeholder="you@gmail.com" value="<?= sanitize($settings['smtp_user'] ?? '') ?>"></div>
                <div class="form-group"><label>SMTP Password / App Key</label><input type="password" name="smtp_pass"
                    placeholder="••••••••" value="<?= sanitize($settings['smtp_pass'] ?? '') ?>"></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label>From Name</label><input type="text" name="smtp_from_name"
                    value="<?= sanitize($settings['smtp_from_name'] ?? 'Welthflow') ?>"></div>
                <div class="form-group"><label>From Email</label><input type="email" name="smtp_from_email"
                    value="<?= sanitize($settings['smtp_from_email'] ?? 'noreply@welthflow.com') ?>"></div>
              </div>
              <div class="form-group"><label>Encryption</label>
                <select name="smtp_encryption">
                  <?php foreach (['tls' => 'TLS (Port 587 — Recommended)', 'ssl' => 'SSL (Port 465)', '' => 'None (Port 25)'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= ($settings['smtp_encryption'] ?? 'tls') === $v ? 'selected' : '' ?>><?= $l ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <p class="form-hint">&#128161; <strong>Gmail users:</strong> Use an <a
                  href="https://myaccount.google.com/apppasswords" target="_blank" style="color:var(--primary)">App
                  Password</a> (not your normal password). Enable 2FA first.</p>
            </div>
            <div class="admin-card">
              <h3>Automation</h3>
              <div class="form-group"><label>Profit Cron Key</label><input type="text" name="profit_cron_key"
                  value="<?= sanitize($settings['profit_cron_key'] ?? '') ?>"></div>
              <p class="form-hint">Set up a cron job to auto-run
                profits:<br><code>GET /public/cron-profit.php?key=YOUR_KEY</code><br>Run daily at midnight.</p>
            </div>
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg">Save All Settings</button>
      </form>
    </div>
  </div>
</body>

</html>