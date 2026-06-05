<?php
require_once '../includes/auth.php';
$pageTitle = 'Terms & Privacy Policy';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Terms & Privacy Policy - Welthflow</title>
<link rel="stylesheet" href="<?= assetUrl('assets/css/style.css') ?>">
<style>
  .terms-body{background:#0d1b3e;min-height:100vh;padding:40px 16px;}
  .terms-card{max-width:820px;margin:0 auto;background:#fff;border-radius:16px;padding:48px 40px;box-shadow:0 8px 40px rgba(0,0,0,.2);}
  .terms-logo{display:flex;align-items:center;gap:12px;margin-bottom:32px;}
  .terms-logo .logo-bar{width:4px;height:40px;background:linear-gradient(180deg,#F97316,#c9a227);border-radius:2px;display:inline-block;}
  .terms-logo-text .logo-main{display:block;font-size:20px;font-weight:800;letter-spacing:3px;color:#0d1b3e;}
  .terms-logo-text .logo-sub{display:block;font-size:9px;letter-spacing:4px;color:#F97316;text-transform:uppercase;}
  .terms-h1{font-size:26px;font-weight:800;color:#0d1b3e;margin-bottom:6px;}
  .terms-date{font-size:13px;color:#94A3B8;margin-bottom:32px;}
  .terms-section{margin-bottom:28px;}
  .terms-section h2{font-size:16px;font-weight:700;color:#0d1b3e;margin-bottom:10px;padding-bottom:6px;border-bottom:2px solid #F97316;}
  .terms-section p,.terms-section li{font-size:14px;color:#475569;line-height:1.8;margin-bottom:8px;}
  .terms-section ul{padding-left:20px;}
  .terms-back{display:inline-block;background:#0d1b3e;color:#fff;padding:12px 24px;border-radius:8px;font-weight:700;font-size:14px;text-decoration:none;margin-top:24px;}
  .terms-back:hover{background:#1a396b;color:#fff;}
</style>
</head>
<body class="terms-body">
<div class="terms-card">
  <div class="terms-logo">
    <span class="logo-bar"></span>
    <div class="terms-logo-text">
      <span class="logo-main">WELTHFLOW</span>
      <span class="logo-sub">Investment</span>
    </div>
  </div>

  <h1 class="terms-h1">Terms of Service & Privacy Policy</h1>
  <p class="terms-date">Last updated: January 1, 2026 &nbsp;|&nbsp; Effective: January 1, 2026</p>

  <div class="terms-section">
    <h2>1. Acceptance of Terms</h2>
    <p>By accessing or using Welthflow's platform, services, website, or mobile applications, you confirm that you have read, understood, and agree to be bound by these Terms of Service and our Privacy Policy. If you do not agree, please do not use our services.</p>
    <p>Welthflow reserves the right to update these terms at any time. Continued use of the platform after changes constitutes your acceptance of the new terms.</p>
  </div>

  <div class="terms-section">
    <h2>2. Eligibility</h2>
    <ul>
      <li>You must be at least 18 years of age to use our platform.</li>
      <li>You must reside in a jurisdiction where investment services are legally permitted.</li>
      <li>You must provide accurate, current, and complete registration information.</li>
      <li>One account per person/entity. Multiple accounts may be suspended without notice.</li>
    </ul>
  </div>

  <div class="terms-section">
    <h2>3. Investment Plans & Returns</h2>
    <p>Welthflow offers managed investment plans with stated daily ROI percentages. These returns are based on our trading activities and are subject to market conditions. Past performance does not guarantee future results.</p>
    <ul>
      <li>All investment plans have fixed durations and ROI rates as stated at the time of purchase.</li>
      <li>Profits are credited to your account balance daily as per your plan terms.</li>
      <li>Capital and profits are available for withdrawal upon plan completion.</li>
      <li>Welthflow reserves the right to modify, pause, or terminate any investment plan at its discretion with reasonable notice.</li>
    </ul>
  </div>

  <div class="terms-section">
    <h2>4. Deposits & Withdrawals</h2>
    <ul>
      <li>All deposits must be made using the payment methods specified on the platform (cryptocurrency).</li>
      <li>Minimum deposit is $100. Maximum varies by plan.</li>
      <li>Deposits are subject to admin verification before being credited.</li>
      <li>Withdrawal requests are processed within 24–72 business hours after admin approval.</li>
      <li>A withdrawal fee of up to 2% may apply.</li>
      <li>Minimum withdrawal is $50.</li>
      <li>Welthflow reserves the right to request identity verification (KYC) before processing withdrawals.</li>
    </ul>
  </div>

  <div class="terms-section">
    <h2>5. KYC & Identity Verification</h2>
    <p>To comply with anti-money laundering (AML) and know-your-customer (KYC) regulations, we may require you to submit government-issued identity documents. Failure to complete KYC may result in restricted account functionality.</p>
  </div>

  <div class="terms-section">
    <h2>6. Prohibited Activities</h2>
    <ul>
      <li>Using the platform for money laundering, fraud, or any illegal activity.</li>
      <li>Creating fake accounts or providing false information.</li>
      <li>Attempting to hack, disrupt, or reverse-engineer the platform.</li>
      <li>Abusing the referral or bonus system.</li>
      <li>Sharing account credentials with third parties.</li>
    </ul>
  </div>

  <div class="terms-section">
    <h2>7. Risk Disclosure</h2>
    <p>All investments carry risk, including the possible loss of principal. Cryptocurrency and forex markets are highly volatile. Welthflow does not guarantee specific investment outcomes. You should only invest funds you can afford to lose. By investing, you acknowledge and accept these risks.</p>
  </div>

  <div class="terms-section">
    <h2>8. Privacy Policy</h2>
    <p>We collect personal information including name, email, phone number, country, and financial transaction data to operate and improve our services. We do not sell your personal information to third parties.</p>
    <ul>
      <li><strong>Data Use:</strong> Your data is used for account management, transaction processing, KYC verification, and customer support.</li>
      <li><strong>Data Security:</strong> We use industry-standard encryption and security measures to protect your data.</li>
      <li><strong>Cookies:</strong> We use session cookies for authentication. No tracking cookies are shared with advertisers.</li>
      <li><strong>Retention:</strong> We retain your data for as long as your account is active and as required by applicable law.</li>
      <li><strong>Your Rights:</strong> You may request data deletion by contacting support@welthflow.com.</li>
    </ul>
  </div>

  <div class="terms-section">
    <h2>9. Limitation of Liability</h2>
    <p>To the maximum extent permitted by law, Welthflow shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of the platform, including but not limited to loss of profits or investment losses.</p>
  </div>

  <div class="terms-section">
    <h2>10. Governing Law</h2>
    <p>These terms are governed by the laws of England and Wales. Any disputes shall be resolved through binding arbitration in London, United Kingdom.</p>
  </div>

  <div class="terms-section">
    <h2>11. Contact Us</h2>
    <p>For questions about these terms, please contact us at:</p>
    <ul>
      <li>Email: <a href="mailto:support@welthflow.com" style="color:#F97316;">support@welthflow.com</a></li>
      <li>Admin: <a href="mailto:admin@welthflow.com" style="color:#F97316;">admin@welthflow.com</a></li>
    </ul>
  </div>

  <a href="<?= SITE_BASE ?>/register.php" class="terms-back">&#8592; Back to Register</a>
</div>
</body>
</html>
