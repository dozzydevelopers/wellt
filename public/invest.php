<?php
require_once '../includes/auth.php';
requireLogin();
requirePinSetup();
$user = getCurrentUser();
$pageTitle = 'Investment Plans';

$success = '';
$error   = '';

$plans = fetchAll("SELECT * FROM plans WHERE status='active' ORDER BY min_deposit ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $planId = intval($_POST['plan_id'] ?? 0);
    $amount = floatval($_POST['amount'] ?? 0);
    $txPin  = $_POST['transaction_pin'] ?? '';

    $plan = fetchOne("SELECT * FROM plans WHERE id=? AND status='active'", [$planId]);

    if (!verifyPin($user['transaction_pin'], $txPin)) {
        $error = 'Invalid transaction PIN. Please try again.';
    } elseif (!$plan) {
        $error = "Invalid plan selected.";
    } elseif ($amount < $plan['min_deposit']) {
        $error = "Minimum investment for this plan is " . formatMoney($plan['min_deposit']);
    } elseif ($amount > $plan['max_deposit']) {
        $error = "Maximum investment for this plan is " . formatMoney($plan['max_deposit']);
    } elseif ($amount > $user['balance']) {
        $error = "Insufficient balance. Please deposit first.";
    } else {
        $endDate     = date('Y-m-d H:i:s', strtotime("+{$plan['duration_days']} days"));
        $dailyProfit = $amount * ($plan['daily_roi'] / 100);

        query("UPDATE users SET balance = balance - ?, portfolio = portfolio + ? WHERE id = ?",
            [$amount, $amount, $user['id']]);
        $invId = insert("INSERT INTO investments (user_id, plan_id, amount, daily_profit, end_date) VALUES (?,?,?,?,?)",
            [$user['id'], $planId, $amount, $dailyProfit, $endDate]);
        $ref = generateReference();
        insert("INSERT INTO transactions (user_id, type, amount, description, status, reference) VALUES (?,?,?,?,?,?)",
            [$user['id'], 'deposit', $amount, "Investment in {$plan['name']}", 'completed', $ref]);

        sendNotification($user['id'], 'Investment Activated', "Your {$plan['name']} investment of " . formatMoney($amount) . " is now active!", 'success');
        $invData = fetchOne("SELECT * FROM investments WHERE id=?", [$invId]);
        $user = getCurrentUser();
        if ($invData) sendInvestmentStartedEmail($user, $invData, $plan);
        $success = "Investment successful! Your {$plan['name']} plan is now active.";
    }
}

// Helper: duration label
function planDurLabel(int $days): string {
    return $days === 1 ? '24 Hours' : $days . ' Days';
}
function planMaxLabel(float $max): string {
    return $max >= 9000000 ? 'Unlimited' : '$' . number_format($max);
}

include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">

      <h2 class="page-title">Investment Plans</h2>
      <p class="page-sub">Your Balance: <strong class="positive"><?= formatMoney($user['balance']) ?></strong> &nbsp;·&nbsp; Choose a plan to start earning</p>

      <?php if ($success): ?><div class="alert alert-success" style="animation:slideDown .4s both"><?= $success ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-error" style="animation:slideDown .4s both"><?= sanitize($error) ?></div><?php endif; ?>

      <!-- Plans Grid -->
      <div class="invest-plans-grid">
        <?php foreach ($plans as $idx => $plan):
          $totalRoi   = $plan['daily_roi'] * $plan['duration_days'];
          $durLabel   = planDurLabel($plan['duration_days']);
          $maxLabel   = planMaxLabel($plan['max_deposit']);
          $refPct     = $plan['referral_percent'] ?? 3.0;
          $popular    = ($idx === 1); // GROWTH plan is most popular
        ?>
        <div class="invest-plan-card<?= $popular ? ' popular' : '' ?>" style="--pc:<?= $plan['color'] ?>;animation:fadeInUp .45s <?= $idx * .1 ?>s both">
          <?php if ($popular): ?><div class="popular-badge">⭐ MOST POPULAR</div><?php endif; ?>
          <div class="ipc-top">
            <div class="ipc-name"><?= sanitize($plan['name']) ?></div>
            <div class="ipc-roi" style="color:<?= $plan['color'] ?>"><?= $totalRoi ?>%</div>
            <div class="ipc-period">ROI / <?= $durLabel ?></div>
          </div>
          <div class="ipc-divider" style="background:<?= $plan['color'] ?>"></div>
          <ul class="ipc-features">
            <li><span class="feat-dot" style="background:<?= $plan['color'] ?>"></span>
              <strong>Investment:</strong> $<?= number_format($plan['min_deposit']) ?> – <?= $maxLabel ?></li>
            <li><span class="feat-dot" style="background:<?= $plan['color'] ?>"></span>
              <strong>Duration:</strong> <?= $durLabel ?></li>
            <li><span class="feat-dot" style="background:<?= $plan['color'] ?>"></span>
              <strong>Referral Bonus:</strong> <?= $refPct ?>%</li>
            <li><span class="feat-dot" style="background:<?= $plan['color'] ?>"></span>
              <strong>24/7 Support:</strong> YES</li>
          </ul>
          <button class="btn ipc-btn" style="background:<?= $plan['color'] ?>"
            onclick="openInvestModal(<?= $plan['id'] ?>, <?= json_encode($plan['name']) ?>, <?= $plan['min_deposit'] ?>, <?= $plan['max_deposit'] ?>, <?= $totalRoi ?>, '<?= $durLabel ?>', '<?= $plan['color'] ?>')">
            Invest Now &#8594;
          </button>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Active Investments -->
      <?php $activeInv = fetchAll("SELECT i.*,p.name as plan_name,p.daily_roi,p.duration_days FROM investments i JOIN plans p ON i.plan_id=p.id WHERE i.user_id=? AND i.status='active'", [$user['id']]); ?>
      <?php if ($activeInv): ?>
      <div class="section-header mt-4"><h3>Your Active Investments</h3></div>
      <div class="table-card">
        <table class="data-table">
          <thead><tr><th>Plan</th><th>Amount</th><th>Daily Profit</th><th>End Date</th><th>Status</th></tr></thead>
          <tbody>
          <?php foreach ($activeInv as $inv): ?>
          <tr>
            <td><?= sanitize($inv['plan_name']) ?></td>
            <td><?= formatMoney($inv['amount']) ?></td>
            <td class="positive">+<?= formatMoney($inv['daily_profit']) ?></td>
            <td><?= date('M j, Y', strtotime($inv['end_date'])) ?></td>
            <td><span class="badge badge-approved">Active</span></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<!-- Invest Modal -->
