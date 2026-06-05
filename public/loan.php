<?php
require_once '../includes/auth.php';
startSession();
$pageTitle = 'Loan & Pension Funds';
$pageDesc  = 'Access flexible loan options and retirement investment packages designed for long-term financial security.';
$b = SITE_BASE;
?>
<?php include '../includes/public-header.php'; ?>

<div class="wf-page-hero" style="background-image:url('https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=1600&q=70')">
  <div class="wf-page-hero-overlay"></div>
  <div class="container wf-page-hero-content">
    <div class="wf-breadcrumb"><a href="<?=$b?>/">Home</a><i class="fas fa-chevron-right"></i><span>Loan &amp; Pension</span></div>
    <h1 class="wf-page-title">Loan &amp; Pension Funds</h1>
    <p class="wf-page-subtitle">Access flexible loan options and retirement investment packages designed for long-term financial security.</p>
  </div>
</div>

<!-- Pension Funds -->
<section style="padding:80px 0">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="wf-section-badge mb-3">RETIREMENT</div>
        <h2 class="wf-h2">Retirement &amp; Pension Fund Investment</h2>
        <p class="wf-body mb-3">After retirement, there needs to be a regular source of income which is possible only when you make the right investments. Investing in the right plans helps you get a regular income and also helps you deal with the rising cost of living.</p>
        <p class="wf-body mb-4">Welthflow's Pension Fund investment package is specifically designed for investors planning for their long-term future. Our professional managers allocate your capital across a diversified portfolio of assets optimised for steady, compounding returns.</p>
        <div class="wf-loan-features">
          <?php foreach([
            ['fa-piggy-bank','Tax-efficient retirement savings'],
            ['fa-chart-line','Compounding monthly returns'],
            ['fa-shield-halved','Capital protected investment'],
            ['fa-rotate','Flexible contribution schedule'],
          ] as [$icon,$text]): ?>
          <div class="wf-loan-feature"><i class="fas <?=$icon?>"></i><span><?=$text?></span></div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-6">
        <img src="https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=700&q=70" alt="pension" class="img-fluid rounded-3 shadow">
      </div>
    </div>
  </div>
</section>

<!-- Loan Program -->
<section style="background:#f8fafc;padding:80px 0">
  <div class="container">
    <div class="text-center mb-5">
      <div class="wf-section-badge mb-2">LOAN PROGRAM</div>
      <h2 class="wf-h2">Loan Offer with Welthflow Funds Company</h2>
      <p class="wf-body" style="color:#718096;max-width:620px;margin:0 auto">Welthflow grants loan offers exclusively to active investors. There are two loan tiers available depending on your investment profile.</p>
    </div>
    <div class="row g-4 mb-5">
      <div class="col-lg-6">
        <div class="wf-loan-card">
          <div class="wf-loan-card-header"><i class="fas fa-calendar-days"></i><h3>Semi-Annual Offer</h3></div>
          <div class="wf-loan-amount">$5,000 – $49,999</div>
          <ul class="wf-loan-list">
            <li><i class="fas fa-check"></i> Zero interest rate</li>
            <li><i class="fas fa-check"></i> Repayment period: 6 months</li>
            <li><i class="fas fa-check"></i> Grace period: 2 weeks</li>
            <li><i class="fas fa-check"></i> Full lump-sum repayment required</li>
            <li><i class="fas fa-check"></i> Requires 10+ active referrals</li>
          </ul>
          <a href="<?=$b?>/register.php" class="wf-btn-primary d-block text-center">Apply Now</a>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="wf-loan-card featured">
          <div class="wf-loan-badge">Higher Limit</div>
          <div class="wf-loan-card-header"><i class="fas fa-calendar-days"></i><h3>Annual Offer</h3></div>
          <div class="wf-loan-amount">$50,000 – $100,000</div>
          <ul class="wf-loan-list">
            <li><i class="fas fa-check"></i> 2% interest on loan amount</li>
            <li><i class="fas fa-check"></i> Repayment period: 12 months</li>
            <li><i class="fas fa-check"></i> Grace period: 2 weeks</li>
            <li><i class="fas fa-check"></i> Full lump-sum repayment required</li>
            <li><i class="fas fa-check"></i> Requires 10+ active referrals</li>
          </ul>
          <a href="<?=$b?>/register.php" class="wf-btn-primary d-block text-center">Apply Now</a>
        </div>
      </div>
    </div>
    <!-- Eligibility -->
    <div class="wf-eligibility-box">
      <h4><i class="fas fa-info-circle"></i> Loan Eligibility Requirements</h4>
      <div class="row g-3 mt-2">
        <?php foreach([
          'Must be an active Welthflow investor',
          'Minimum 10 active referrals who have invested',
          'Account in good standing (no outstanding issues)',
          'All referred investors must have made at least one deposit',
          'Full repayment — no installment payments permitted',
          'Loan applied for through official account dashboard',
        ] as $item): ?>
        <div class="col-md-6">
          <div class="wf-eligibility-item"><i class="fas fa-check-circle"></i><span><?=htmlspecialchars($item)?></span></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- Fixed Deposit VIP -->
<section style="background:#01123c;padding:80px 0">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="wf-section-badge mb-3">VIP PLAN</div>
        <h2 class="wf-h2 c-w">Fixed Funds Deposit</h2>
        <p class="c-w mb-3">The Fixed Funds Deposit (FFD) is Welthflow's premium VIP investment plan, offering up to 28% weekly interest. This plan is ideal for serious investors seeking maximum compounding returns over a defined period.</p>
        <div class="row g-3 mb-4">
          <?php foreach([['$9,999','Minimum Deposit'],['28%','Weekly Interest'],['5%','Early Exit Penalty'],['$200','Account Activation Fee']] as [$val,$label]): ?>
          <div class="col-6">
            <div style="border:1px solid rgba(255,255,255,0.2);border-radius:10px;padding:16px;text-align:center">
              <div style="font-family:'Lora',serif;font-size:28px;font-weight:700;color:#3eda99"><?=$val?></div>
              <div style="color:rgba(255,255,255,0.65);font-size:12px;margin-top:4px"><?=$label?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <a href="<?=$b?>/register.php" class="wf-btn-primary">Start Fixed Deposit</a>
      </div>
      <div class="col-lg-6">
        <div class="wf-ffd-benefits">
          <h4 class="c-w mb-4">Benefits of Fixed Funds Deposit</h4>
          <?php foreach([
            ['fa-coins','Accumulated Wealth','Earn massively at the end of the investment period — far more than standard packages.'],
            ['fa-shield-halved','Risk-Free Returns','Your investment is protected by PAMM and CySec Policy, guaranteeing fixed returns.'],
            ['fa-rotate','Flexible Migration','Migrate from any ordinary account to Fixed Deposit with no separate account required.'],
            ['fa-money-bill-wave','Emergency Access','Premature withdrawal is available for emergencies — subject to a 5% penalty.'],
          ] as [$icon,$title,$desc]): ?>
          <div class="wf-ffd-benefit">
            <div class="wf-ffd-benefit-icon"><i class="fas <?=$icon?>"></i></div>
            <div><h6 class="c-w mb-1"><?=$title?></h6><p style="color:rgba(255,255,255,0.7);font-size:14px;margin-bottom:0"><?=$desc?></p></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include '../includes/public-footer.php'; ?>
