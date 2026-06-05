<?php
require_once '../includes/auth.php';
startSession();
$pageTitle = 'Welthflow — Premier Investment Platform';
$pageDesc = 'We pride ourselves in our guarantees, success and track record in asset management. Take control with Cryptocurrency, Real Estate, FOREX and more.';
$b = SITE_BASE;

$activePlans = fetchAll("SELECT * FROM plans WHERE status='active' ORDER BY min_deposit ASC");
?>
<?php include '../includes/public-header.php'; ?>
<script>document.body.dataset.page = 'home';</script>

<!-- HERO SLIDER -->
<div class="wf-hero" id="wf-hero">
<?php
$slides = [
  ['bg'=>'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=1920&q=90','title'=>'Welthflow Investments','sub'=>'We pride ourselves in our guarantees, success and track record in the asset management and investments market. Take control with our all-in-one multiple investment packages such as Real estate, Cryptocurrency, Agro and more.','cta'=>'Create an Account','ctaHref'=>$b.'/register.php','ctaIcon'=>'fa-user-plus'],
  ['bg'=>'https://images.unsplash.com/photo-1642790106117-e829e14a795f?w=1920&q=90','title'=>'Invest and Earn With Us','sub'=>'Invest with confidence on World\'s leading asset management and investment platform. Your single point of access to professional asset investment and management solutions built for investors seeking stable returns and high liquidity.','cta'=>'Login','ctaHref'=>$b.'/login.php','ctaIcon'=>'fa-user'],
  ['bg'=>'https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?w=1920&q=90','title'=>'Trading Expertise You Can Trust','sub'=>'Our goal is to enhance lives by providing a safe avenue for investing in the world\'s most profitable financial markets — improving our investors\' financial situation and ultimately delivering the financial freedom they deserve.','cta'=>'Get Started','ctaHref'=>$b.'/register.php','ctaIcon'=>'fa-play'],
  ['bg'=>'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1920&q=90','title'=>'Over $563Bn Assets Managed','sub'=>'Trusted by 80 million investors across 80 countries. From Bitcoin to Real Estate to PAMM Forex — our expert portfolio managers work around the clock to grow your wealth with precision and security.','cta'=>'View Investment Plans','ctaHref'=>'#plans-table','ctaIcon'=>'fa-chart-line'],
];
foreach ($slides as $i => $s): ?>
<div class="wf-slide <?= $i===0?'active':'' ?>" style="background-image:url('<?= $s['bg'] ?>')">
  <div class="wf-slide-overlay"></div>
  <div class="container wf-slide-content">
    <div class="wf-slide-badge"><span class="wf-slide-badge-dot"></span>SECURE GLOBAL INVESTMENT PLATFORM</div>
    <h1 class="wf-slide-title"><?= $s['title'] ?></h1>
    <p class="wf-slide-sub"><?= $s['sub'] ?></p>
    <div class="wf-slide-btns">
      <a href="<?= $s['ctaHref'] ?>" class="wf-btn-primary"><i class="fas <?= $s['ctaIcon'] ?>"></i> <?= $s['cta'] ?></a>
      <a href="<?= $b ?>/about.php" class="wf-btn-glass">Learn More</a>
    </div>
  </div>
</div>
<?php endforeach; ?>
<div class="wf-slide-dots" id="wf-slide-dots">
  <?php foreach($slides as $i=>$_): ?>
  <button class="wf-dot <?= $i===0?'active':'' ?>" onclick="goSlide(<?=$i?>)"></button>
  <?php endforeach; ?>
</div>
</div>
<script>
var _slide=0,_total=<?=count($slides)?>;
function goSlide(n){
  document.querySelectorAll('.wf-slide').forEach(function(el,i){el.classList.toggle('active',i===n);});
  document.querySelectorAll('.wf-dot').forEach(function(el,i){el.classList.toggle('active',i===n);});
  _slide=n;
}
setInterval(function(){goSlide((_slide+1)%_total);},6000);
</script>

