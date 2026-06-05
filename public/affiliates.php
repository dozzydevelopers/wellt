<?php
require_once '../includes/auth.php';
startSession();
$pageTitle = 'Affiliate Program';
$pageDesc  = 'Earn generous commissions by referring friends and colleagues to invest with Welthflow.';
$b = SITE_BASE;
?>
<?php include '../includes/public-header.php'; ?>

<div class="wf-page-hero" style="background-image:url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1600&q=70')">
  <div class="wf-page-hero-overlay"></div>
  <div class="container wf-page-hero-content">
    <div class="wf-breadcrumb"><a href="<?=$b?>/">Home</a><i class="fas fa-chevron-right"></i><span>Affiliate</span></div>
    <h1 class="wf-page-title">Affiliate Program</h1>
    <p class="wf-page-subtitle">Earn generous commissions by referring friends and colleagues to invest with Welthflow.</p>
  </div>
</div>

<!-- Hero Stats -->
<div style="background:#01123c;padding:50px 0">
  <div class="container">
    <div class="row g-4 text-center">
      <?php foreach([['Up to 2%','Commission per referral'],['Unlimited','Referrals you can make'],['Instant','Commission crediting'],['$0','Cost to join']] as [$v,$l]): ?>
      <div class="col-6 col-md-3">
        <div style="font-family:'Lora',serif;font-size:36px;font-weight:700;color:#3eda99"><?=$v?></div>
        <div style="color:rgba(255,255,255,0.65);font-size:13px;margin-top:4px"><?=$l?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- How it Works -->
<section style="padding:80px 0">
  <div class="container">
    <div class="text-center mb-5">
      <div class="wf-section-badge mb-2">HOW IT WORKS</div>
      <h2 class="wf-h2">Start Earning in 4 Simple Steps</h2>
    </div>
    <div class="row g-4">
      <?php foreach([
        ['fa-user-plus','01','Sign Up &amp; Invest','Create your account and make your first investment to activate your affiliate status.'],
        ['fa-share-nodes','02','Share Your Link','Get your unique referral link from your dashboard and share it with your network.'],
        ['fa-users','03','Friends Join &amp; Invest','Your referrals sign up using your link and make their first investment.'],
        ['fa-coins','04','Earn Commissions','Receive your referral bonus automatically credited to your account.'],
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

<!-- Commission Tiers -->
<section style="background:#f8fafc;padding:80px 0">
  <div class="container">
    <div class="text-center mb-5">
      <div class="wf-section-badge mb-2">COMMISSIONS</div>
      <h2 class="wf-h2">Referral Bonus by Plan</h2>
      <p class="wf-body" style="color:#718096;max-width:560px;margin:0 auto">The more your referral invests, the more you earn. All commissions are paid instantly upon successful investment.</p>
    </div>
    <div class="row g-4 justify-content-center">
      <?php foreach([
        ['Tiro Package','$200 – $4,500','1%','Bronze','#cd7f32'],
        ['Semi-Tiro Package','$5,000 – $45,000','1.3%','Silver','#aaa9ad'],
        ['Executive Package','$50,000 – $100,000','2%','Gold','#ffd700'],
      ] as [$plan,$range,$bonus,$tier,$color]): ?>
      <div class="col-md-4">
        <div class="wf-tier-card">
          <div class="wf-tier-badge" style="background:<?=$color?>"><?=$tier?></div>
          <h4 class="wf-tier-plan"><?=$plan?></h4>
          <div class="wf-tier-range"><?=$range?></div>
          <div class="wf-tier-bonus"><?=$bonus?><span>referral bonus</span></div>
          <p style="font-size:13px;color:#718096;margin-bottom:0">Per every referred investor who joins at this plan level.</p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Benefits + Loan Access -->
<section style="padding:80px 0">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="wf-section-badge mb-3">AFFILIATE BENEFITS</div>
        <h2 class="wf-h2">Why Join the Welthflow Affiliate Program?</h2>
        <p class="wf-body mb-4">Our affiliate program is one of the most rewarding in the investment industry. With no cap on earnings and instant commission payouts, it's a powerful way to generate passive income alongside your own investments.</p>
        <div class="wf-loan-features">
          <?php foreach([
            ['fa-infinity','Unlimited referral potential'],
            ['fa-bolt','Instant commission crediting'],
            ['fa-link','Unique trackable referral link'],
            ['fa-chart-bar','Real-time referral dashboard'],
            ['fa-wallet','Multiple withdrawal options'],
            ['fa-headset','Dedicated affiliate support'],
          ] as [$icon,$text]): ?>
          <div class="wf-loan-feature"><i class="fas <?=$icon?>"></i><span><?=$text?></span></div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-6">
        <div style="background:#01123c;border-radius:16px;padding:40px 36px">
          <h4 class="c-w mb-4">Loan Program Access</h4>
          <p class="c-w mb-3" style="font-size:15px">Affiliates who refer 10 or more active investors also become eligible for Welthflow's exclusive Loan Program, giving you access to:</p>
          <ul style="list-style:none;padding:0">
            <?php foreach(['$5,000 – $49,999 at 0% interest (Semi-Annual)','$50,000 – $100,000 at 2% interest (Annual)','Repayment periods of 6 or 12 months','2-week grace period included'] as $item): ?>
            <li style="color:rgba(255,255,255,0.8);font-size:14px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.08);display:flex;gap:10px;align-items:flex-start">
              <i class="fas fa-check-circle" style="color:#3eda99;margin-top:2px"></i> <?=$item?>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<div style="background:#062f6d;padding:70px 0">
  <div class="container text-center">
    <h2 style="font-family:'Lora',serif;color:#fff;font-size:28px;margin-bottom:12px">Ready to Start Earning?</h2>
    <p style="color:rgba(255,255,255,0.8);margin:0 auto 28px;max-width:520px">Join thousands of Welthflow affiliates already earning generous commissions. Sign up today and start sharing your referral link.</p>
    <a href="<?=$b?>/register.php" class="wf-btn-primary">Join the Affiliate Program</a>
  </div>
</div>

<?php include '../includes/public-footer.php'; ?>
