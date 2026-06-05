<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
if (!$user) {
  logoutUser();
  exit;
}

$copyTraders = fetchAll("SELECT * FROM copy_traders WHERE status='active' ORDER BY roi DESC LIMIT 4");
$recentTxns = fetchAll("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5", [$user['id']]);
$activeInvests = fetchAll("SELECT i.*, p.name as plan_name, p.daily_roi FROM investments i JOIN plans p ON i.plan_id=p.id WHERE i.user_id=? AND i.status='active'", [$user['id']]);
$unreadNotifs = fetchOne("SELECT COUNT(*) as cnt FROM notifications WHERE user_id=? AND is_read=0", [$user['id']]);
$referrals = fetchOne("SELECT COUNT(*) as cnt FROM users WHERE referred_by = ?", [$user['id']]);
$totalDeposits = fetchOne("SELECT COUNT(*) as cnt FROM transactions WHERE user_id=? AND type='deposit' AND status='approved'", [$user['id']]);
$activePlans = fetchAll("SELECT * FROM plans WHERE status='active' ORDER BY min_deposit ASC");

include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">

      <!-- Welcome Banner -->
      <div class="welcome-banner">
        <p class="welcome-text">Welcome, <?= strtoupper(sanitize($user['full_name'])) ?>!</p>
        <p class="welcome-sub">Here's your account overview</p>
      </div>

      <!-- Announcement -->
      <div class="announcement-bar">
        <span class="annc-icon">&#128276;</span>
        <span>Welcome To Welthflow</span>
        <button class="annc-close" onclick="this.parentElement.style.display='none'">&times;</button>
      </div>

      <!-- Security Status Bar -->
      <div class="sec-status-bar">
        <div class="ssb-left">
          <span class="ssb-shield">🛡️</span>
          <div>
            <div class="ssb-title">Account Security</div>
            <div class="ssb-sub">
              <?php
              $secLevel = $user['security_level'] ?? 'basic';
              $levels = ['basic' => 'Basic', 'pin' => 'PIN Protected', 'biometric' => 'Maximum — Biometric Active'];
              echo $levels[$secLevel] ?? 'Basic';
              ?>
            </div>
          </div>
        </div>
        <div class="ssb-badges">
          <span class="ssb-badge <?= !empty($user['transaction_pin']) ? 'ok' : 'off' ?>">
            🔐 PIN <?= !empty($user['transaction_pin']) ? '✓' : '—' ?>
          </span>
          <span class="ssb-badge <?= !empty($user['face_verified']) ? 'ok' : 'off' ?>">
            👤 Face ID <?= !empty($user['face_verified']) ? '✓' : '—' ?>
          </span>
          <span class="ssb-badge <?= !empty($user['fingerprint_verified']) ? 'ok' : 'off' ?>">
            ☝️ Fingerprint <?= !empty($user['fingerprint_verified']) ? '✓' : '—' ?>
          </span>
        </div>
        <?php if (empty($user['biometric_enabled'])): ?>
          <a href="<?= SITE_BASE ?>/biometric-setup.php" class="ssb-setup-btn">Enable Biometrics →</a>
        <?php else: ?>
          <span class="ssb-max-badge">🔒 MAXIMUM SECURITY</span>
        <?php endif; ?>
      </div>

      <!-- Balance & Stats Grid -->
      <div class="stats-grid">
        <div class="balance-card">
          <div class="balance-top">
            <span class="balance-label">TOTAL BALANCE</span>
            <span class="badge-active">Active</span>
          </div>
          <div class="balance-amount"><?= formatMoney($user['balance']) ?></div>
          <div class="balance-actions">
            <a href="<?= SITE_BASE ?>/deposit.php" class="btn btn-sm btn-white">+ Deposit</a>
            <a href="<?= SITE_BASE ?>/withdraw.php" class="btn btn-sm btn-outline-white">&#8593; Withdraw</a>
          </div>
          <div class="balance-stats">
            <div class="stat-item"><span class="stat-label">PORTFOLIO</span><span
                class="stat-val"><?= formatMoney($user['portfolio']) ?></span></div>
            <div class="stat-item"><span class="stat-label">PROFIT</span><span
                class="stat-val positive">+<?= formatMoney($user['total_profit']) ?></span></div>
            <div class="stat-item"><span class="stat-label">BONUS</span><span
                class="stat-val"><?= formatMoney($user['bonus']) ?></span></div>
            <div class="stat-item"><span class="stat-label">DEPOSITS</span><span
                class="stat-val"><?= formatMoney($user['total_deposited']) ?></span></div>
            <div class="stat-item"><span class="stat-label">WITHDRAWN</span><span
                class="stat-val"><?= formatMoney($user['total_withdrawn']) ?></span></div>
          </div>
        </div>

        <div class="side-cards">
          <div class="mini-card">
            <div class="mini-card-top">
              <div class="mini-icon ref-icon">&#128101;</div>
              <a href="<?= SITE_BASE ?>/referrals.php" class="view-link">View &#8594;</a>
            </div>
            <div class="mini-amount"><?= formatMoney(0) ?></div>
            <div class="mini-label">Referral Bonus</div>
            <div class="mini-stat"><span class="dot-green">&#9679;</span> <?= $referrals['cnt'] ?? 0 ?> referrals</div>
          </div>
          <div class="mini-card">
            <div class="mini-card-top">
              <div class="mini-icon dep-icon">&#128200;</div>
              <a href="<?= SITE_BASE ?>/deposit.php" class="link-orange">Deposit &#8594;</a>
            </div>
            <div class="mini-amount"><?= formatMoney($user['total_deposited']) ?></div>
            <div class="mini-label">Total Deposit</div>
            <div class="mini-stat"><span class="dot-green">&#9679;</span> <?= $totalDeposits['cnt'] ?? 0 ?> deposits
            </div>
          </div>
        </div>
      </div>

      <!-- Top Copy Traders -->
      <div class="section-header">
        <h3>Top Copy Traders <span class="auto-badge">⟳ Auto-Updated Daily</span></h3>
        <a href="<?= SITE_BASE ?>/copy-trading.php" class="view-all-link">View all &#8594;</a>
      </div>
      <div class="traders-grid">
        <?php foreach ($copyTraders as $trader): ?>
          <div class="trader-card">
            <div class="trader-info">
              <div class="trader-photo-wrap">
                <?php if (!empty($trader['photo_url'])): ?>
                  <img src="<?= sanitize($trader['photo_url']) ?>" alt="<?= sanitize($trader['name']) ?>"
                    class="trader-photo">
                <?php else: ?>
                  <div class="trader-avatar"><?= strtoupper(substr($trader['name'], 0, 1)) ?></div>
                <?php endif; ?>
                <span class="trader-online-dot"></span>
              </div>
              <div>
                <div class="trader-name"><?= sanitize($trader['name']) ?></div>
                <div class="trader-followers"><?= number_format($trader['followers']) ?> followers</div>
              </div>
            </div>
            <div class="trader-stats">
              <div class="trader-stat"><span class="stat-key">ROI</span><span
                  class="stat-val positive"><?= $trader['roi'] ?>%</span></div>
              <div class="trader-stat"><span class="stat-key">Profit</span><span
                  class="stat-val positive">+<?= $trader['profit_percent'] ?>%</span></div>
            </div>
            <div class="trader-footer">
              <span>&#128337; <?= sanitize($trader['period']) ?></span>
              <span>&#128178; <?= $trader['fee_percent'] ?>% fee</span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Investment Plans Preview -->
      <div class="section-header">
        <h3>Investment Plans</h3>
        <a href="<?= SITE_BASE ?>/invest.php" class="view-all-link">View all &amp; Invest &#8594;</a>
      </div>
      <div class="dash-plans-grid">
        <?php foreach ($activePlans as $plan):
          $totalRoi = $plan['daily_roi'] * $plan['duration_days'];
          $durLabel = $plan['duration_days'] === 1 ? '24 Hours' : $plan['duration_days'] . ' Days';
          $refPct = $plan['referral_percent'] ?? 3.0;
          $maxLabel = $plan['max_deposit'] >= 9000000 ? 'Unlimited' : '$' . number_format($plan['max_deposit']);
          ?>
          <div class="dash-plan-card"
            style="--pc:<?= $plan['color'] ?>;animation:fadeInUp .45s <?= array_search($plan, $activePlans) * .08 ?>s both">
            <div class="dpc-header" style="background:<?= $plan['color'] ?>">
              <div class="dpc-name"><?= sanitize($plan['name']) ?></div>
              <div class="dpc-roi"><?= $totalRoi ?>% ROI</div>
              <div class="dpc-period">/ <?= $durLabel ?></div>
            </div>
            <div class="dpc-body">
              <div class="dpc-row"><span>Investment</span><strong>$<?= number_format($plan['min_deposit']) ?> –
                  <?= $maxLabel ?></strong></div>
              <div class="dpc-row"><span>Duration</span><strong><?= $durLabel ?></strong></div>
              <div class="dpc-row"><span>Referral</span><strong
                  style="color:<?= $plan['color'] ?>"><?= $refPct ?>%</strong></div>
              <div class="dpc-row"><span>Support</span><strong>24/7 ✓</strong></div>
            </div>
            <a href="<?= SITE_BASE ?>/invest.php" class="dpc-btn" style="background:<?= $plan['color'] ?>">Invest Now</a>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Recent Transactions -->
      <div class="section-header">
        <h3>Recent Transactions (<?= count($recentTxns) ?>)</h3>
        <a href="<?= SITE_BASE ?>/transactions.php" class="view-all-link">View all &#8594;</a>
      </div>
      <div class="table-card">
        <table class="data-table">
          <thead>
            <tr>
              <th>DATE</th>
              <th>TYPE</th>
              <th>AMOUNT</th>
              <th>STATUS</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($recentTxns)): ?>
              <tr>
                <td colspan="4" class="empty-row">No transactions yet</td>
              </tr>
            <?php else: ?>
              <?php foreach ($recentTxns as $tx): ?>
                <tr>
                  <td><?= date('M j, Y', strtotime($tx['created_at'])) ?></td>
                  <td class="tx-type"><?= ucfirst($tx['type']) ?></td>
                  <td
                    class="<?= in_array($tx['type'], ['deposit', 'profit', 'bonus', 'referral']) ? 'positive' : 'negative' ?>">
                    <?= in_array($tx['type'], ['deposit', 'profit', 'bonus', 'referral']) ? '+' : '-' ?>    <?= formatMoney($tx['amount']) ?>
                  </td>
                  <td><span class="badge badge-<?= $tx['status'] ?>"><?= ucfirst($tx['status']) ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Referral Link -->
      <div class="referral-section">
        <h3>Refer Us &amp; Earn</h3>
        <p class="ref-sub">Use the link below to invite your friends:</p>
        <div class="ref-link-box">
          <input type="text" id="refLink" readonly
            value="<?= SITE_URL ?>/public/register.php?ref=<?= $user['referral_id'] ?>">
          <button onclick="copyRef()" class="btn btn-sm btn-copy">&#128203; Copy</button>
        </div>
      </div>

    </div>
  </div>
