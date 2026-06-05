<?php
require_once '../includes/auth.php';
startSession();
$pageTitle = 'FAQ';
$pageDesc  = 'Frequently asked questions about Welthflow investment platform.';
$b = SITE_BASE;

$faqs = [
  ['What is Welthflow?','Welthflow is a premier asset management and investment platform dedicated to helping investors around the world reach their desired investment goals. We offer a broad range of investment packages including Cryptocurrency, Real Estate, FOREX (PAMM/MAM), Agro, and Fixed Deposit products.'],
  ['How do I open an account?','Opening an account is simple. Click the "Create an Account" or "Get Started" button, fill in your personal details, verify your email address, and you\'ll be ready to start investing within minutes.'],
  ['What is the minimum investment?','The minimum investment varies by package. Our entry plan starts at $100, making it accessible for new investors. Higher plans offer better daily returns.'],
  ['How are returns paid?','Returns (ROI) are calculated on a daily basis and credited to your account. You can withdraw your returns at any time depending on your selected investment plan.'],
  ['Is my investment safe?','Your investment security is our top priority. Welthflow is fully regulated by the FCA and CySec. All invested funds are covered by our comprehensive insurance policy.'],
  ['What is PAMM/MAM Forex trading?','PAMM (Percentage Allocation Management Module) and MAM (Multi-Account Manager) are Forex trading systems where our professional managers trade using pooled capital of investors. Profits and losses are shared proportionally.'],
  ['Can I refer friends to Welthflow?','Yes! Our referral/affiliate program rewards you for every investor you refer. Referral bonuses range from 1% to 2% depending on your active investment plan. There is no limit to how many people you can refer.'],
  ['How do I make a withdrawal?','Withdrawals are processed through your account dashboard. Navigate to the "Withdrawal" section, enter the amount, and select your preferred payment method. Standard withdrawals are processed within 24–72 business hours.'],
  ['What is the Fixed Funds Deposit plan?','The Fixed Funds Deposit (FFD) is our VIP investment plan offering up to 28% weekly interest rate. The minimum deposit is $9,999. Early withdrawal is permitted with a 5% penalty.'],
  ['Are there loan options available?','Yes. Active investors with at least 10 referred accounts are eligible for our loan program. The Semi-Annual Offer provides $5,000–$49,999 with no interest for up to 6 months. The Annual Offer provides $50,000–$100,000 at 2% interest for up to 12 months.'],
  ['How do I contact support?','Our support team is available 24/7. You can reach us via email at admin@welthflow.com, through our live chat widget, or via WhatsApp at +447418611709.'],
  ['Is Welthflow available in my country?','Welthflow serves investors across 80+ countries worldwide including North America, Europe, Asia, Africa, the Middle East, and Oceania.'],
];
?>
<?php include '../includes/public-header.php'; ?>

<!-- Page Hero -->
<div class="wf-page-hero" style="background-image:url('https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?w=1600&q=70')">
  <div class="wf-page-hero-overlay"></div>
  <div class="container wf-page-hero-content">
    <div class="wf-breadcrumb"><a href="<?=$b?>/">Home</a><i class="fas fa-chevron-right"></i><span>FAQ</span></div>
    <h1 class="wf-page-title">Frequently Asked Questions</h1>
    <p class="wf-page-subtitle">Find answers to the most common questions about investing with Welthflow.</p>
  </div>
</div>

<section style="padding:80px 0 100px">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="wf-section-badge mb-3">HELP CENTER</div>
        <h2 class="wf-h2 mb-2">Got Questions? We Have Answers.</h2>
        <p class="wf-body mb-5" style="color:#718096">Can't find what you're looking for? Contact us at <a href="mailto:admin@welthflow.com" style="color:#062f6d">admin@welthflow.com</a></p>
        <div class="wf-accordion">
          <?php foreach($faqs as $i=>[$q,$a]): ?>
          <div class="wf-accordion-item" id="faq-<?=$i?>">
            <button class="wf-accordion-btn" onclick="toggleFaq(<?=$i?>)">
              <span><?= htmlspecialchars($q) ?></span>
              <i class="fas fa-plus" id="faq-icon-<?=$i?>"></i>
            </button>
            <div class="wf-accordion-body" id="faq-body-<?=$i?>" style="display:none">
              <p><?= htmlspecialchars($a) ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-4 mt-5 mt-lg-0">
        <div class="wf-faq-cta-card">
          <div class="wf-faq-icon"><i class="fas fa-headset"></i></div>
          <h4>Still have questions?</h4>
          <p>Our investment advisors are available 24/7 to help you get started.</p>
          <a href="mailto:admin@welthflow.com" class="wf-btn-primary d-block text-center">Email Support</a>
          <a href="<?=$b?>/support.php" class="wf-btn-outline d-block text-center mt-3">Live Chat</a>
        </div>
        <div class="wf-faq-stat-card mt-4">
          <div class="wf-faq-stat"><span class="wf-faq-num">80M+</span><span>Active Investors</span></div>
          <div class="wf-faq-stat"><span class="wf-faq-num">10+</span><span>Years Experience</span></div>
          <div class="wf-faq-stat"><span class="wf-faq-num">$563Bn</span><span>Assets Managed</span></div>
          <div class="wf-faq-stat"><span class="wf-faq-num">80+</span><span>Countries Served</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
function toggleFaq(i){
  var body=document.getElementById('faq-body-'+i);
  var icon=document.getElementById('faq-icon-'+i);
  var item=document.getElementById('faq-'+i);
  var open=body.style.display==='block';
  document.querySelectorAll('.wf-accordion-body').forEach(function(el){el.style.display='none';});
  document.querySelectorAll('.wf-accordion-btn i').forEach(function(el){el.className='fas fa-plus';});
  document.querySelectorAll('.wf-accordion-item').forEach(function(el){el.classList.remove('open');});
  if(!open){body.style.display='block';icon.className='fas fa-minus';item.classList.add('open');}
}
toggleFaq(0);
</script>

<?php include '../includes/public-footer.php'; ?>
