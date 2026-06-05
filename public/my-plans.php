<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'My Plans';

$investments = fetchAll("SELECT i.*, p.name as plan_name, p.daily_roi, p.color FROM investments i JOIN plans p ON i.plan_id=p.id WHERE i.user_id=? ORDER BY i.start_date DESC", [$user['id']]);
include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">My Investment Plans</h2>
      <a href="<?= SITE_BASE ?>/invest.php" class="btn btn-primary mb-4">+ New Investment</a>

      <?php if (empty($investments)): ?>
        <div class="empty-state"><p>No investments yet. <a href="<?= SITE_BASE ?>/invest.php">Start investing now!</a></p></div>
      <?php else: ?>
      <div class="plans-grid">
        <?php foreach ($investments as $inv): ?>
        <div class="plan-card" style="border-top:4px solid <?= $inv['color'] ?>">
          <div class="plan-header">
            <h3><?= sanitize($inv['plan_name']) ?></h3>
            <span class="badge badge-<?= $inv['status'] ?>"><?= ucfirst($inv['status']) ?></span>
          </div>
          <div class="plan-detail-row"><span>Invested:</span><span><?= formatMoney($inv['amount']) ?></span></div>
          <div class="plan-detail-row"><span>Daily Profit:</span><span class="positive"><?= formatMoney($inv['daily_profit']) ?></span></div>
          <div class="plan-detail-row"><span>Total Profit:</span><span class="positive"><?= formatMoney($inv['total_profit']) ?></span></div>
          <div class="plan-detail-row"><span>ROI:</span><span><?= $inv['daily_roi'] ?>% Daily</span></div>
          <div class="plan-detail-row"><span>Start:</span><span><?= date('M j, Y', strtotime($inv['start_date'])) ?></span></div>
          <div class="plan-detail-row"><span>End:</span><span><?= date('M j, Y', strtotime($inv['end_date'])) ?></span></div>
          <?php
          $daysLeft = ceil((strtotime($inv['end_date']) - time()) / 86400);
          if ($inv['status'] === 'active'):
          ?>
          <div class="plan-progress">
            <div class="progress-bar">
              <?php
              $total = (strtotime($inv['end_date']) - strtotime($inv['start_date'])) / 86400;
              $elapsed = max(0, $total - max(0, $daysLeft));
              $pct = $total > 0 ? min(100, round($elapsed / $total * 100)) : 0;
              ?>
              <div class="progress-fill" style="width:<?= $pct ?>%"></div>
            </div>
            <small><?= max(0, $daysLeft) ?> day(s) remaining</small>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