<!-- INVESTMENT PLANS -->
<section class="wf-quick-plans" id="plans-table">
  <div class="container">
    <div class="wf-quick-plans-header">
      <div class="wf-section-badge" style="background:rgba(201,162,39,0.15);color:#c9a227;border-color:rgba(201,162,39,0.3)">INVESTMENT PACKAGES</div>
      <h2 class="wf-quick-plans-title">Choose Your <span style="color:#c9a227">Investment Plan</span></h2>
      <p class="wf-quick-plans-sub">High-yield plans managed by our expert portfolio team. Daily returns — all with 24/7 support.</p>
    </div>
    <div class="wf-qp-grid">
    <?php
    $planColors = ['#c9a227','#3eda99','#1dbfc8','#ff6b6b'];
    $planIcons  = ['fa-star','fa-chart-line','fa-gem','fa-crown'];
    foreach ($activePlans as $idx => $plan):
      $color = $planColors[$idx % count($planColors)];
      $icon  = $planIcons[$idx % count($planIcons)];
      $popular = $idx === 1;
    ?>
    <div class="wf-qp-card <?= $popular?'wf-qp-popular':'' ?>" style="--plan-color:<?=$color?>;--plan-glow:<?=$color?>33">
      <?php if($popular): ?><div class="wf-qp-badge">Most Popular</div><?php endif; ?>
      <div class="wf-qp-icon-wrap" style="background:<?=$color?>22"><i class="fas <?=$icon?>" style="color:<?=$color?>"></i></div>
      <div class="wf-qp-name"><?= sanitize(strtoupper($plan['name'])) ?></div>
      <div class="wf-qp-roi" style="color:<?=$color?>"><?= number_format($plan['daily_roi'],1) ?>%</div>
      <div class="wf-qp-period">per 24 Hours</div>
      <div class="wf-qp-divider" style="background:<?=$color?>"></div>
      <ul class="wf-qp-features">
        <li><span>Investment</span><b>$<?= number_format($plan['min_deposit']) ?> – $<?= number_format($plan['max_deposit']) ?></b></li>
        <li><span>Duration</span><b><?= $plan['duration_days'] ?> Day<?= $plan['duration_days']>1?'s':'' ?></b></li>
        <li><span>Daily ROI</span><b style="color:#3eda99"><?= $plan['daily_roi'] ?>% ✓</b></li>
        <li><span>Support</span><b style="color:#3eda99">24/7 ✓</b></li>
      </ul>
      <a href="<?= $b ?>/register.php" class="wf-qp-btn" style="background:<?=$color?>">Invest Now</a>
    </div>
    <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- STATS BAR -->
<div style="background:#01123c;padding:60px 0">
  <div class="container">
    <div class="row g-4 text-center">
      <?php foreach([['$563Bn+','Assets Under Management'],['80M+','Active Investors'],['80+','Countries Served'],['10+','Years Experience']] as [$v,$l]): ?>
      <div class="col-6 col-md-3">
        <h2 style="font-family:'Lora',serif;font-size:42px;font-weight:700;color:#3eda99;margin-bottom:8px"><?=$v?></h2>
        <p style="color:rgba(255,255,255,0.7);font-size:13px;text-transform:uppercase;letter-spacing:1px"><?=$l?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- WHY CHOOSE US -->
<section style="padding:80px 0;background:#f8fafc">
  <div class="container">
    <div class="text-center mb-5">
      <div class="wf-section-badge mb-2">WHY WELTHFLOW</div>
      <h2 class="wf-h2">Why Thousands Trust Us</h2>
    </div>
    <div class="row g-4">
      <?php foreach([
        ['fa-shield-halved','FCA & CySec Regulated','We are fully regulated and compliant, giving you peace of mind that your investments are in safe hands.'],
        ['fa-chart-line','Consistent Returns','Our professional portfolio managers deliver consistent daily returns across all market conditions.'],
        ['fa-globe','Global Reach','Serving 80+ million investors in 80+ countries with 24/7 dedicated support.'],
        ['fa-lock','Secure Platform','Bank-level security with full encryption, two-factor authentication, and cold storage.'],
      ] as [$icon,$title,$desc]): ?>
      <div class="col-md-6 col-lg-3">
        <div class="wf-value-card">
          <div class="wf-value-icon"><i class="fas <?=$icon?>"></i></div>
          <h4 class="wf-value-title"><?=$title?></h4>
          <p class="wf-value-desc"><?=$desc?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<div style="background:#062f6d;padding:70px 0">
  <div class="container text-center">
    <h2 style="font-family:'Lora',serif;color:#fff;font-size:32px;margin-bottom:16px">Ready to Start Growing Your Wealth?</h2>
    <p style="color:rgba(255,255,255,0.8);max-width:520px;margin:0 auto 32px;font-size:15px">Join over 80 million investors who trust Welthflow with their financial future. Open your account in minutes.</p>
    <a href="<?= $b ?>/register.php" class="wf-btn-primary" style="font-size:16px;padding:14px 36px">Create Free Account</a>
    <span style="margin:0 16px;color:rgba(255,255,255,0.4)">or</span>
    <a href="<?= $b ?>/login.php" class="wf-btn-outline-white">Sign In</a>
  </div>
</div>

<?php include '../includes/public-footer.php'; ?>
