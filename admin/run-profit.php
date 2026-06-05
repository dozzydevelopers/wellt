<?php
require_once '../includes/auth.php';
requireAdmin();
$pageTitle = 'Run Daily Profit';
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_profit'])) {
  $count = 0;
  $totalPaid = 0;
  $investments = fetchAll("SELECT i.*, p.daily_roi, p.name as plan_name FROM investments i JOIN plans p ON i.plan_id=p.id WHERE i.status='active' AND (i.last_profit_date IS NULL OR i.last_profit_date < date('now'))");
  foreach ($investments as $inv) {
    if (strtotime($inv['end_date']) < time()) {
      query("UPDATE investments SET status='completed' WHERE id=?", [$inv['id']]);
      query("UPDATE users SET portfolio=portfolio-? WHERE id=?", [$inv['amount'], $inv['user_id']]);
      sendNotification($inv['user_id'], 'Investment Completed', "Your {$inv['plan_name']} investment has completed!", 'success');
      continue;
    }
    $profit = $inv['daily_profit'];
    query("UPDATE investments SET total_profit=total_profit+?, last_profit_date=date('now') WHERE id=?", [$profit, $inv['id']]);
    query("UPDATE users SET balance=balance+?, total_profit=total_profit+? WHERE id=?", [$profit, $profit, $inv['user_id']]);
    $ref = generateReference();
    insert(
      "INSERT INTO transactions (user_id,type,amount,description,status,reference) VALUES (?,?,?,?,?,?)",
      [$inv['user_id'], 'profit', $profit, "Daily profit from {$inv['plan_name']}", 'completed', $ref]
    );
    sendNotification($inv['user_id'], 'Daily Profit Credited', "Your daily profit of " . formatMoney($profit) . " from {$inv['plan_name']} has been credited!", 'success');
    $count++;
    $totalPaid += $profit;
  }
  $result = "Profit run complete! Credited to $count investments. Total paid: " . formatMoney($totalPaid);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Run Profit - Admin</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
  <?php include 'includes/admin-sidebar.php'; ?>
  <div class="admin-main">
    <?php include 'includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <h2 class="admin-page-title">Run Daily Profit</h2>
      <?php if ($result): ?>
        <div class="alert alert-success"><?= $result ?></div><?php endif; ?>
      <div class="admin-card" style="max-width:500px">
        <p>This will credit daily profits to all active investments that haven't received profit today.</p>
        <p>For automation, set up a cron job hitting <code>/public/cron-profit.php?key=YOUR_KEY</code></p>
        <form method="POST">
          <button type="submit" name="run_profit" class="btn btn-primary btn-lg"
            onclick="return confirm('Run daily profit for all active investments?')">&#128183; Run Daily Profit
            Now</button>
        </form>
      </div>
      <div class="admin-card mt-4">
        <h3>Active Investments Summary</h3>
        <?php
        $summary = fetchAll("SELECT p.name, COUNT(i.id) as count, SUM(i.amount) as total_invested, SUM(i.daily_profit) as daily_payout FROM investments i JOIN plans p ON i.plan_id=p.id WHERE i.status='active' GROUP BY p.id, p.name");
        ?>
        <table class="admin-table">
          <thead>
            <tr>
              <th>Plan</th>
              <th>Count</th>
              <th>Total Invested</th>
              <th>Daily Payout</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($summary as $s): ?>
              <tr>
                <td><?= sanitize($s['name']) ?></td>
                <td><?= $s['count'] ?></td>
                <td><?= formatMoney($s['total_invested']) ?></td>
                <td class="positive"><?= formatMoney($s['daily_payout']) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($summary)): ?>
              <tr>
                <td colspan="4" class="empty-row">No active investments</td>
              </tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>

</html>