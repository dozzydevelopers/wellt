<?php
require_once '../includes/auth.php';
requireAdmin();
$pageTitle = 'Edit User';

$userId = intval($_GET['id'] ?? 0);
$editUser = fetchOne("SELECT * FROM users WHERE id=?", [$userId]);
if (!$editUser) {
  header('Location: users.php');
  exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = sanitize($_POST['action'] ?? 'update');
  if ($action === 'update') {
    $fullName = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $status = sanitize($_POST['status'] ?? 'active');
    $kycStatus = sanitize($_POST['kyc_status'] ?? 'pending');
    $isAdmin = intval($_POST['is_admin'] ?? 0);
    query(
      "UPDATE users SET full_name=?,email=?,phone=?,status=?,kyc_status=?,is_admin=? WHERE id=?",
      [$fullName, $email, $phone, $status, $kycStatus, $isAdmin, $userId]
    );
    if ($_POST['new_password'] ?? '') {
      query("UPDATE users SET password=? WHERE id=?", [hashPassword($_POST['new_password']), $userId]);
    }
    $editUser = fetchOne("SELECT * FROM users WHERE id=?", [$userId]);
    $success = 'User updated successfully!';
  } elseif ($action === 'add_funds') {
    $amount = floatval($_POST['amount'] ?? 0);
    $type = sanitize($_POST['fund_type'] ?? 'balance');
    $desc = sanitize($_POST['description'] ?? 'Admin adjustment');
    if ($amount != 0) {
      if ($type === 'balance')
        query("UPDATE users SET balance=balance+? WHERE id=?", [$amount, $userId]);
      elseif ($type === 'bonus')
        query("UPDATE users SET bonus=bonus+?, balance=balance+? WHERE id=?", [$amount, $amount, $userId]);
      elseif ($type === 'profit')
        query("UPDATE users SET total_profit=total_profit+?, balance=balance+? WHERE id=?", [$amount, $amount, $userId]);
      $ref = generateReference();
      insert(
        "INSERT INTO transactions (user_id,type,amount,description,status,reference) VALUES (?,?,?,?,?,?)",
        [$userId, $amount > 0 ? 'bonus' : 'withdrawal', abs($amount), $desc, 'completed', $ref]
      );
      sendNotification($userId, 'Account Adjustment', $desc . " (" . formatMoney($amount) . ")", 'info');
      $editUser = fetchOne("SELECT * FROM users WHERE id=?", [$userId]);
      $success = 'Funds updated!';
    }
  } elseif ($action === 'suspend') {
    $status = sanitize($_POST['new_status'] ?? 'suspended');
    query("UPDATE users SET status=? WHERE id=?", [$status, $userId]);
    $editUser = fetchOne("SELECT * FROM users WHERE id=?", [$userId]);
    $success = 'User status updated!';
  }
}

