<?php $b = SITE_BASE; ?>
<footer class="wf-footer">
  <div class="wf-footer-body">
    <div class="container">
      <div class="row mb-4">
        <div class="col-md-4 mb-4">
          <h4 class="wf-footer-heading">Quick Links To Buy Bitcoin in EUROPE</h4>
          <ul class="wf-footer-list">
            <?php foreach([['Coin Mama','https://coinmama.com/'],['PayBis','https://paybis.com/'],['Coin Base','https://coinbase.com/'],['Luno','https://luno.com/'],['Kraken','https://kraken.com/'],['Binance','https://binance.com/'],['Bit2me','https://bit2me.com/']] as [$l,$u]): ?>
            <li><a href="<?= $u ?>" target="_blank" rel="noreferrer"><?= $l ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="col-md-4 mb-4">
          <h4 class="wf-footer-heading">Quick Links To Buy Bitcoin in AMERICA</h4>
          <ul class="wf-footer-list">
            <?php foreach([['Coin Mama','https://coinmama.com/'],['PayBis','https://paybis.com/'],['Coin Base','https://coinbase.com/'],['Local Bitcoins','https://localbitcoins.com/'],['Cex.io','https://cex.io/'],['Gemini','https://gemini.com/']] as [$l,$u]): ?>
            <li><a href="<?= $u ?>" target="_blank" rel="noreferrer"><?= $l ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="col-md-4 mb-4">
          <h4 class="wf-footer-heading">Quick Links To Buy Bitcoin in OTHERS</h4>
          <ul class="wf-footer-list">
            <?php foreach([['Indodax','https://indodax.com/'],['Coinhako','https://coinhako.com/'],['Wazirx','https://wazirx.com/'],['Zebpay','https://zebpay.com/'],['Nobitex','https://nobitex.ir/'],['Wallex','https://wallex.ir/']] as [$l,$u]): ?>
            <li><a href="<?= $u ?>" target="_blank" rel="noreferrer"><?= $l ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <hr style="border-color:rgba(255,255,255,0.15)">
      <div class="row mt-4">
        <div class="col-lg-4 mb-4">
          <h4 class="wf-footer-heading">Our Contacts</h4>
          <ul class="wf-footer-contact">
            <li><i class="fas fa-map-marker-alt"></i> 8 Fitzroy Pl, Finnieston, Glasgow G3 7RH, United Kingdom.</li>
            <li><i class="fas fa-envelope"></i> <a href="mailto:admin@welthflow.com">admin@welthflow.com</a></li>
            <li><i class="fab fa-whatsapp"></i> <a href="#">+447418611709</a></li>
          </ul>
        </div>
        <div class="col-lg-3 mb-4">
          <h4 class="wf-footer-heading">Quick Links</h4>
          <ul class="wf-footer-list">
            <li><a href="<?= $b ?>/">Home</a></li>
            <li><a href="<?= $b ?>/about.php">About Us</a></li>
            <li><a href="<?= $b ?>/affiliates.php">Affiliate</a></li>
            <li><a href="<?= $b ?>/terms.php">Terms &amp; Conditions</a></li>
            <li><a href="<?= $b ?>/register.php">Create Account</a></li>
            <li><a href="<?= $b ?>/login.php">Account Login</a></li>
          </ul>
        </div>
        <div class="col-lg-5 mb-4">
          <div class="wf-footer-logo-wrap mb-3">
            <svg width="200" height="40" viewBox="0 0 220 44" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs><linearGradient id="wf-footgrad" x1="0" y1="0" x2="44" y2="44" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#3eda99"/><stop offset="100%" stop-color="#1dbfc8"/>
              </linearGradient></defs>
              <rect x="0" y="4" width="40" height="36" rx="8" fill="url(#wf-footgrad)" opacity="0.15"/>
              <path d="M8 32 L14 16 L20 27 L26 18 L32 32" stroke="url(#wf-footgrad)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
              <circle cx="26" cy="11" r="5" fill="url(#wf-footgrad)" opacity="0.9"/>
              <text x="50" y="31" font-family="'Lora', serif" font-weight="700" font-size="22" fill="#ffffff">Welth</text>
              <text x="103" y="31" font-family="'Lora', serif" font-weight="400" font-size="22" fill="#3eda99">flow</text>
              <text x="50" y="42" font-family="'Raleway', sans-serif" font-size="9.5" letter-spacing="3" fill="rgba(255,255,255,0.55)">INVESTMENTS</text>
            </svg>
          </div>
          <p style="color:rgba(255,255,255,0.7);font-size:14px">
            Welthflow is dedicated to helping investors around the world reach their desired investment goals and broaden their financial horizons.
          </p>
        </div>
      </div>
    </div>
  </div>
  <div class="wf-footer-copy">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <div class="wf-social-icons">
            <?php foreach([['fab fa-facebook-f','#'],['fab fa-twitter','#'],['fab fa-instagram','#'],['fab fa-linkedin-in','#'],['fab fa-youtube','#']] as [$icon,$href]): ?>
            <a href="<?= $href ?>" class="wf-social-icon"><i class="<?= $icon ?>"></i></a>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="col-lg-6 text-lg-end">
          <p class="mb-0" style="color:rgba(255,255,255,0.6);font-size:13px">&copy; <?= date('Y') ?> Welthflow (welthflow.com). All Rights Reserved.</p>
        </div>
      </div>
    </div>
  </div>
</footer>

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
</body>
</html>
