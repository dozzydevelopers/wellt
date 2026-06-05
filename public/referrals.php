<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Referrals';

$referrals = fetchAll("SELECT u.username, u.full_name, u.created_at, u.total_deposited FROM users u WHERE u.referred_by = ? ORDER BY u.created_at DESC", [$user['id']]);
$refEarnings = fetchOne("SELECT SUM(amount) as total FROM transactions WHERE user_id=? AND type='referral'", [$user['id']]);
include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">Referrals &amp; Earnings</h2>

      <div class="stats-row">
        <div class="stat-box"><div class="stat-val"><?= count($referrals) ?></div><div class="stat-label">Total Referrals</div></div>
        <div class="stat-box"><div class="stat-val positive"><?= formatMoney($refEarnings['total'] ?? 0) ?></div><div class="stat-label">Total Earned</div></div>
        <div class="stat-box"><div class="stat-val"><?= $user['referral_id'] ?></div><div class="stat-label">Your Referral ID</div></div>
      </div>

      <div class="card">
        <label><strong>Your Referral Link:</strong></label>
        <div class="ref-link-box">
          <input type="text" id="refLink" readonly value="<?= SITE_URL ?>/public/register.php?ref=<?= $user['referral_id'] ?>">
          <button onclick="copyRef()" class="btn btn-sm btn-copy">&#128203; Copy</button>
        </div>
      </div>

      <div class="card mt-4">
        <h3>Referred Users (<?= count($referrals) ?>)</h3>
        <table class="data-table">
          <thead><tr><th>Username</th><th>Full Name</th><th>Joined</th><th>Total Deposited</th></tr></thead>
          <tbody>
          <?php foreach ($referrals as $r): ?>
          <tr>
            <td><?= sanitize($r['username']) ?></td>
            <td><?= sanitize($r['full_name']) ?></td>
            <td><?= date('M j, Y', strtotime($r['created_at'])) ?></td>
            <td><?= formatMoney($r['total_deposited']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($referrals)): ?><tr><td colspan="4" class="empty-row">No referrals yet</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script>
function copyRef() {
  var el = document.getElementById('refLink');
  el.select(); document.execCommand('copy');
  alert('Referral link copied!');
}
</script>
<?php include '../includes/footer.php'; ?>