$userTxns = fetchAll("SELECT * FROM transactions WHERE user_id=? ORDER BY created_at DESC LIMIT 10", [$userId]);
$userInvs = fetchAll("SELECT i.*,p.name as pname FROM investments i JOIN plans p ON i.plan_id=p.id WHERE i.user_id=? ORDER BY i.start_date DESC", [$userId]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Edit User - Admin</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
  <?php include 'includes/admin-sidebar.php'; ?>
  <div class="admin-main">
    <?php include 'includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <div class="admin-page-header">
        <h2 class="admin-page-title">Edit User: <?= sanitize($editUser['username']) ?></h2>
        <a href="users.php" class="btn btn-outline">&larr; Back</a>
      </div>
      <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div><?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div><?php endif; ?>

      <div class="admin-two-col">
        <div>
          <!-- Profile Card -->
          <div class="admin-card mb-4">
            <div class="user-profile-header">
              <div class="admin-avatar-big"><?= strtoupper(substr($editUser['full_name'], 0, 1)) ?></div>
              <div>
                <h3><?= sanitize($editUser['full_name']) ?></h3>
                <p>@<?= sanitize($editUser['username']) ?> &bull; <?= sanitize($editUser['email']) ?></p>
                <p>Joined: <?= date('M j, Y', strtotime($editUser['created_at'])) ?></p>
              </div>
            </div>
            <div class="user-balance-grid">
              <div class="ub-item"><span>Balance</span><strong><?= formatMoney($editUser['balance']) ?></strong></div>
              <div class="ub-item">
                <span>Deposited</span><strong><?= formatMoney($editUser['total_deposited']) ?></strong></div>
              <div class="ub-item">
                <span>Withdrawn</span><strong><?= formatMoney($editUser['total_withdrawn']) ?></strong></div>
              <div class="ub-item"><span>Profit</span><strong><?= formatMoney($editUser['total_profit']) ?></strong>
              </div>
              <div class="ub-item"><span>Bonus</span><strong><?= formatMoney($editUser['bonus']) ?></strong></div>
              <div class="ub-item"><span>Portfolio</span><strong><?= formatMoney($editUser['portfolio']) ?></strong>
              </div>
            </div>
          </div>

          <!-- Edit Form -->
          <div class="admin-card mb-4">
            <h3>Edit Profile</h3>
            <form method="POST">
              <input type="hidden" name="action" value="update">
              <div class="form-row">
                <div class="form-group"><label>Full Name</label><input type="text" name="full_name"
                    value="<?= sanitize($editUser['full_name']) ?>"></div>
                <div class="form-group"><label>Email</label><input type="email" name="email"
                    value="<?= sanitize($editUser['email']) ?>"></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label>Phone</label><input type="text" name="phone"
                    value="<?= sanitize($editUser['phone']) ?>"></div>
                <div class="form-group"><label>KYC Status</label>
                  <select name="kyc_status">
                    <?php foreach (['pending', 'submitted', 'approved', 'rejected'] as $k): ?>
                      <option value="<?= $k ?>" <?= $editUser['kyc_status'] === $k ? 'selected' : '' ?>><?= ucfirst($k) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group"><label>Status</label>
                  <select name="status">
                    <?php foreach (['active', 'suspended', 'banned'] as $s): ?>
                      <option value="<?= $s ?>" <?= $editUser['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group"><label>Admin</label>
                  <select name="is_admin">
                    <option value="0" <?= !$editUser['is_admin'] ? 'selected' : '' ?>>No</option>
                    <option value="1" <?= $editUser['is_admin'] ? 'selected' : '' ?>>Yes</option>
                  </select>
                </div>
              </div>
              <div class="form-group"><label>New Password (leave blank to keep)</label><input type="password"
                  name="new_password"></div>
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
          </div>

          <!-- Add/Deduct Funds -->
          <div class="admin-card">
            <h3>Add / Deduct Funds</h3>
            <form method="POST">
              <input type="hidden" name="action" value="add_funds">
              <div class="form-row">
                <div class="form-group"><label>Amount (negative to deduct)</label><input type="number" name="amount"
                    step="0.01" required></div>
                <div class="form-group"><label>Type</label>
                  <select name="fund_type">
                    <option value="balance">Balance</option>
                    <option value="bonus">Bonus</option>
                    <option value="profit">Profit</option>
                  </select>
                </div>
              </div>
              <div class="form-group"><label>Description</label><input type="text" name="description"
                  placeholder="Admin note"></div>
              <button type="submit" class="btn btn-primary">Apply</button>
            </form>
          </div>
        </div>

        <div>
          <!-- Recent Transactions -->
          <div class="admin-card mb-4">
            <h3>Recent Transactions</h3>
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($userTxns as $t): ?>
                  <tr>
                    <td><?= date('M j, Y', strtotime($t['created_at'])) ?></td>
                    <td><?= ucfirst($t['type']) ?></td>
                    <td class="<?= in_array($t['type'], ['deposit', 'profit', 'bonus', 'referral']) ? 'positive' : 'negative' ?>">
                      <?= formatMoney($t['amount']) ?></td>
                    <td><span class="badge badge-<?= $t['status'] ?>"><?= ucfirst($t['status']) ?></span></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($userTxns)): ?>
                  <tr>
                    <td colspan="4" class="empty-row">None</td>
                  </tr><?php endif; ?>
              </tbody>
            </table>
            <a href="user-transactions.php?user_id=<?= $userId ?>" class="admin-view-all">View All</a>
          </div>

          <!-- Investments -->
          <div class="admin-card">
            <h3>Investments</h3>
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Plan</th>
                  <th>Amount</th>
                  <th>Profit</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($userInvs as $inv): ?>
                  <tr>
                    <td><?= sanitize($inv['pname']) ?></td>
                    <td><?= formatMoney($inv['amount']) ?></td>
                    <td class="positive"><?= formatMoney($inv['total_profit']) ?></td>
                    <td><span class="badge badge-<?= $inv['status'] ?>"><?= ucfirst($inv['status']) ?></span></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($userInvs)): ?>
                  <tr>
                    <td colspan="4" class="empty-row">None</td>
                  </tr><?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>