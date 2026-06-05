<?php $b = SITE_BASE; ?>
<!-- Mobile Bottom Navigation -->
<nav class="mobile-nav">
  <a href="<?= $b ?>/dashboard.php"
    class="mobile-nav-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
    <span class="mobile-nav-icon">&#127968;</span><span>Home</span>
  </a>
  <a href="<?= $b ?>/deposit.php"
    class="mobile-nav-item <?= basename($_SERVER['PHP_SELF']) == 'deposit.php' ? 'active' : '' ?>">
    <span class="mobile-nav-icon">&#8595;</span><span>Deposit</span>
  </a>
  <a href="<?= $b ?>/invest.php" class="mobile-nav-item <?= basename($_SERVER['PHP_SELF']) == 'invest.php' ? 'active' : '' ?>">
    <span class="mobile-nav-icon">&#128200;</span><span>Invest</span>
  </a>
  <a href="<?= $b ?>/trade.php" class="mobile-nav-item <?= basename($_SERVER['PHP_SELF']) == 'trade.php' ? 'active' : '' ?>">
    <span class="mobile-nav-icon">&#128201;</span><span>Trade</span>
  </a>
  <a href="<?= $b ?>/support.php" class="mobile-nav-item <?= basename($_SERVER['PHP_SELF']) == 'support.php' ? 'active' : '' ?>">
    <span class="mobile-nav-icon">&#9776;</span><span>Support</span>
  </a>
</nav>
<script src="<?= assetUrl('assets/js/app.js') ?>"></script>
<!-- Smartsupp Live Chat -->
<script type="text/javascript">
  var _smartsupp = _smartsupp || {};
  _smartsupp.key = 'e1f6488ccb5c061e24ad09e6ec82da06eead2ef8';
  window.smartsupp||(function(d) {
    var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
    s=d.getElementsByTagName('script')[0];c=d.createElement('script');
    c.type='text/javascript';c.charset='utf-8';c.async=true;
    c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
  })(document);
</script>
<noscript>Powered by <a href="https://www.smartsupp.com" target="_blank">Smartsupp</a></noscript>
</body>
</html>