</div>

<style>
  /* Security Status Bar */
  .sec-status-bar {
    background: linear-gradient(135deg, #0F172A, #1E293B);
    border: 1px solid rgba(249, 115, 22, .2);
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    animation: fadeInUp .4s .05s both;
  }

  .ssb-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 160px;
  }

  .ssb-shield {
    font-size: 28px;
    filter: drop-shadow(0 0 8px rgba(249, 115, 22, .5));
  }

  .ssb-title {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1px;
    color: rgba(255, 255, 255, .5);
    text-transform: uppercase;
  }

  .ssb-sub {
    font-size: 13px;
    font-weight: 600;
    color: #F97316;
  }

  .ssb-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    flex: 1;
  }

  .ssb-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 5px 10px;
    border-radius: 20px;
    border: 1px solid;
  }

  .ssb-badge.ok {
    background: rgba(34, 197, 94, .1);
    border-color: rgba(34, 197, 94, .3);
    color: #86EFAC;
  }

  .ssb-badge.off {
    background: rgba(100, 116, 139, .08);
    border-color: rgba(100, 116, 139, .2);
    color: rgba(255, 255, 255, .3);
  }

  .ssb-setup-btn {
    background: linear-gradient(135deg, #F97316, #EF4444);
    color: #fff;
    text-decoration: none;
    font-size: 12px;
    font-weight: 700;
    padding: 8px 16px;
    border-radius: 20px;
    white-space: nowrap;
    transition: all .2s;
  }

  .ssb-setup-btn:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
    text-decoration: none;
  }

  .ssb-max-badge {
    background: rgba(34, 197, 94, .12);
    border: 1px solid rgba(34, 197, 94, .3);
    color: #86EFAC;
    font-size: 11px;
    font-weight: 800;
    padding: 8px 16px;
    border-radius: 20px;
    letter-spacing: 1px;
  }

  /* Trader photo */
  .trader-photo-wrap {
    position: relative;
    flex-shrink: 0;
  }

  .trader-photo {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #E2E8F0;
  }

  .trader-online-dot {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 10px;
    height: 10px;
    background: #22C55E;
    border-radius: 50%;
    border: 2px solid #fff;
  }

  .auto-badge {
    font-size: 11px;
    font-weight: 500;
    color: #94A3B8;
    background: #F1F5F9;
    border-radius: 20px;
    padding: 3px 8px;
    margin-left: 8px;
    vertical-align: middle;
  }

  /* Dashboard plan cards */
  .dash-plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
  }

  .dash-plan-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    border: 1.5px solid #E2E8F0;
    transition: all .25s;
  }

  .dash-plan-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, .1);
    border-color: var(--pc);
  }

  .dpc-header {
    padding: 16px;
    color: #fff;
    text-align: center;
  }

  .dpc-name {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    opacity: .85;
    margin-bottom: 4px;
  }

  .dpc-roi {
    font-size: 30px;
    font-weight: 900;
    line-height: 1;
  }

  .dpc-period {
    font-size: 12px;
    opacity: .8;
    margin-top: 2px;
  }

  .dpc-body {
    padding: 14px 16px;
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .dpc-row {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #475569;
  }

  .dpc-row strong {
    color: #0F172A;
  }

  .dpc-btn {
    display: block;
    text-align: center;
    background: var(--pc);
    color: #fff;
    padding: 10px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: filter .15s;
  }

  .dpc-btn:hover {
    filter: brightness(1.08);
    text-decoration: none;
  }

  @media(max-width:540px) {
    .dash-plans-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
</style>

<script>
  function copyRef() {
    var el = document.getElementById('refLink');
    el.select();
    navigator.clipboard ? navigator.clipboard.writeText(el.value) : document.execCommand('copy');
    alert('Referral link copied!');
  }
</script>
<script src="<?= assetUrl('assets/js/app.js') ?>"></script>
<?php include '../includes/footer.php'; ?>