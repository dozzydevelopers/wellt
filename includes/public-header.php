<?php
if (!defined('SITE_BASE')) require_once __DIR__ . '/db.php';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$b = SITE_BASE;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' — ' : '' ?>Welthflow Investments</title>
  <meta name="description" content="<?= isset($pageDesc) ? sanitize($pageDesc) : 'Welthflow — Premier asset management and investment platform. Cryptocurrency, FOREX, Real Estate and more.' ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;600;700&family=Raleway:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= assetUrl('assets/css/welthflow.css') ?>">
  <?php if (isset($extraCss)) echo $extraCss; ?>
</head>
<body>

<nav class="wf-navbar" id="wf-navbar">
  <div class="container">
    <div class="wf-nav-inner">
      <!-- Logo -->
      <a href="<?= $b ?>/" class="wf-logo-link">
        <svg width="200" height="40" viewBox="0 0 220 44" fill="none" xmlns="http://www.w3.org/2000/svg">
          <defs><linearGradient id="wf-grad2" x1="0" y1="0" x2="44" y2="44" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#3eda99"/><stop offset="100%" stop-color="#1dbfc8"/>
          </linearGradient></defs>
          <rect x="0" y="4" width="40" height="36" rx="8" fill="url(#wf-grad2)" opacity="0.15"/>
          <path d="M8 32 L14 16 L20 27 L26 18 L32 32" stroke="url(#wf-grad2)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
          <circle cx="26" cy="11" r="5" fill="url(#wf-grad2)" opacity="0.9"/>
          <path d="M26 6 L27.8 9.5 L32 10.2 L29 13.1 L29.6 17.4 L26 15.5 L22.4 17.4 L23 13.1 L20 10.2 L24.2 9.5 Z" fill="url(#wf-grad2)"/>
          <text x="50" y="31" font-family="'Lora', serif" font-weight="700" font-size="22" fill="#ffffff">Welth</text>
          <text x="103" y="31" font-family="'Lora', serif" font-weight="400" font-size="22" fill="#3eda99">flow</text>
          <text x="50" y="42" font-family="'Raleway', sans-serif" font-weight="400" font-size="9.5" letter-spacing="3" fill="rgba(255,255,255,0.55)">INVESTMENTS</text>
        </svg>
      </a>

      <!-- Hamburger -->
      <button class="wf-hamburger" id="wf-hamburger" aria-label="Menu">
        <i class="fas fa-bars"></i>
      </button>

      <!-- Links -->
      <ul class="wf-nav-links" id="wf-nav-links">
        <li><a href="<?= $b ?>/" class="<?= $currentPage === 'index' ? 'active' : '' ?>">Home</a></li>
        <li><a href="<?= $b ?>/faq.php" class="<?= $currentPage === 'faq' ? 'active' : '' ?>">FAQ</a></li>
        <li id="google_translate_el"></li>
        <li class="wf-dropdown">
          <a href="#">Our Company <i class="fas fa-chevron-down" style="font-size:10px"></i></a>
          <ul class="wf-submenu">
            <li><a href="<?= $b ?>/about.php">About Us</a></li>
            <li><a href="<?= $b ?>/terms.php">Legal &amp; Terms</a></li>
          </ul>
        </li>
        <li class="wf-dropdown">
          <a href="#">Investment Packages <i class="fas fa-chevron-down" style="font-size:10px"></i></a>
          <ul class="wf-submenu">
            <li><a href="<?= $b ?>/investment.php">Cryptocurrency</a></li>
            <li><a href="<?= $b ?>/loan.php">Loan &amp; Pension</a></li>
            <li><a href="<?= $b ?>/forex.php">FOREX (PAMM/MAM)</a></li>
          </ul>
        </li>
        <li><a href="<?= $b ?>/affiliates.php" class="<?= $currentPage === 'affiliates' ? 'active' : '' ?>">Affiliate</a></li>
        <li class="wf-dropdown">
          <a href="#">My Account <i class="fas fa-chevron-down" style="font-size:10px"></i></a>
          <ul class="wf-submenu">
            <li><a href="<?= $b ?>/register.php">Open Account</a></li>
            <li><a href="<?= $b ?>/login.php">Account Login</a></li>
          </ul>
        </li>
        <li>
          <a href="<?= $b ?>/register.php" class="wf-btn-nav">
            <i class="fas fa-user-plus"></i> Get Started
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script>
// Navbar scroll effect
window.addEventListener('scroll', function() {
  var nav = document.getElementById('wf-navbar');
  if (window.scrollY > 60) nav.style.background = '#01123c';
  else if (document.body.dataset.page === 'home') nav.style.background = 'transparent';
});
// Hamburger toggle
document.getElementById('wf-hamburger').addEventListener('click', function() {
  var links = document.getElementById('wf-nav-links');
  links.classList.toggle('open');
  this.querySelector('i').className = links.classList.contains('open') ? 'fas fa-times' : 'fas fa-bars';
});
</script>
