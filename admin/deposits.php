<?php
require_once '../includes/auth.php';
requireAdmin();
$pageTitle = 'Deposits';

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $txnId = intval($_POST['txn_id'] ?? 0);
  $action = sanitize($_POST['action'] ?? '');
  $note = sanitize($_POST['admin_note'] ?? '');
  $txn = fetchOne("SELECT * FROM transactions WHERE id=? AND type='deposit'", [$txnId]);
  if ($txn && $action === 'approve' && $txn['status'] === 'pending') {
    query("UPDATE transactions SET status='approved', admin_note=? WHERE id=?", [$note, $txnId]);
    query("UPDATE users SET balance=balance+?, total_deposited=total_deposited+? WHERE id=?", [$txn['amount'], $txn['amount'], $txn['user_id']]);
    sendNotification($txn['user_id'], 'Deposit Approved', "Your deposit of " . formatMoney($txn['amount']) . " has been approved!", 'success');
    $uData = fetchOne("SELECT * FROM users WHERE id=?", [$txn['user_id']]);
    $txnFull = fetchOne("SELECT * FROM transactions WHERE id=?", [$txnId]);
    if ($uData && $txnFull)
      sendDepositApprovedEmail($uData, $txnFull);
  } elseif ($txn && $action === 'reject' && $txn['status'] === 'pending') {
    query("UPDATE transactions SET status='rejected', admin_note=? WHERE id=?", [$note, $txnId]);
    sendNotification($txn['user_id'], 'Deposit Rejected', "Your deposit of " . formatMoney($txn['amount']) . " was rejected. Reason: $note", 'error');
    $uData = fetchOne("SELECT * FROM users WHERE id=?", [$txn['user_id']]);
    $txnFull = fetchOne("SELECT * FROM transactions WHERE id=?", [$txnId]);
    if ($uData && $txnFull)
      sendDepositRejectedEmail($uData, $txnFull, $note);
  }
  header('Location: deposits.php');
  exit;
}

$status = sanitize($_GET['status'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;
$where = "t.type='deposit'";
$params = [];
if ($status) {
  $where .= " AND t.status=?";
  $params[] = $status;
}
$total = fetchOne("SELECT COUNT(*) as c FROM transactions t WHERE $where", $params)['c'];
$deps = fetchAll("SELECT t.*, u.username, u.email FROM transactions t JOIN users u ON t.user_id=u.id WHERE $where ORDER BY t.created_at DESC LIMIT $perPage OFFSET $offset", $params);
$pages = ceil($total / $perPage);
$pendingSum = fetchOne("SELECT SUM(amount) as s FROM transactions WHERE type='deposit' AND status='pending'")['s'] ?? 0;
$approvedSum = fetchOne("SELECT SUM(amount) as s FROM transactions WHERE type='deposit' AND status='approved'")['s'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Deposits - Admin</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
  <?php include 'includes/admin-sidebar.php'; ?>
  <div class="admin-main">
    <?php include 'includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <h2 class="admin-page-title">Deposit Management</h2>
      <div class="admin-stats-mini">
        <div class="stat-mini orange">Pending Sum: <strong><?= formatMoney($pendingSum) ?></strong></div>
        <div class="stat-mini green">Approved Sum: <strong><?= formatMoney($approvedSum) ?></strong></div>
      </div>
      <div class="admin-filters">
        <form method="GET" class="filter-form">
          <select name="status">
            <option value="">All</option>
            <?php foreach (['pending', 'approved', 'rejected'] as $s): ?>
              <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
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
              <th>Reference</th>
              <th>Proof</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($deps as $d): ?>
              <tr>
                <td><?= date('M j, Y H:i', strtotime($d['created_at'])) ?></td>
                <td><?= sanitize($d['username']) ?><br><small><?= sanitize($d['email']) ?></small></td>
                <td class="positive"><strong><?= formatMoney($d['amount']) ?></strong></td>
                <td><?= sanitize($d['payment_method'] ?? '-') ?></td>
                <td><small><?= sanitize($d['reference']) ?></small></td>
                <td><?php if ($d['proof_image']): ?><a href="/uploads/deposits/<?= $d['proof_image'] ?>" target="_blank"
                      class="btn-table blue">View</a><?php else: ?>-<?php endif; ?></td>
                <td><span class="badge badge-<?= $d['status'] ?>"><?= ucfirst($d['status']) ?></span></td>
                <td>
                  <?php if ($d['status'] === 'pending'): ?>
                    <form method="POST" style="display:inline">
                      <input type="hidden" name="txn_id" value="<?= $d['id'] ?>">
                      <input type="hidden" name="action" value="approve">
                      <button type="submit" class="btn-table green"
                        onclick="return confirm('Approve this deposit?')">Approve</button>
                    </form>
                    <form method="POST" style="display:inline" onsubmit="return promptNote(this)">
                      <input type="hidden" name="txn_id" value="<?= $d['id'] ?>">
                      <input type="hidden" name="action" value="reject">
                      <input type="hidden" name="admin_note" id="note_<?= $d['id'] ?>" value="">
                      <button type="submit" class="btn-table red">Reject</button>
                    </form>
                  <?php else: ?>    <?= $d['admin_note'] ? '<small title="' . sanitize($d['admin_note']) . '">Note &#9432;</small>' : '-' ?>  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($deps)): ?>
              <tr>
                <td colspan="8" class="empty-row">No deposits found</td>
              </tr><?php endif; ?>
          </tbody>
        </table>
      </div>
      <?php if ($pages > 1): ?>
        <div class="pagination"><?php for ($i = 1; $i <= $pages; $i++): ?><a href="?status=<?= $status ?>&page=<?= $i ?>"
              class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a><?php endfor; ?></div><?php endif; ?>
    </div>
  </div>
  <script>
    function promptNote(form) {
      var note = prompt('Reason for rejection (optional):') || '';
      form.querySelector('[name=admin_note]').value = note;
      return confirm('Reject this deposit?');
    }
  </script>
</body>

</html>