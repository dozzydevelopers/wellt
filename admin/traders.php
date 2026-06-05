<?php
require_once '../includes/auth.php';
requireAdmin();
$pageTitle = 'Copy Traders';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = sanitize($_POST['action'] ?? '');
  if ($action === 'create' || $action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $followers = intval($_POST['followers'] ?? 0);
    $roi = floatval($_POST['roi'] ?? 0);
    $profit = floatval($_POST['profit_percent'] ?? 0);
    $fee = floatval($_POST['fee_percent'] ?? 5);
    $period = sanitize($_POST['period'] ?? '1w');
    $bio = sanitize($_POST['bio'] ?? '');
    $status = sanitize($_POST['status'] ?? 'active');
    if ($action === 'create') {
      insert("INSERT INTO copy_traders (name,followers,roi,profit_percent,fee_percent,period,bio,status) VALUES (?,?,?,?,?,?,?,?)", [$name, $followers, $roi, $profit, $fee, $period, $bio, $status]);
      $success = 'Trader added!';
    } else {
      query("UPDATE copy_traders SET name=?,followers=?,roi=?,profit_percent=?,fee_percent=?,period=?,bio=?,status=? WHERE id=?", [$name, $followers, $roi, $profit, $fee, $period, $bio, $status, $id]);
      $success = 'Trader updated!';
    }
  } elseif ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    query("UPDATE copy_traders SET status='inactive' WHERE id=?", [$id]);
    $success = 'Trader deactivated!';
  }
}
$traders = fetchAll("SELECT * FROM copy_traders ORDER BY roi DESC");
$editT = null;
if (isset($_GET['edit']))
  $editT = fetchOne("SELECT * FROM copy_traders WHERE id=?", [intval($_GET['edit'])]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Traders - Admin</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
  <?php include 'includes/admin-sidebar.php'; ?>
  <div class="admin-main">
    <?php include 'includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <h2 class="admin-page-title">Manage Copy Traders</h2>
      <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div><?php endif; ?>
      <div class="admin-two-col">
        <div class="admin-card">
          <h3><?= $editT ? 'Edit' : 'Add' ?> Trader</h3>
          <form method="POST">
            <input type="hidden" name="action" value="<?= $editT ? 'update' : 'create' ?>">
            <?php if ($editT): ?><input type="hidden" name="id" value="<?= $editT['id'] ?>"><?php endif; ?>
            <div class="form-group"><label>Name</label><input type="text" name="name" required
                value="<?= sanitize($editT['name'] ?? '') ?>"></div>
            <div class="form-row">
              <div class="form-group"><label>Followers</label><input type="number" name="followers"
                  value="<?= $editT['followers'] ?? 0 ?>"></div>
              <div class="form-group"><label>ROI (%)</label><input type="number" name="roi" step="0.01" required
                  value="<?= $editT['roi'] ?? '' ?>"></div>
            </div>
            <div class="form-row">
              <div class="form-group"><label>Profit %</label><input type="number" name="profit_percent" step="0.01"
                  required value="<?= $editT['profit_percent'] ?? '' ?>"></div>
              <div class="form-group"><label>Fee %</label><input type="number" name="fee_percent" step="0.01"
                  value="<?= $editT['fee_percent'] ?? 5 ?>"></div>
            </div>
            <div class="form-row">
              <div class="form-group"><label>Period</label><input type="text" name="period" placeholder="3d, 1w, 1m"
                  value="<?= sanitize($editT['period'] ?? '1w') ?>"></div>
              <div class="form-group"><label>Status</label><select name="status">
                  <option value="active" <?= ($editT['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                  <option value="inactive" <?= ($editT['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select></div>
            </div>
            <div class="form-group"><label>Bio</label><textarea name="bio"><?= sanitize($editT['bio'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?= $editT ? 'Update' : 'Add Trader' ?></button>
            <?php if ($editT): ?><a href="traders.php" class="btn btn-outline">Cancel</a><?php endif; ?>
          </form>
        </div>
        <div class="admin-card">
          <h3>All Traders (<?= count($traders) ?>)</h3>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>ROI</th>
                <th>Followers</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($traders as $t): ?>
                <tr>
                  <td><?= sanitize($t['name']) ?></td>
                  <td class="positive"><?= $t['roi'] ?>%</td>
                  <td><?= number_format($t['followers']) ?></td>
                  <td><span
                      class="badge badge-<?= $t['status'] === 'active' ? 'approved' : 'rejected' ?>"><?= ucfirst($t['status']) ?></span>
                  </td>
                  <td>
                    <a href="traders.php?edit=<?= $t['id'] ?>" class="btn-table">Edit</a>
                    <form method="POST" style="display:inline"><input type="hidden" name="action" value="delete"><input
                        type="hidden" name="id" value="<?= $t['id'] ?>"><button type="submit" class="btn-table red"
                        onclick="return confirm('Deactivate?')">Off</button></form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>

</html>