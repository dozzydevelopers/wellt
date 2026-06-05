<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Transactions';

$filter = sanitize($_GET['type'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$where = "user_id = ?";
$params = [$user['id']];
if ($filter) { $where .= " AND type = ?"; $params[] = $filter; }

$total = fetchOne("SELECT COUNT(*) as c FROM transactions WHERE $where", $params)['c'];
$txns = fetchAll("SELECT * FROM transactions WHERE $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset", $params);
$pages = ceil($total / $perPage);

include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">Transactions</h2>

      <div class="filter-bar">
        <?php foreach ([''=>'All','deposit'=>'Deposits','withdrawal'=>'Withdrawals','profit'=>'Profits','bonus'=>'Bonus','referral'=>'Referrals'] as $k=>$v): ?>
        <a href="?type=<?= $k ?>" class="filter-chip <?= $filter===$k?'active':'' ?>"><?= $v ?></a>
        <?php endforeach; ?>
      </div>

      <div class="table-card">
        <table class="data-table">
          <thead><tr><th>Date</th><th>Type</th><th>Description</th><th>Amount</th><th>Reference</th><th>Status</th></tr></thead>
          <tbody>
          <?php foreach ($txns as $t): ?>
          <tr>
            <td><?= date('M j, Y H:i', strtotime($t['created_at'])) ?></td>
            <td class="tx-type"><?= ucfirst($t['type']) ?></td>
            <td><?= sanitize($t['description'] ?? '-') ?></td>
            <td class="<?= in_array($t['type'],['deposit','profit','bonus','referral'])?'positive':'negative' ?>">
              <?= in_array($t['type'],['deposit','profit','bonus','referral'])?'+':'-' ?><?= formatMoney($t['amount']) ?>
            </td>
            <td><small><?= sanitize($t['reference']) ?></small></td>
            <td><span class="badge badge-<?= $t['status'] ?>"><?= ucfirst($t['status']) ?></span></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($txns)): ?><tr><td colspan="6" class="empty-row">No transactions found</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($pages > 1): ?>
      <div class="pagination">
        <?php for ($i=1; $i<=$pages; $i++): ?>
        <a href="?type=<?= $filter ?>&page=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
        <?php endfor; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
