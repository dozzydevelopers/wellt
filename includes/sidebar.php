<?php $cur = basename($_SERVER['PHP_SELF']); $b = SITE_BASE; ?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo">
      <span class="logo-bar-sm"></span>
      <span class="sidebar-logo-text">WELTHFLOW</span>
    </div>
    <button class="sidebar-close" onclick="toggleSidebar()">&#10005;</button>
  </div>

  <div class="sidebar-user">
    <div class="sidebar-avatar"><?= strtoupper(substr($user['full_name'],0,1)) ?></div>
    <div>
      <div class="sidebar-username"><?= strtoupper(sanitize($user['full_name'])) ?></div>
      <div class="sidebar-balance">&#128200; <?= formatMoney($user['balance']) ?></div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <a href="<?= $b ?>/dashboard.php" class="nav-item <?= $cur=='dashboard.php'?'active':'' ?>">
      <span class="nav-icon">&#127968;</span><span>Home</span>
    </a>
    <a href="<?= $b ?>/deposit.php" class="nav-item <?= $cur=='deposit.php'?'active':'' ?>">
      <span class="nav-icon">&#8595;</span><span>Deposit</span>
    </a>
    <a href="<?= $b ?>/withdraw.php" class="nav-item <?= $cur=='withdraw.php'?'active':'' ?>">
      <span class="nav-icon">&#8593;</span><span>Withdraw</span>
    </a>
    <a href="<?= $b ?>/profit-history.php" class="nav-item <?= $cur=='profit-history.php'?'active':'' ?>">
      <span class="nav-icon">&#128337;</span><span>Profit History</span>
    </a>
    <a href="<?= $b ?>/transactions.php" class="nav-item <?= $cur=='transactions.php'?'active':'' ?>">
      <span class="nav-icon">&#128203;</span><span>Transactions</span>
    </a>
    <a href="<?= $b ?>/swap.php" class="nav-item <?= $cur=='swap.php'?'active':'' ?>">
      <span class="nav-icon">&#8646;</span><span>Swap Crypto</span>
    </a>
    <a href="<?= $b ?>/profile.php" class="nav-item <?= $cur=='profile.php'?'active':'' ?>">
      <span class="nav-icon">&#128100;</span><span>Profile</span>
    </a>
    <a href="<?= $b ?>/invest.php" class="nav-item <?= $cur=='invest.php'?'active':'' ?>">
      <span class="nav-icon">&#128200;</span><span>Investment Plans</span>
    </a>
    <a href="<?= $b ?>/my-plans.php" class="nav-item <?= $cur=='my-plans.php'?'active':'' ?>">
      <span class="nav-icon">&#128196;</span><span>My Plans</span>
    </a>
    <a href="<?= $b ?>/trade.php" class="nav-item <?= $cur=='trade.php'?'active':'' ?>">
      <span class="nav-icon">&#128201;</span><span>Binary Trade</span>
    </a>
    <a href="<?= $b ?>/trade-history.php" class="nav-item <?= $cur=='trade-history.php'?'active':'' ?>">
      <span class="nav-icon">&#128337;</span><span>Trade History</span>
    </a>
    <a href="<?= $b ?>/copy-trading.php" class="nav-item <?= $cur=='copy-trading.php'?'active':'' ?>">
      <span class="nav-icon">&#128101;</span><span>Copy Trading</span>
    </a>
    <a href="<?= $b ?>/copy-history.php" class="nav-item <?= $cur=='copy-history.php'?'active':'' ?>">
      <span class="nav-icon">&#128337;</span><span>Copy History</span>
    </a>
    <a href="<?= $b ?>/active-copies.php" class="nav-item <?= $cur=='active-copies.php'?'active':'' ?>">
      <span class="nav-icon">&#9654;</span><span>Active Copies</span>
    </a>
    <a href="<?= $b ?>/stocks.php" class="nav-item <?= $cur=='stocks.php'?'active':'' ?>">
      <span class="nav-icon">&#128200;</span><span>Stocks</span>
    </a>
    <a href="<?= $b ?>/p2p-trading.php" class="nav-item <?= $cur=='p2p-trading.php'?'active':'' ?>">
      <span class="nav-icon">&#128188;</span><span>P2P Trading</span>
    </a>
    <a href="<?= $b ?>/stock-history.php" class="nav-item <?= $cur=='stock-history.php'?'active':'' ?>">
      <span class="nav-icon">&#128337;</span><span>Stock History</span>
    </a>
    <a href="<?= $b ?>/referrals.php" class="nav-item <?= $cur=='referrals.php'?'active':'' ?>">
      <span class="nav-icon">&#128101;</span><span>Referrals</span>
    </a>
    <a href="<?= $b ?>/support.php" class="nav-item <?= $cur=='support.php'?'active':'' ?>">
      <span class="nav-icon">&#128172;</span><span>Support</span>
    </a>
    <a href="<?= $b ?>/kyc.php" class="nav-item <?= $cur=='kyc.php'?'active':'' ?>">
      <span class="nav-icon">&#128100;</span><span>KYC Verification</span>
    </a>
  </nav>

  <div class="sidebar-help">
    <div class="help-box">
      <p><strong>Need Help?</strong></p>
      <p>Contact our 24/7 customer support center.</p>
      <a href="<?= $b ?>/support.php" class="btn btn-help">Contact Us</a>
    </div>
  </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
