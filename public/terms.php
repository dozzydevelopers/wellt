<?php
require_once '../includes/auth.php';
startSession();
$pageTitle = 'Legal & Terms';
$pageDesc  = 'Terms and Conditions for using Welthflow investment platform.';
$b = SITE_BASE;

$sections = [
  ['1. Acceptance of Terms','By accessing or using the Welthflow website and investment services, you agree to be bound by these Terms and Conditions, our Privacy Policy, and all applicable laws and regulations. If you do not agree to these terms, you may not use our services. Welthflow reserves the right to update these terms at any time. Continued use of the platform constitutes acceptance of any changes.'],
  ['2. Eligibility','To use Welthflow services, you must: (a) be at least 18 years of age; (b) have the legal capacity to enter into binding contracts in your jurisdiction; (c) not be a resident of any jurisdiction where the provision of such services is prohibited by law.'],
  ['3. Investment Risks','All investments carry inherent risk, including the possible loss of principal. Past performance is not indicative of future results. Welthflow does not guarantee any specific investment return. Cryptocurrency, Forex, and other financial markets are highly volatile. You should only invest capital that you can afford to lose.'],
  ['4. Account Registration','You are responsible for maintaining the confidentiality of your account credentials. All activities conducted through your account are your responsibility. You must provide accurate, current, and complete information during registration. Notify us immediately at admin@welthflow.com if you suspect unauthorized access.'],
  ['5. Deposits and Withdrawals','Minimum deposit amounts vary by investment plan. All deposits must be made using approved payment methods as listed on the platform. Withdrawal requests are processed within 24–72 business hours, subject to verification.'],
  ['6. Fixed Funds Deposit Terms','The Fixed Funds Deposit (FFD) plan requires a minimum deposit of $9,999. Funds are locked for the agreed investment period. Early withdrawal attracts a penalty of 5% of the total invested amount.'],
  ['7. Loan Program Terms','Eligibility for the loan program requires a minimum of 10 active referrals who have made investments. The Semi-Annual Offer provides loans from $5,000 to $49,999 with no interest, repayable within 6 months. The Annual Offer provides $50,000 to $100,000 at 2% interest, repayable within 12 months. Both include a 2-week grace period.'],
  ['8. Referral and Affiliate Program','Referral bonuses are credited to your account upon successful registration and investment by referred parties. Bonus rates range from 1% to 2% depending on your active plan. Fraudulent referrals, including self-referrals or multiple accounts, will result in account termination and forfeiture of all bonuses.'],
  ['9. Prohibited Activities','You may not use the platform for money laundering or financing illegal activities; create multiple accounts; manipulate the platform in any way; engage in any activity that disrupts the service; or violate any applicable local, national, or international laws.'],
  ['10. Privacy and Data Protection','Welthflow collects and processes personal data in accordance with applicable data protection laws, including GDPR where applicable. We do not sell your personal data to third parties.'],
  ['11. Regulatory Compliance','Welthflow operates in compliance with regulations set by the Financial Conduct Authority (FCA) and the Cyprus Securities and Exchange Commission (CySec).'],
  ['12. Limitation of Liability','To the maximum extent permitted by law, Welthflow shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of the platform or investment services.'],
  ['13. Governing Law','These Terms shall be governed by and construed in accordance with the laws of England and Wales.'],
  ['14. Contact Us','For any questions, please contact us at: Email: admin@welthflow.com | Address: 8 Fitzroy Pl, Finnieston, Glasgow G3 7RH, United Kingdom | WhatsApp: +447418611709'],
];
?>
<?php include '../includes/public-header.php'; ?>

<div class="wf-page-hero" style="background-image:url('https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=1600&q=70')">
  <div class="wf-page-hero-overlay"></div>
  <div class="container wf-page-hero-content">
    <div class="wf-breadcrumb"><a href="<?=$b?>/">Home</a><i class="fas fa-chevron-right"></i><span>Terms</span></div>
    <h1 class="wf-page-title">Legal &amp; Terms of Service</h1>
    <p class="wf-page-subtitle">Last updated: January 1, 2024 — Please read these terms carefully before using welthflow.com.</p>
  </div>
</div>

<section style="padding:80px 0 100px">
  <div class="container">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-lg-3 d-none d-lg-block">
        <div class="wf-terms-sidebar">
          <h6 class="wf-terms-sidebar-title">Contents</h6>
          <?php foreach($sections as $i=>[$title,$_]): ?>
          <a href="#section-<?=$i?>" class="wf-terms-sidebar-link"><?=htmlspecialchars($title)?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <!-- Content -->
      <div class="col-lg-9">
        <div class="wf-terms-intro">
          <i class="fas fa-balance-scale wf-terms-intro-icon"></i>
          <p>These Terms and Conditions govern your use of Welthflow (welthflow.com) and all associated investment services. By using our platform, you acknowledge that you have read, understood, and agree to be bound by these terms. Contact us at <a href="mailto:admin@welthflow.com">admin@welthflow.com</a> with any questions.</p>
        </div>
        <?php foreach($sections as $i=>[$title,$content]): ?>
        <div id="section-<?=$i?>" class="wf-terms-section">
          <h3 class="wf-terms-heading"><?=htmlspecialchars($title)?></h3>
          <p class="wf-body"><?=htmlspecialchars($content)?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php include '../includes/public-footer.php'; ?>
