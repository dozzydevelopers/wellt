<?php
require_once '../includes/auth.php';
startSession();
if (isLoggedIn())
  redirect(SITE_BASE . '/dashboard.php');
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = sanitize($_POST['email'] ?? '');
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Invalid email address.';
  } else {
    $userRow = fetchOne("SELECT * FROM users WHERE email=?", [$email]);
    if ($userRow) {
      $token = bin2hex(random_bytes(32));
      $expiry = date('Y-m-d H:i:s', time() + 3600);
      query("UPDATE users SET remember_token=? WHERE id=?", [$token, $userRow['id']]);
      // In production, send email with reset link
      // For demo: show the token directly
      $resetLink = SITE_URL . "/public/reset-password.php?token=$token";
      $success = "Password reset link generated. In production this would be emailed. Reset link: <a href='$resetLink'>Click here</a>";
    } else {
      $success = "If that email exists, a reset link has been sent."; // Don't reveal if email exists
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Forgot Password - Welthflow</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/style.css') ?>">
</head>

<body class="auth-body">
  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-logo"><span class="logo-bar"></span>
        <div class="logo-text"><span class="logo-main">WELTHFLOW</span><span class="logo-sub">INVESTMENT</span></div>
      </div>
      <h2 class="auth-title">Reset Password</h2>
      <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div><?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div><?php endif; ?>
      <form method="POST" class="auth-form">
        <div class="form-group"><label>Email Address</label>
          <div class="input-wrap"><span class="input-icon">&#9993;</span><input type="email" name="email" required
              placeholder="Enter your email"></div>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Send Reset Link</button>
        <p class="auth-footer"><a href="<?= SITE_BASE ?>/login.php" class="link-orange">&larr; Back to Login</a></p>
      </form>
    </div>
  </div>
<script type="text/javascript">
  var _smartsupp = _smartsupp || {};
  _smartsupp.key = 'e1f6488ccb5c061e24ad09e6ec82da06eead2ef8';
  window.smartsupp||(function(d) {
    var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
    s=d.getElementsByTagName('script')[0];c=d.createElement('script');
    c.type='text/javascript';c.charset='utf-8';c.async=true;
    c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
  })(document);
</script>
</body>
</html>