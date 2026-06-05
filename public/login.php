<?php
require_once '../includes/auth.php';
startSession();
if (isLoggedIn()) {
  redirect(isAdmin() ? SITE_BASE . '/admin/index.php' : SITE_BASE . '/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $login = sanitize($_POST['login'] ?? '');
  $password = $_POST['password'] ?? '';
  $remember = isset($_POST['remember']);

  if (!$login || !$password) {
    $error = 'Please enter your email/username and password.';
  } else {
    $user = fetchOne("SELECT * FROM users WHERE (email = ? OR username = ?) AND status != 'banned'", [$login, $login]);
    if ($user && verifyPassword($password, $user['password'])) {
      if ($user['status'] === 'suspended') {
        $error = 'Your account has been suspended. Contact support.';
      } else {
        loginUser($user);
        if ($remember) {
          $token = bin2hex(random_bytes(32));
          query("UPDATE users SET remember_token = ? WHERE id = ?", [$token, $user['id']]);
          setcookie('remember_token', $token, time() + (86400 * 30), '/', '', false, true);
        }
        redirect($user['is_admin'] ? SITE_BASE . '/admin/index.php' : SITE_BASE . '/dashboard.php');
      }
    } else {
      $error = 'Invalid credentials. Please try again.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Welthflow</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/style.css') ?>">
</head>

<body class="auth-body">
  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-logo">
        <span class="logo-bar"></span>
        <div class="logo-text">
          <span class="logo-main">WELTHFLOW</span>
          <span class="logo-sub">INVESTMENT</span>
        </div>
      </div>
      <h2 class="auth-title">Log In</h2>
      <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div><?php endif; ?>
      <form method="POST" class="auth-form">
        <div class="form-group">
          <label>Email or Username <span class="req">*</span></label>
          <div class="input-wrap"><span class="input-icon">&#128100;</span>
            <input type="text" name="login" placeholder="Email or username" required>
          </div>
        </div>
        <div class="form-group">
          <label>Password <span class="req">*</span></label>
          <div class="input-wrap"><span class="input-icon">&#128273;</span>
            <input type="password" name="password" placeholder="Enter Password" required>
          </div>
        </div>
        <div class="form-row form-row-between">
          <label class="checkbox-label"><input type="checkbox" name="remember"> Remember me</label>
          <a href="<?= SITE_BASE ?>/forgot-password.php" class="link-orange">Forgot password?</a>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Sign In</button>
        <p class="auth-footer">Don't have an account? <a href="<?= SITE_BASE ?>/register.php" class="link-orange">Sign Up</a></p>
        <p class="copyright">&copy; 2026 Welthflow. All Rights Reserved.</p>
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