<div class="modal" id="investModal">
  <div class="modal-content" style="max-width:420px">
    <div class="modal-header">
      <h3 id="modalPlanName">Invest</h3>
      <button onclick="closeInvModal()">&times;</button>
    </div>
    <form method="POST" id="investForm">
      <input type="hidden" name="plan_id" id="modalPlanId">
      <input type="hidden" name="transaction_pin" value="">
      <div class="plan-detail-row"><span>Total ROI:</span><span id="modalTotalRoi" class="positive" style="font-weight:800;font-size:18px"></span></div>
      <div class="plan-detail-row"><span>Duration:</span><span id="modalDuration"></span></div>
      <div class="plan-detail-row"><span>Investment Range:</span><span id="modalRange"></span></div>
      <div class="plan-detail-row"><span>Account Balance:</span><span class="positive"><?= formatMoney($user['balance']) ?></span></div>
      <div class="form-group mt-3">
        <label>Amount (USD)</label>
        <div class="input-wrap"><span class="input-icon">$</span>
        <input type="number" name="amount" id="modalAmount" step="0.01" required></div>
        <small id="modalRangeHint"></small>
      </div>
      <button type="button" class="btn btn-primary btn-full" onclick="showPinModal('investForm')">🔐 Confirm &amp; Invest</button>
    </form>
  </div>
</div>
<div class="modal-overlay" id="modalOverlay" onclick="closeInvModal()"></div>

<?php include '../includes/pin-modal.php'; ?>

<style>
.invest-plans-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:20px;margin-bottom:32px;}
.invest-plan-card{background:#fff;border-radius:18px;padding:24px 20px;border:2px solid #E2E8F0;position:relative;overflow:hidden;transition:all .25s;display:flex;flex-direction:column;gap:0;}
.invest-plan-card:hover{border-color:var(--pc);box-shadow:0 12px 40px rgba(0,0,0,.1);transform:translateY(-4px);}
.invest-plan-card.popular{border-color:var(--pc);box-shadow:0 8px 32px rgba(0,0,0,.12);}
.popular-badge{position:absolute;top:12px;right:12px;background:var(--pc);color:#fff;font-size:10px;font-weight:800;padding:3px 8px;border-radius:20px;letter-spacing:.5px;}
.ipc-top{margin-bottom:12px;}
.ipc-name{font-size:13px;font-weight:700;letter-spacing:1.5px;color:#64748B;text-transform:uppercase;margin-bottom:6px;}
.ipc-roi{font-size:42px;font-weight:900;line-height:1;}
.ipc-period{font-size:13px;color:#94A3B8;margin-top:2px;}
.ipc-divider{height:3px;border-radius:2px;margin:14px 0;opacity:.35;}
.ipc-features{list-style:none;margin:0 0 18px;padding:0;display:flex;flex-direction:column;gap:8px;}
.ipc-features li{font-size:13px;color:#475569;display:flex;align-items:center;gap:8px;}
.feat-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
.ipc-btn{border:none;color:#fff;width:100%;padding:12px;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;margin-top:auto;transition:filter .15s,transform .15s;}
.ipc-btn:hover{filter:brightness(1.1);transform:translateY(-1px);}
</style>

<script>
function openInvestModal(id, name, min, max, totalRoi, dur, color) {
  document.getElementById('modalPlanId').value  = id;
  document.getElementById('modalPlanName').textContent = 'Invest in ' + name;
  document.getElementById('modalTotalRoi').textContent = totalRoi + '%';
  document.getElementById('modalTotalRoi').style.color = color;
  document.getElementById('modalDuration').textContent = dur;
  var maxLbl = max >= 9000000 ? 'Unlimited' : '$' + Number(max).toLocaleString();
  document.getElementById('modalRange').textContent = '$' + Number(min).toLocaleString() + ' – ' + maxLbl;
  document.getElementById('modalAmount').min = min;
  document.getElementById('modalAmount').max = max >= 9000000 ? 9999999 : max;
  document.getElementById('modalRangeHint').textContent = 'Min: $' + Number(min).toLocaleString() + (max < 9000000 ? ' | Max: $' + Number(max).toLocaleString() : '');
  document.getElementById('investModal').style.display = 'flex';
  document.getElementById('modalOverlay').style.display = 'block';
}
function closeInvModal() {
  document.getElementById('investModal').style.display = 'none';
  document.getElementById('modalOverlay').style.display = 'none';
}
</script>
<?php include '../includes/footer.php'; ?>
