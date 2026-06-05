<?php
require_once '../includes/auth.php';
startSession();
$pageTitle = 'Cryptocurrency Investment';
$pageDesc  = 'Access high-growth opportunities in Bitcoin and crypto markets managed by our expert team.';
$b = SITE_BASE;

$plans = fetchAll("SELECT * FROM plans WHERE status='active' ORDER BY min_deposit ASC");
?>
<?php include '../includes/public-header.php'; ?>

<div class="wf-page-hero" style="background-image:url('https://images.unsplash.com/photo-1518546305927-5a555bb7020d?w=1600&q=70')">
  <div class="wf-page-hero-overlay"></div>
  <div class="container wf-page-hero-content">
    <div class="wf-breadcrumb"><a href="<?=$b?>/">Home</a><i class="fas fa-chevron-right"></i><span>Cryptocurrency</span></div>
    <h1 class="wf-page-title">Cryptocurrency Investment</h1>
    <p class="wf-page-subtitle">Access high-growth opportunities in Bitcoin and crypto markets — managed by our expert team.</p>
  </div>
</div>

<!-- Intro -->
<section style="padding:80px 0">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="wf-section-badge mb-3">CRYPTO INVESTING</div>
        <h2 class="wf-h2">Why Invest in Cryptocurrency with Welthflow?</h2>
        <p class="wf-body mb-3">You don't need to be a cryptocurrency expert to profit from this sector. Our professional team takes charge of all trading and mining management, so your investment return is secured while you sit back and earn.</p>
        <p class="wf-body mb-4">We offer investors access to high-growth opportunities in the Bitcoin markets through cutting-edge technical facilities and industry-standard cryptocurrency trading strategies.</p>
        <div class="row g-3">
          <?php foreach([['fa-shield-halved','Secured Returns'],['fa-clock','Daily Payouts'],['fa-headset','24/7 Support'],['fa-chart-line','Expert Management']] as [$icon,$label]): ?>
          <div class="col-6">
            <div class="wf-feature-chip"><i class="fas <?=$icon?>"></i> <?=$label?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-6">
        <img src="https://images.unsplash.com/photo-1642790106117-e829e14a795f?w=700&q=70" alt="crypto trading" class="img-fluid rounded-3 shadow">
      </div>
    </div>
  </div>
</section>

<!-- How It Works -->
<section style="background:#f8fafc;padding:80px 0">
  <div class="container">
    <div class="text-center mb-5">
      <div class="wf-section-badge mb-2">PROCESS</div>
      <h2 class="wf-h2">How It Works</h2>
    </div>
    <div class="row g-4">
      <?php foreach([
        ['fa-user-plus','01','Create Your Account','Sign up in minutes with your basic details. Identity verification is quick and secure.'],
        ['fa-wallet','02','Fund Your Account','Deposit cryptocurrency or fiat currency using any of our supported payment methods.'],
        ['fa-sliders','03','Choose a Plan','Select the investment package that matches your goals and risk tolerance.'],
        ['fa-chart-line','04','Earn Daily Returns','Watch your investment grow with daily ROI payouts credited directly to your account.'],
      ] as [$icon,$step,$title,$desc]): ?>
      <div class="col-md-6 col-lg-3">
        <div class="wf-step-card">
          <div class="wf-step-num"><?=$step?></div>
          <div class="wf-step-icon"><i class="fas <?=$icon?>"></i></div>
          <h4 class="wf-step-title"><?=$title?></h4>
          <p class="wf-step-desc"><?=$desc?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Plans from DB -->
<section style="background:#01123c;padding:80px 0">
  <div class="container">
    <div class="text-center mb-5">
      <div class="wf-plan-eyebrow">INVESTMENT PLANS</div>
      <h2 class="c-w">Cryptocurrency Investment <span class="wf-highlight">Plans</span></h2>
      <p class="c-w" style="max-width:560px;margin:0 auto">Choose the plan that best suits your financial goals. All plans include daily ROI payouts and 24/7 support.</p>
    </div>
    <div class="row g-4 justify-content-center">
    <?php if ($plans): foreach($plans as $i=>$p): $popular = $i===1; ?>
      <div class="col-lg-4">
        <div class="wf-plan-card <?= $popular?'popular':'' ?>" style="height:100%">
          <?php if($popular): ?><div class="wf-plan-badge">Most Popular</div><?php endif; ?>
          <h5 class="wf-plan-name"><?=sanitize(strtoupper($p['name']))?></h5>
          <div class="wf-plan-roi"><?=$p['daily_roi']?>%</div>
          <span class="wf-plan-period">/ Daily ROI</span>
          <hr>
          <div style="color:rgba(255,255,255,0.7);font-size:13px;margin-bottom:16px">
            <div><b style="color:#fff">Range:</b> $<?=number_format($p['min_deposit'])?> – $<?=number_format($p['max_deposit'])?></div>
            <div><b style="color:#fff">Duration:</b> <?=$p['duration_days']?> Day<?=$p['duration_days']>1?'s':''?></div>
          </div>
          <ul class="wf-plan-features">
            <li>Daily profit payouts</li>
            <li>24/7 support included</li>
            <li>Online dashboard access</li>
            <li>Crypto trading included</li>
          </ul>
          <a href="<?=$b?>/register.php" class="wf-btn-plan">Invest Now</a>
        </div>
      </div>
    <?php endforeach; else: ?>
      <?php foreach([
        ['TIRO PACKAGE','1.5%','$200 – $4,500','3 Months',false],
        ['SEMI-TIRO PACKAGE','2%','$5,000 – $45,000','6 Months',true],
        ['EXECUTIVE PACKAGE','3.5%','$50,000 – $100,000','1 Year',false],
      ] as [$name,$roi,$range,$dur,$pop]): ?>
      <div class="col-lg-4">
        <div class="wf-plan-card <?=$pop?'popular':''?>" style="height:100%">
          <?php if($pop): ?><div class="wf-plan-badge">Most Popular</div><?php endif; ?>
          <h5 class="wf-plan-name"><?=$name?></h5>
          <div class="wf-plan-roi"><?=$roi?></div>
          <span class="wf-plan-period">/ Daily ROIs</span>
          <hr>
          <div style="color:rgba(255,255,255,0.7);font-size:13px;margin-bottom:16px">
            <div><b style="color:#fff">Range:</b> <?=$range?></div>
            <div><b style="color:#fff">Duration:</b> <?=$dur?></div>
          </div>
          <ul class="wf-plan-features">
            <li>Daily profit payouts</li>
            <li>24/7 support included</li>
            <li>Online dashboard access</li>
            <li>Crypto trading included</li>
          </ul>
          <a href="<?=$b?>/register.php" class="wf-btn-plan">Invest Now</a>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<div style="background:#062f6d;padding:70px 0">
  <div class="container text-center">
    <h2 style="font-family:'Lora',serif;color:#fff;font-size:28px;margin-bottom:12px">Start Earning Today</h2>
    <p style="color:rgba(255,255,255,0.8);margin:0 auto 28px;max-width:500px">Open your account in minutes and join millions of investors already growing their wealth with Welthflow.</p>
    <a href="<?=$b?>/register.php" class="wf-btn-primary">Create Free Account</a>
  </div>
</div>

<?php include '../includes/public-footer.php'; ?>
