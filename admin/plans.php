<?php
require_once '../includes/auth.php';
requireAdmin();
$pageTitle = 'Manage Plans';

$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = sanitize($_POST['action'] ?? '');
  if ($action === 'create' || $action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $desc = sanitize($_POST['description'] ?? '');
    $roi = floatval($_POST['daily_roi'] ?? 0);
    $days = intval($_POST['duration_days'] ?? 1);
    $minDep = floatval($_POST['min_deposit'] ?? 0);
    $maxDep = floatval($_POST['max_deposit'] ?? 0);
    $maxRet = floatval($_POST['max_return_percent'] ?? 99);
    $status = sanitize($_POST['status'] ?? 'active');
    $color = sanitize($_POST['color'] ?? '#1E40AF');
    if ($action === 'create') {
      insert(
        "INSERT INTO plans (name,description,daily_roi,duration_days,min_deposit,max_deposit,max_return_percent,status,color) VALUES (?,?,?,?,?,?,?,?,?)",
        [$name, $desc, $roi, $days, $minDep, $maxDep, $maxRet, $status, $color]
      );
      $success = 'Plan created!';
    } else {
      query(
        "UPDATE plans SET name=?,description=?,daily_roi=?,duration_days=?,min_deposit=?,max_deposit=?,max_return_percent=?,status=?,color=? WHERE id=?",
        [$name, $desc, $roi, $days, $minDep, $maxDep, $maxRet, $status, $color, $id]
      );
      $success = 'Plan updated!';
    }
  } elseif ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    query("UPDATE plans SET status='inactive' WHERE id=?", [$id]);
    $success = 'Plan deactivated!';
  }
}

$plans = fetchAll("SELECT * FROM plans ORDER BY min_deposit ASC");
$editPlan = null;
if (isset($_GET['edit']))
  $editPlan = fetchOne("SELECT * FROM plans WHERE id=?", [intval($_GET['edit'])]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Plans - Admin</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
  <?php include 'includes/admin-sidebar.php'; ?>
  <div class="admin-main">
    <?php include 'includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <h2 class="admin-page-title">Manage Investment Plans</h2>
      <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div><?php endif; ?>

      <div class="admin-two-col">
        <div class="admin-card">
          <h3><?= $editPlan ? 'Edit Plan' : 'Create New Plan' ?></h3>
          <form method="POST">
            <input type="hidden" name="action" value="<?= $editPlan ? 'update' : 'create' ?>">
            <?php if ($editPlan): ?><input type="hidden" name="id" value="<?= $editPlan['id'] ?>"><?php endif; ?>
            <div class="form-group"><label>Plan Name</label><input type="text" name="name" required
                value="<?= sanitize($editPlan['name'] ?? '') ?>"></div>
            <div class="form-group"><label>Description</label><textarea
                name="description"><?= sanitize($editPlan['description'] ?? '') ?></textarea></div>
            <div class="form-row">
              <div class="form-group"><label>Daily ROI (%)</label><input type="number" name="daily_roi" step="0.01"
                  required value="<?= $editPlan['daily_roi'] ?? '' ?>"></div>
              <div class="form-group"><label>Duration (Days)</label><input type="number" name="duration_days" required
                  value="<?= $editPlan['duration_days'] ?? '' ?>"></div>
            </div>
            <div class="form-row">
              <div class="form-group"><label>Min Deposit ($)</label><input type="number" name="min_deposit" step="0.01"
                  required value="<?= $editPlan['min_deposit'] ?? '' ?>"></div>
              <div class="form-group"><label>Max Deposit ($)</label><input type="number" name="max_deposit" step="0.01"
                  required value="<?= $editPlan['max_deposit'] ?? '' ?>"></div>
            </div>
            <div class="form-row">
              <div class="form-group"><label>Max Return %</label><input type="number" name="max_return_percent"
                  step="0.01" value="<?= $editPlan['max_return_percent'] ?? 99 ?>"></div>
              <div class="form-group"><label>Color</label><input type="color" name="color"
                  value="<?= $editPlan['color'] ?? '#1E40AF' ?>"></div>
            </div>
            <div class="form-group"><label>Status</label>
              <select name="status">
                <option value="active" <?= ($editPlan['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($editPlan['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive
                </option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary"><?= $editPlan ? 'Update Plan' : 'Create Plan' ?></button>
            <?php if ($editPlan): ?><a href="plans.php" class="btn btn-outline">Cancel</a><?php endif; ?>
          </form>
        </div>

        <div class="admin-card">
          <h3>All Plans (<?= count($plans) ?>)</h3>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>ROI</th>
                <th>Range</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($plans as $p): ?>
                <tr>
                  <td><span style="color:<?= $p['color'] ?>">&#9632;</span> <?= sanitize($p['name']) ?></td>
                  <td><?= $p['daily_roi'] ?>%</td>
                  <td><?= formatMoney($p['min_deposit']) ?> - <?= formatMoney($p['max_deposit']) ?></td>
                  <td><span
                      class="badge badge-<?= $p['status'] === 'active' ? 'approved' : 'rejected' ?>"><?= ucfirst($p['status']) ?></span>
                  </td>
                  <td>
                    <a href="plans.php?edit=<?= $p['id'] ?>" class="btn-table">Edit</a>
                    <form method="POST" style="display:inline"><input type="hidden" name="action" value="delete"><input
                        type="hidden" name="id" value="<?= $p['id'] ?>"><button type="submit" class="btn-table red"
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