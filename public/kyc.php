<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'KYC Verification';
$success = '';
$error = '';

$existing = fetchOne("SELECT * FROM kyc_documents WHERE user_id=? ORDER BY id DESC LIMIT 1", [$user['id']]);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$existing) {
    $docType = sanitize($_POST['doc_type'] ?? '');
    if (!$docType) { $error = 'Please select document type.'; }
    elseif (!isset($_FILES['doc_front']) || $_FILES['doc_front']['error'] !== 0) { $error = 'Please upload front of document.'; }
    else {
        $uploadDir = '../uploads/kyc/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $uploadFile = function($file, $prefix) use ($user, $uploadDir) {
            if (!$file || $file['error'] !== 0) return null;
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','pdf'])) return null;
            $name = $prefix . '_' . $user['id'] . '_' . time() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], $uploadDir . $name);
            return $name;
        };
        $front  = $uploadFile($_FILES['doc_front'], 'front');
        $back   = $uploadFile($_FILES['doc_back'] ?? null, 'back');
        $selfie = $uploadFile($_FILES['selfie'] ?? null, 'selfie');

        insert("INSERT INTO kyc_documents (user_id, doc_type, doc_front, doc_back, selfie) VALUES (?,?,?,?,?)",
            [$user['id'], $docType, $front, $back, $selfie]);
        query("UPDATE users SET kyc_status='submitted' WHERE id=?", [$user['id']]);
        sendNotification($user['id'], 'KYC Submitted', 'Your KYC documents have been submitted and are under review.', 'info');
        $user = getCurrentUser();
        $existing = fetchOne("SELECT * FROM kyc_documents WHERE user_id=? ORDER BY id DESC LIMIT 1", [$user['id']]);
        $success = 'KYC documents submitted successfully!';
    }
}

include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">KYC Verification</h2>
      <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

      <div class="kyc-status-banner kyc-status-<?= $user['kyc_status'] ?>">
        <strong>KYC Status: <?= strtoupper($user['kyc_status']) ?></strong>
        <?php if ($user['kyc_status'] === 'approved'): ?>
          &#10003; Your identity has been verified.
        <?php elseif ($user['kyc_status'] === 'submitted'): ?>
          Your documents are under review. Please wait 24-48 hours.
        <?php elseif ($user['kyc_status'] === 'rejected'): ?>
          Your documents were rejected. Please resubmit.
        <?php else: ?>
          Please complete KYC to unlock full account features.
        <?php endif; ?>
      </div>

      <?php if ($user['kyc_status'] !== 'approved' && $user['kyc_status'] !== 'submitted'): ?>
      <div class="card">
        <h3>Submit KYC Documents</h3>
        <form method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label>Document Type *</label>
            <select name="doc_type" required>
              <option value="">Select type</option>
              <option value="passport">Passport</option>
              <option value="national_id">National ID</option>
              <option value="drivers_license">Driver's License</option>
              <option value="utility_bill">Utility Bill</option>
            </select>
          </div>
          <div class="form-group">
            <label>Front of Document *</label>
            <input type="file" name="doc_front" accept="image/*,.pdf" required>
          </div>
          <div class="form-group">
            <label>Back of Document (if applicable)</label>
            <input type="file" name="doc_back" accept="image/*,.pdf">
          </div>
          <div class="form-group">
            <label>Selfie with Document</label>
            <input type="file" name="selfie" accept="image/*">
          </div>
          <button type="submit" class="btn btn-primary">Submit for Verification</button>
        </form>
      </div>
      <?php elseif ($existing): ?>
      <div class="card">
        <h3>Submission Details</h3>
        <p>Document Type: <strong><?= ucwords(str_replace('_',' ',$existing['doc_type'])) ?></strong></p>
        <p>Submitted: <strong><?= date('M j, Y', strtotime($existing['submitted_at'])) ?></strong></p>
        <p>Status: <span class="badge badge-<?= $existing['status'] ?>"><?= ucfirst($existing['status']) ?></span></p>
        <?php if ($existing['admin_note']): ?><p>Note: <?= sanitize($existing['admin_note']) ?></p><?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
