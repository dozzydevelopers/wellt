<?php
require_once '../includes/auth.php';
requireAdmin();
$pageTitle = 'KYC Requests';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $kycId = intval($_POST['kyc_id'] ?? 0);
  $action = sanitize($_POST['action'] ?? '');
  $note = sanitize($_POST['admin_note'] ?? '');
  $kyc = fetchOne("SELECT * FROM kyc_documents WHERE id=?", [$kycId]);
  if ($kyc) {
    if ($action === 'approve') {
      query("UPDATE kyc_documents SET status='approved', admin_note=?, reviewed_at=datetime('now') WHERE id=?", [$note, $kycId]);
      query("UPDATE users SET kyc_status='approved' WHERE id=?", [$kyc['user_id']]);
      sendNotification($kyc['user_id'], 'KYC Approved', 'Your identity has been verified!', 'success');
      $uData = fetchOne("SELECT * FROM users WHERE id=?", [$kyc['user_id']]);
      if ($uData)
        sendKycApprovedEmail($uData);
    } elseif ($action === 'reject') {
      query("UPDATE kyc_documents SET status='rejected', admin_note=?, reviewed_at=datetime('now') WHERE id=?", [$note, $kycId]);
      query("UPDATE users SET kyc_status='rejected' WHERE id=?", [$kyc['user_id']]);
      sendNotification($kyc['user_id'], 'KYC Rejected', "Your KYC was rejected. Reason: $note. Please resubmit.", 'error');
      $uData = fetchOne("SELECT * FROM users WHERE id=?", [$kyc['user_id']]);
      if ($uData)
        sendKycRejectedEmail($uData, $note);
    }
  }
  header('Location: kyc.php');
  exit;
}

$status = sanitize($_GET['status'] ?? '');
$where = "1=1";
$params = [];
if ($status) {
  $where .= " AND k.status=?";
  $params[] = $status;
}
$docs = fetchAll("SELECT k.*, u.username, u.email, u.full_name FROM kyc_documents k JOIN users u ON k.user_id=u.id WHERE $where ORDER BY k.submitted_at DESC", $params);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>KYC - Admin</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
  <?php include 'includes/admin-sidebar.php'; ?>
  <div class="admin-main">
    <?php include 'includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <h2 class="admin-page-title">KYC Verification Requests</h2>
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
              <th>User</th>
              <th>Doc Type</th>
              <th>Documents</th>
              <th>Submitted</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($docs as $d): ?>
              <tr>
                <td><?= sanitize($d['username']) ?><br><small><?= sanitize($d['email']) ?></small></td>
                <td><?= ucwords(str_replace('_', ' ', $d['doc_type'])) ?></td>
                <td>
                  <?php if ($d['doc_front']): ?><a href="/uploads/kyc/<?= $d['doc_front'] ?>" target="_blank"
                      class="btn-table blue">Front</a><?php endif; ?>
                  <?php if ($d['doc_back']): ?><a href="/uploads/kyc/<?= $d['doc_back'] ?>" target="_blank"
                      class="btn-table">Back</a><?php endif; ?>
                  <?php if ($d['selfie']): ?><a href="/uploads/kyc/<?= $d['selfie'] ?>" target="_blank"
                      class="btn-table">Selfie</a><?php endif; ?>
                </td>
                <td><?= date('M j, Y', strtotime($d['submitted_at'])) ?></td>
                <td><span class="badge badge-<?= $d['status'] ?>"><?= ucfirst($d['status']) ?></span></td>
                <td>
                  <?php if ($d['status'] === 'pending'): ?>
                    <form method="POST" style="display:inline">
                      <input type="hidden" name="kyc_id" value="<?= $d['id'] ?>">
                      <input type="hidden" name="action" value="approve">
                      <button type="submit" class="btn-table green"
                        onclick="return confirm('Approve KYC?')">Approve</button>
                    </form>
                    <form method="POST" style="display:inline" onsubmit="return setRejectNote(this)">
                      <input type="hidden" name="kyc_id" value="<?= $d['id'] ?>">
                      <input type="hidden" name="action" value="reject">
                      <input type="hidden" name="admin_note" value="">
                      <button type="submit" class="btn-table red">Reject</button>
                    </form>
                  <?php else: ?>    <?= sanitize($d['admin_note'] ?? '-') ?>  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($docs)): ?>
              <tr>
                <td colspan="6" class="empty-row">No KYC requests</td>
              </tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <script>
    function setRejectNote(form) {
      var note = prompt('Rejection reason:') || '';
      form.querySelector('[name=admin_note]').value = note;
      return confirm('Reject this KYC?');
    }
  </script>
</body>

</html>