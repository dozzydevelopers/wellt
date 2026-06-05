<?php
require_once '../includes/auth.php';
startSession();
$pageTitle = 'About Us';
$pageDesc  = 'Learn about Welthflow — a pioneer of commission-free investing built for the sole benefit of our investors.';
$b = SITE_BASE;
?>
<?php include '../includes/public-header.php'; ?>

<div class="wf-page-hero" style="background-image:url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=1600&q=70')">
  <div class="wf-page-hero-overlay"></div>
  <div class="container wf-page-hero-content">
    <div class="wf-breadcrumb"><a href="<?=$b?>/">Home</a><i class="fas fa-chevron-right"></i><span>About Us</span></div>
    <h1 class="wf-page-title">About Welthflow</h1>
    <p class="wf-page-subtitle">A pioneer of commission-free investing — built for the sole benefit of our investors.</p>
  </div>
</div>

<!-- Mission -->
<section style="padding:80px 0">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="wf-section-badge mb-3">OUR STORY</div>
        <h2 class="wf-h2">Who We Are</h2>
        <p class="wf-body mb-3">Welthflow was founded on a simple but revolutionary idea — that an investment company should be run for the sole benefit of its investors. We removed outside owners and outside interests from the equation, creating a structure where our success can only be measured by your success.</p>
        <p class="wf-body mb-3">Today, we manage over $562.9 billion in assets on behalf of governments, pension funds, insurers, companies, charities, foundations and individual investors across 80 countries. With employees in more than 40 locations worldwide, our operations extend across global financial capitals and important regional centres.</p>
        <p class="wf-body">We combine deep knowledge of local markets with the power of coordinated global oversight to drive better investment outcomes for every single client.</p>
      </div>
      <div class="col-lg-6">
        <div class="wf-about-img-grid">
          <img src="https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=600&q=70" alt="trading" class="wf-about-img wf-about-img-lg">
          <img src="https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?w=400&q=70" alt="charts" class="wf-about-img wf-about-img-sm">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Stats -->
<div style="background:#01123c;padding:60px 0">
  <div class="container">
    <div class="row g-4 text-center">
      <?php foreach([['$563Bn+','Assets Under Management'],['80M+','Active Investors Worldwide'],['80+','Countries Served'],['40+','Global Office Locations']] as [$v,$l]): ?>
      <div class="col-6 col-md-3">
        <h2 style="font-family:'Lora',serif;font-size:42px;font-weight:700;color:#3eda99;margin-bottom:8px"><?=$v?></h2>
        <p style="color:rgba(255,255,255,0.7);font-size:13px;text-transform:uppercase;letter-spacing:1px"><?=$l?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Values -->
<section style="padding:80px 0;background:#f8fafc">
  <div class="container">
    <div class="text-center mb-5">
      <div class="wf-section-badge mb-2">WHAT DRIVES US</div>
      <h2 class="wf-h2">Our Core Values</h2>
    </div>
    <div class="row g-4">
      <?php foreach([
        ['fa-shield-halved','Integrity','We operate with full transparency and hold ourselves to the highest ethical standards in every transaction.'],
        ['fa-chart-line','Performance','We are relentlessly focused on delivering superior, consistent returns for our investors across all market conditions.'],
        ['fa-globe','Global Reach','With a presence in 40+ locations, we bring local market knowledge combined with coordinated global oversight.'],
        ['fa-users','Investor First','Our entire structure is built around you. Every decision we make is guided by the best interests of our investors.'],
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

<!-- Team -->
<section style="padding:80px 0">
  <div class="container">
    <div class="text-center mb-5">
      <div class="wf-section-badge mb-2">LEADERSHIP</div>
      <h2 class="wf-h2">Meet Our Team</h2>
      <p class="wf-body" style="color:#718096;max-width:560px;margin:0 auto">Our team of seasoned professionals brings decades of combined experience across global financial markets.</p>
    </div>
    <div class="row g-4 justify-content-center">
      <?php foreach([
        ['James Harrington','Chief Executive Officer','https://randomuser.me/api/portraits/men/52.jpg','20+ years in global asset management across Europe and the Americas.'],
        ['Sophie Chen','Chief Investment Officer','https://randomuser.me/api/portraits/women/62.jpg','Former hedge fund manager with expertise in cryptocurrency and emerging markets.'],
        ['Marcus Webb','Head of Forex Trading','https://randomuser.me/api/portraits/men/44.jpg','PAMM/MAM specialist with a decade of institutional trading experience.'],
        ['Amara Osei','Head of Client Relations','https://randomuser.me/api/portraits/women/33.jpg','Dedicated to ensuring every investor receives world-class service and support.'],
      ] as [$name,$role,$img,$bio]): ?>
      <div class="col-md-6 col-lg-3">
        <div class="wf-team-card">
          <img src="<?=$img?>" alt="<?=htmlspecialchars($name)?>" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #3eda99;margin-bottom:14px;display:block;margin-left:auto;margin-right:auto;">
          <h4 class="wf-team-name"><?=$name?></h4>
          <p class="wf-team-role"><?=$role?></p>
          <p class="wf-team-bio"><?=$bio?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<div style="background:#062f6d;padding:70px 0">
  <div class="container text-center">
    <h2 style="font-family:'Lora',serif;color:#fff;font-size:30px;margin-bottom:16px">Ready to grow your wealth?</h2>
    <p style="color:rgba(255,255,255,0.8);max-width:520px;margin:0 auto 32px;font-size:15px">Join over 80 million investors who trust Welthflow with their financial future.</p>
    <a href="<?=$b?>/register.php" class="wf-btn-primary">Open Your Account Today</a>
  </div>
</div>

<?php include '../includes/public-footer.php'; ?>
