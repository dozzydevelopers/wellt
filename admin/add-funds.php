<?php
require_once '../includes/auth.php';
requireAdmin();
$pageTitle = 'Add/Deduct Funds';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userId = intval($_POST['user_id'] ?? 0);
  $amount = floatval($_POST['amount'] ?? 0);
  $type = sanitize($_POST['fund_type'] ?? 'balance');
  $desc = sanitize($_POST['description'] ?? 'Admin adjustment');
  $targetUser = fetchOne("SELECT * FROM users WHERE id=?", [$userId]);
  if (!$targetUser) {
    $error = 'User not found.';
  } elseif ($amount === 0.0) {
    $error = 'Amount cannot be zero.';
  } else {
    if ($type === 'balance')
      query("UPDATE users SET balance=balance+? WHERE id=?", [$amount, $userId]);
    elseif ($type === 'bonus')
      query("UPDATE users SET bonus=bonus+?,balance=balance+? WHERE id=?", [$amount, $amount, $userId]);
    elseif ($type === 'profit')
      query("UPDATE users SET total_profit=total_profit+?,balance=balance+? WHERE id=?", [$amount, $amount, $userId]);
    elseif ($type === 'deposit')
      query("UPDATE users SET total_deposited=total_deposited+?,balance=balance+? WHERE id=?", [$amount, $amount, $userId]);
    $ref = 'TXN' . strtoupper(uniqid());
    insert(
      "INSERT INTO transactions (user_id,type,amount,description,status,reference) VALUES (?,?,?,?,?,?)",
      [$userId, $amount > 0 ? 'bonus' : 'withdrawal', abs($amount), $desc, 'completed', $ref]
    );
    sendNotification($userId, 'Account Update', $desc . ' (' . ($amount > 0 ? '+' : '') . number_format($amount, 2) . ')');
    ;
    $success = "Funds updated for {$targetUser['username']}. New balance: " . formatMoney(fetchOne("SELECT balance FROM users WHERE id=?", [$userId])['balance']);
  }
}

$preUser = null;
if (isset($_GET['user_id']))
  $preUser = fetchOne("SELECT * FROM users WHERE id=?", [intval($_GET['user_id'])]);
$users = fetchAll("SELECT id,username,full_name,balance FROM users WHERE is_admin=0 ORDER BY username ASC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Add Funds - Admin</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
  <?php include 'includes/admin-sidebar.php'; ?>
  <div class="admin-main">
    <?php include 'includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <h2 class="admin-page-title">Add / Deduct Funds</h2>
      <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div><?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div><?php endif; ?>
      <div class="admin-card" style="max-width:500px">
        <form method="POST">
          <div class="form-group"><label>Select User</label>
            <select name="user_id" required>
              <option value="">-- Select User --</option>
              <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>" <?= ($preUser && $preUser['id'] == $u['id']) ? 'selected' : '' ?>>
                  <?= sanitize($u['username']) ?> (<?= sanitize($u['full_name']) ?>) - <?= formatMoney($u['balance']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group"><label>Amount (negative to deduct)</label><input type="number" name="amount"
              step="0.01" required placeholder="e.g. 100 or -50"></div>
          <div class="form-group"><label>Type</label>
            <select name="fund_type">
              <option value="balance">Balance (direct)</option>
              <option value="bonus">Bonus</option>
              <option value="profit">Profit</option>
              <option value="deposit">Deposit</option>
            </select>
          </div>
          <div class="form-group"><label>Description / Note</label><input type="text" name="description"
              placeholder="Admin adjustment note" required></div>
          <button type="submit" class="btn btn-primary">Apply Funds</button>
        </form>
      </div>
    </div>
  </div>
</body>

</html>