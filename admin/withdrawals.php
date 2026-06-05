<?php
require_once '../includes/auth.php';
requireAdmin();
$pageTitle = 'Withdrawals';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $txnId = intval($_POST['txn_id'] ?? 0);
  $action = sanitize($_POST['action'] ?? '');
  $note = sanitize($_POST['admin_note'] ?? '');
  $txn = fetchOne("SELECT * FROM transactions WHERE id=? AND type='withdrawal'", [$txnId]);
  if ($txn && $action === 'approve' && $txn['status'] === 'pending') {
    query("UPDATE transactions SET status='approved', admin_note=? WHERE id=?", [$note, $txnId]);
    query("UPDATE users SET total_withdrawn=total_withdrawn+? WHERE id=?", [$txn['amount'], $txn['user_id']]);
    sendNotification($txn['user_id'], 'Withdrawal Approved', "Your withdrawal of " . formatMoney($txn['amount']) . " has been approved and is being processed!", 'success');
    $uData = fetchOne("SELECT * FROM users WHERE id=?", [$txn['user_id']]);
    $txnFull = fetchOne("SELECT * FROM transactions WHERE id=?", [$txnId]);
    if ($uData && $txnFull)
      sendWithdrawalApprovedEmail($uData, $txnFull);
  } elseif ($txn && $action === 'reject' && $txn['status'] === 'pending') {
    query("UPDATE users SET balance=balance+? WHERE id=?", [$txn['amount'], $txn['user_id']]);
    query("UPDATE transactions SET status='rejected', admin_note=? WHERE id=?", [$note, $txnId]);
    sendNotification($txn['user_id'], 'Withdrawal Rejected', "Your withdrawal of " . formatMoney($txn['amount']) . " was rejected. Amount refunded. Reason: $note", 'error');
    $uData = fetchOne("SELECT * FROM users WHERE id=?", [$txn['user_id']]);
    $txnFull = fetchOne("SELECT * FROM transactions WHERE id=?", [$txnId]);
    if ($uData && $txnFull)
      sendWithdrawalRejectedEmail($uData, $txnFull, $note);
  }
  header('Location: withdrawals.php');
  exit;
}

$status = sanitize($_GET['status'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;
$where = "t.type='withdrawal'";
$params = [];
if ($status) {
  $where .= " AND t.status=?";
  $params[] = $status;
}
$total = fetchOne("SELECT COUNT(*) as c FROM transactions t WHERE $where", $params)['c'];
$ws = fetchAll("SELECT t.*, u.username, u.email FROM transactions t JOIN users u ON t.user_id=u.id WHERE $where ORDER BY t.created_at DESC LIMIT $perPage OFFSET $offset", $params);
$pages = ceil($total / $perPage);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Withdrawals - Admin</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
  <?php include 'includes/admin-sidebar.php'; ?>
  <div class="admin-main">
    <?php include 'includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <h2 class="admin-page-title">Withdrawal Management</h2>
      <div class="admin-filters">
        <form method="GET" class="filter-form">
          <select name="status">
            <option value="">All</option><?php foreach (['pending', 'approved', 'rejected'] as $s): ?>
              <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-primary">Filter</button>
        </form>
      </div>
      <div class="admin-card">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>User</th>
              <th>Amount</th>
              <th>Method</th>
              <th>Wallet Address</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ws as $w): ?>
              <tr>
                <td><?= date('M j, Y H:i', strtotime($w['created_at'])) ?></td>
                <td><?= sanitize($w['username']) ?><br><small><?= sanitize($w['email']) ?></small></td>
                <td class="negative"><strong><?= formatMoney($w['amount']) ?></strong></td>
                <td><?= sanitize($w['payment_method'] ?? '-') ?></td>
                <td><small><?= sanitize($w['payment_address'] ?? '-') ?></small></td>
                <td><span class="badge badge-<?= $w['status'] ?>"><?= ucfirst($w['status']) ?></span></td>
                <td>
                  <?php if ($w['status'] === 'pending'): ?>
                    <form method="POST" style="display:inline">
                      <input type="hidden" name="txn_id" value="<?= $w['id'] ?>">
                      <input type="hidden" name="action" value="approve">
                      <button type="submit" class="btn-table green" onclick="return confirm('Approve?')">Approve</button>
                    </form>
                    <form method="POST" style="display:inline" onsubmit="return promptNote(this)">
                      <input type="hidden" name="txn_id" value="<?= $w['id'] ?>">
                      <input type="hidden" name="action" value="reject">
                      <input type="hidden" name="admin_note" value="">
                      <button type="submit" class="btn-table red">Reject</button>
                    </form>
                  <?php else: ?>-<?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($ws)): ?>
              <tr>
                <td colspan="7" class="empty-row">No withdrawals found</td>
              </tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <script>
    function promptNote(form) {
      var note = prompt('Reason for rejection (will refund balance):') || '';
      form.querySelector('[name=admin_note]').value = note;
      return confirm('Reject and refund?');
    }
  </script>
</body>

</html>