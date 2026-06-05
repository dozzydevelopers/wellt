<?php
require_once '../includes/auth.php';
requireAdmin();
$user = getCurrentUser();
$pageTitle = 'Admin Dashboard';

$totalUsers = fetchOne("SELECT COUNT(*) as c FROM users WHERE is_admin=0")['c'];
$activeInvs = fetchOne("SELECT COUNT(*) as c FROM investments WHERE status='active'")['c'];
$totalDeposited = fetchOne("SELECT SUM(amount) as s FROM transactions WHERE type='deposit' AND status='approved'")['s'] ?? 0;
$totalWithdrawn = fetchOne("SELECT SUM(amount) as s FROM transactions WHERE type='withdrawal' AND status='approved'")['s'] ?? 0;
$pendingDeposits = fetchOne("SELECT COUNT(*) as c FROM transactions WHERE type='deposit' AND status='pending'")['c'];
$pendingWithdrawals = fetchOne("SELECT COUNT(*) as c FROM transactions WHERE type='withdrawal' AND status='pending'")['c'];
$pendingKyc = fetchOne("SELECT COUNT(*) as c FROM kyc_documents WHERE status='pending'")['c'];
$totalBalance = fetchOne("SELECT SUM(balance) as s FROM users WHERE is_admin=0")['s'] ?? 0;

$recentUsers = fetchAll("SELECT * FROM users WHERE is_admin=0 ORDER BY created_at DESC LIMIT 5");
$recentTxns = fetchAll("SELECT t.*, u.username FROM transactions t JOIN users u ON t.user_id=u.id ORDER BY t.created_at DESC LIMIT 8");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Welthflow</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
  <?php include 'includes/admin-sidebar.php'; ?>
  <div class="admin-main">
    <?php include 'includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <h2 class="admin-page-title">Dashboard Overview</h2>

      <div class="admin-stats-grid">
        <div class="admin-stat-card blue">
          <div class="asc-icon">&#128101;</div>
          <div class="asc-val"><?= number_format($totalUsers) ?></div>
          <div class="asc-label">Total Users</div>
        </div>
        <div class="admin-stat-card green">
          <div class="asc-icon">&#128200;</div>
          <div class="asc-val"><?= number_format($activeInvs) ?></div>
          <div class="asc-label">Active Investments</div>
        </div>
        <div class="admin-stat-card orange">
          <div class="asc-icon">&#128176;</div>
          <div class="asc-val"><?= formatMoney($totalDeposited) ?></div>
          <div class="asc-label">Total Deposited</div>
        </div>
        <div class="admin-stat-card red">
          <div class="asc-icon">&#8593;</div>
          <div class="asc-val"><?= formatMoney($totalWithdrawn) ?></div>
          <div class="asc-label">Total Withdrawn</div>
        </div>
        <div class="admin-stat-card yellow">
          <div class="asc-icon">&#9203;</div>
          <div class="asc-val"><?= $pendingDeposits ?></div>
          <div class="asc-label">Pending Deposits</div>
        </div>
        <div class="admin-stat-card purple">
          <div class="asc-icon">&#9203;</div>
          <div class="asc-val"><?= $pendingWithdrawals ?></div>
          <div class="asc-label">Pending Withdrawals</div>
        </div>
        <div class="admin-stat-card teal">
          <div class="asc-icon">&#128100;</div>
          <div class="asc-val"><?= $pendingKyc ?></div>
          <div class="asc-label">Pending KYC</div>
        </div>
        <div class="admin-stat-card indigo">
          <div class="asc-icon">&#128178;</div>
          <div class="asc-val"><?= formatMoney($totalBalance) ?></div>
          <div class="asc-label">Users Balance</div>
        </div>
      </div>

      <?php if ($pendingDeposits > 0 || $pendingWithdrawals > 0 || $pendingKyc > 0): ?>
        <div class="alert alert-warning">
          &#9888; Pending actions:
          <?php if ($pendingDeposits): ?><a href="deposits.php?status=pending">?? Deposits
              (<?= $pendingDeposits ?>)</a><?php endif; ?>
          <?php if ($pendingWithdrawals): ?><a href="withdrawals.php?status=pending">?? Withdrawals
              (<?= $pendingWithdrawals ?>)</a><?php endif; ?>
          <?php if ($pendingKyc): ?><a href="kyc.php?status=pending">?? KYC (<?= $pendingKyc ?>)</a><?php endif; ?>
        </div>
      <?php endif; ?>

      <div class="admin-two-col">
        <div class="admin-card">
          <h3>Recent Users</h3>
          <table class="admin-table">
            <thead>
              <tr>
                <th>User</th>
                <th>Email</th>
                <th>Balance</th>
                <th>Joined</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentUsers as $u): ?>
                <tr>
                  <td><?= sanitize($u['username']) ?></td>
                  <td><?= sanitize($u['email']) ?></td>
                  <td><?= formatMoney($u['balance']) ?></td>
                  <td><?= date('M j', strtotime($u['created_at'])) ?></td>
                  <td><a href="user-edit.php?id=<?= $u['id'] ?>" class="btn-table">View</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <a href="users.php" class="admin-view-all">View All Users</a>
        </div>

        <div class="admin-card">
          <h3>Recent Transactions</h3>
          <table class="admin-table">
            <thead>
              <tr>
                <th>User</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentTxns as $t): ?>
                <tr>
                  <td><?= sanitize($t['username']) ?></td>
                  <td><?= ucfirst($t['type']) ?></td>
                  <td><?= formatMoney($t['amount']) ?></td>
                  <td><span class="badge badge-<?= $t['status'] ?>"><?= ucfirst($t['status']) ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <a href="transactions.php" class="admin-view-all">View All</a>
        </div>
      </div>
    </div>
  </div>
</body>

</html>