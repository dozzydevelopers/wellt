<?php $cur = basename($_SERVER['PHP_SELF']); $b = SITE_BASE; ?>
<aside class="admin-sidebar" id="adminSidebar">
  <div class="admin-sidebar-logo">
    <span class="logo-bar-sm"></span>
    <div>
      <div class="admin-logo-text">WELTHFLOW</div>
      <div class="admin-logo-sub">ADMIN PANEL</div>
    </div>
  </div>
  <nav class="admin-nav">
    <div class="admin-nav-section">Dashboard</div>
    <a href="<?= $b ?>/admin/index.php" class="admin-nav-item <?= $cur=='index.php'?'active':'' ?>">&#128200; Overview</a>

    <div class="admin-nav-section">Users</div>
    <a href="<?= $b ?>/admin/users.php" class="admin-nav-item <?= $cur=='users.php'?'active':'' ?>">&#128101; All Users</a>
    <a href="<?= $b ?>/admin/kyc.php" class="admin-nav-item <?= $cur=='kyc.php'?'active':'' ?>">&#128100; KYC Requests</a>

    <div class="admin-nav-section">Finance</div>
    <a href="<?= $b ?>/admin/deposits.php" class="admin-nav-item <?= $cur=='deposits.php'?'active':'' ?>">&#8595; Deposits</a>
    <a href="<?= $b ?>/admin/withdrawals.php" class="admin-nav-item <?= $cur=='withdrawals.php'?'active':'' ?>">&#8593; Withdrawals</a>
    <a href="<?= $b ?>/admin/transactions.php" class="admin-nav-item <?= $cur=='transactions.php'?'active':'' ?>">&#128203; All Transactions</a>
    <a href="<?= $b ?>/admin/add-funds.php" class="admin-nav-item <?= $cur=='add-funds.php'?'active':'' ?>">&#43; Add/Deduct Funds</a>

    <div class="admin-nav-section">Investments</div>
    <a href="<?= $b ?>/admin/investments.php" class="admin-nav-item <?= $cur=='investments.php'?'active':'' ?>">&#128200; Investments</a>
    <a href="<?= $b ?>/admin/plans.php" class="admin-nav-item <?= $cur=='plans.php'?'active':'' ?>">&#128196; Manage Plans</a>
    <a href="<?= $b ?>/admin/run-profit.php" class="admin-nav-item <?= $cur=='run-profit.php'?'active':'' ?>">&#128183; Run Daily Profit</a>

    <div class="admin-nav-section">Trading</div>
    <a href="<?= $b ?>/admin/traders.php" class="admin-nav-item <?= $cur=='traders.php'?'active':'' ?>">&#128101; Copy Traders</a>

    <div class="admin-nav-section">Support</div>
    <a href="<?= $b ?>/admin/tickets.php" class="admin-nav-item <?= $cur=='tickets.php'?'active':'' ?>">&#128172; Support Tickets</a>
    <a href="<?= $b ?>/admin/broadcast.php" class="admin-nav-item <?= $cur=='broadcast.php'?'active':'' ?>">&#128226; Broadcast</a>

    <div class="admin-nav-section">System</div>
    <a href="<?= $b ?>/admin/settings.php" class="admin-nav-item <?= $cur=='settings.php'?'active':'' ?>">&#9881; Settings</a>
    <a href="<?= $b ?>/dashboard.php" class="admin-nav-item">&#128279; User View</a>
    <a href="<?= $b ?>/logout.php" class="admin-nav-item danger">&#128682; Logout</a>
  </nav>
</aside>
<div class="admin-sidebar-overlay" id="adminOverlay" onclick="toggleAdminSidebar()"></div>
