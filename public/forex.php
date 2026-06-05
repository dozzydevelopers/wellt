<?php
require_once '../includes/auth.php';
startSession();
$pageTitle = 'FOREX PAMM/MAM Investment';
$pageDesc  = 'Professional Forex trading managed by our expert PAMM managers — invest and earn proportionally.';
$b = SITE_BASE;
?>
<?php include '../includes/public-header.php'; ?>

<div class="wf-page-hero" style="background-image:url('https://images.unsplash.com/photo-1642790106117-e829e14a795f?w=1600&q=70')">
  <div class="wf-page-hero-overlay"></div>
  <div class="container wf-page-hero-content">
    <div class="wf-breadcrumb"><a href="<?=$b?>/">Home</a><i class="fas fa-chevron-right"></i><span>FOREX</span></div>
    <h1 class="wf-page-title">FOREX (PAMM/MAM) Investment</h1>
    <p class="wf-page-subtitle">Professional Forex trading managed by our expert PAMM managers — invest and earn proportionally.</p>
  </div>
</div>

<!-- What is PAMM -->
<section style="padding:80px 0">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="wf-section-badge mb-3">PAMM / MAM FOREX</div>
        <h2 class="wf-h2">What is PAMM Trading?</h2>
        <p class="wf-body mb-3">PAMM (Percentage Allocation Management Module) is a revolutionary Forex trading system where professional managers trade using the pooled investment capital of multiple investors. All profits and losses are shared proportionally among all participating accounts.</p>
        <p class="wf-body mb-3">Welthflow's PAMM Managers are rigorously vetted professionals with proven track records in global Forex markets. They trade using sophisticated algorithms, technical analysis, and market intelligence to consistently generate returns for our investors.</p>
        <p class="wf-body">MAM (Multi-Account Manager) allows a single manager to trade across multiple client accounts simultaneously, ensuring efficiency and consistency in execution across all investor portfolios.</p>
      </div>
      <div class="col-lg-6">
        <div class="row g-3">
          <?php foreach([
            ['fa-chart-line','How it Works','You deposit funds into your managed account. Our PAMM manager trades on your behalf. Profits and losses are distributed proportionally based on your investment size.'],
            ['fa-shield-halved','Risk Management','Our managers follow strict risk management protocols including stop-loss limits, position sizing rules, and diversification across multiple currency pairs.'],
            ['fa-eye','Full Transparency','Monitor your account performance in real time through your dashboard. Full trade history and P&L statements are available at any time.'],
            ['fa-rotate','Flexible Investment','You remain in control. You can add to your investment, withdraw returns, or change plans at any time according to your investment plan terms.'],
          ] as [$icon,$title,$desc]): ?>
          <div class="col-md-6">
            <div class="wf-forex-card">
              <div class="wf-forex-icon"><i class="fas <?=$icon?>"></i></div>
              <h5 class="wf-forex-title"><?=$title?></h5>
              <p class="wf-forex-desc"><?=$desc?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Long-term Plans -->
<section style="background:#01123c;padding:80px 0">
  <div class="container">
    <div class="text-center mb-5">
      <div class="wf-plan-eyebrow">LONG-TERM PACKAGES</div>
      <h2 class="c-w">Long Term Investment <span class="wf-highlight">Packages</span></h2>
      <p class="c-w" style="max-width:560px;margin:0 auto">Our long-term packages offer the highest daily profit rates for serious investors committed to sustained wealth growth.</p>
    </div>
    <div class="row g-4 justify-content-center">
      <?php foreach([
        ['CRYPTO','6.5%','$2,000 – $50,000','1 Year',false,['Daily profit payouts','Crypto portfolio managed','24/7 support','Dashboard analytics','Monthly statements']],
        ['STANDARD PACKAGE','5%','$1,500 – $50,000','1 Year',true,['Daily profit payouts','PAMM/MAM managed','24/7 priority support','Dashboard analytics','Monthly statements','Portfolio diversification']],
        ['REAL ESTATE','8%','$2,500 – $200,000','1 Year',false,['Daily profit payouts','Real estate portfolio','Dedicated account manager','Dashboard analytics','Quarterly reports','Tax documentation']],
      ] as [$name,$roi,$range,$dur,$pop,$features]): ?>
      <div class="col-lg-4">
        <div class="wf-plan-card <?=$pop?'popular':''?>" style="height:100%">
          <?php if($pop): ?><div class="wf-plan-badge">Best Value</div><?php endif; ?>
          <h5 class="wf-plan-name"><?=$name?></h5>
          <div class="wf-plan-roi"><?=$roi?></div>
          <span class="wf-plan-period">/ Daily Profits</span>
          <hr>
          <div style="color:rgba(255,255,255,0.7);font-size:13px;margin-bottom:16px">
            <div><b style="color:#fff">Range:</b> <?=$range?></div>
            <div><b style="color:#fff">Duration:</b> <?=$dur?></div>
          </div>
          <ul class="wf-plan-features">
            <?php foreach($features as $f): ?><li><?=$f?></li><?php endforeach; ?>
          </ul>
          <a href="<?=$b?>/register.php" class="wf-btn-plan">Invest Now</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Why Forex -->
<section style="background:#f8fafc;padding:80px 0">
  <div class="container">
    <div class="text-center mb-5">
      <div class="wf-section-badge mb-2">ADVANTAGES</div>
      <h2 class="wf-h2">Why Trade Forex with Welthflow?</h2>
    </div>
    <div class="row g-4">
      <?php foreach([
        ['fa-globe','24/5 Global Market','The Forex market operates 24 hours a day, 5 days a week, providing continuous opportunities regardless of your time zone.'],
        ['fa-money-bill-trend-up','High Liquidity','With over $6.6 trillion traded daily, Forex is the world\'s most liquid market — ensuring your funds can always be accessed.'],
        ['fa-users','Expert Management','Our PAMM managers have proven track records with average annual returns that outperform traditional investment vehicles.'],
        ['fa-layer-group','Diversified Exposure','Trade across major, minor, and exotic currency pairs plus commodities and indices for a balanced, diversified portfolio.'],
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
    <h2 style="font-family:'Lora',serif;color:#fff;font-size:28px;margin-bottom:12px">Start Forex Investing Today</h2>
    <p style="color:rgba(255,255,255,0.8);margin:0 auto 28px;max-width:500px">Let our expert PAMM managers put your money to work in the world's largest financial market.</p>
    <a href="<?=$b?>/register.php" class="wf-btn-primary">Open Your Account</a>
  </div>
</div>

<?php include '../includes/public-footer.php'; ?>
