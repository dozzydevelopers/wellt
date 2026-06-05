<?php
require_once '../includes/auth.php';
startSession();
if (isLoggedIn())
  redirect(SITE_BASE . '/dashboard.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = sanitize($_POST['username'] ?? '');
  $full_name = sanitize($_POST['full_name'] ?? '');
  $email = sanitize($_POST['email'] ?? '');
  $phone = sanitize($_POST['phone'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirm = $_POST['confirm_password'] ?? '';
  $country = sanitize($_POST['country'] ?? '');
  $referral = sanitize($_POST['referral_id'] ?? '');
  $captcha = intval($_POST['captcha'] ?? 0);
  $terms = $_POST['terms'] ?? '';

  if (!$username || !$full_name || !$email || !$phone || !$password || !$country) {
    $error = 'All required fields must be filled.';
  } elseif ($captcha !== 9) {
    $error = 'Incorrect captcha answer. (4+5=9)';
  } elseif (!$terms) {
    $error = 'You must accept the Terms and Privacy Policy.';
  } elseif (strlen($password) < 6) {
    $error = 'Password must be at least 6 characters.';
  } elseif ($password !== $confirm) {
    $error = 'Passwords do not match.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Invalid email address.';
  } else {
    $existingUser = fetchOne("SELECT id FROM users WHERE email = ? OR username = ?", [$email, $username]);
    if ($existingUser) {
      $error = 'Email or username already exists.';
    } else {
      $refBy = null;
      if ($referral) {
        $refUser = fetchOne("SELECT id FROM users WHERE referral_id = ?", [$referral]);
        if ($refUser)
          $refBy = $refUser['id'];
      }
      $refId = generateReferralId();
      $hashed = hashPassword($password);

      $userId = insert(
        "INSERT INTO users (username, full_name, email, phone, password, country, referral_id, referred_by, bonus, balance) VALUES (?,?,?,?,?,?,?,?,?,?)",
        [$username, $full_name, $email, $phone, $hashed, $country, $refId, $refBy, BONUS_ON_REGISTER, BONUS_ON_REGISTER]
      );

      // Registration bonus transaction
      insert(
        "INSERT INTO transactions (user_id, type, amount, description, status, reference) VALUES (?,?,?,?,?,?)",
        [$userId, 'bonus', BONUS_ON_REGISTER, 'Registration bonus', 'completed', generateReference()]
      );

      // Referral bonus
      if ($refBy) {
        $refBonus = BONUS_ON_REGISTER * 0.5;
        query("UPDATE users SET balance = balance + ? WHERE id = ?", [$refBonus, $refBy]);
        insert(
          "INSERT INTO transactions (user_id, type, amount, description, status, reference) VALUES (?,?,?,?,?,?)",
          [$refBy, 'referral', $refBonus, "Referral bonus from $username", 'completed', generateReference()]
        );
        sendNotification($refBy, 'Referral Bonus', "You earned $" . number_format($refBonus, 2) . " referral bonus from $username!", 'success');
      }

      sendNotification($userId, 'Welcome!', 'Welcome to Welthflow! Your registration bonus of $' . BONUS_ON_REGISTER . ' has been credited.', 'success');

      $user = fetchOne("SELECT * FROM users WHERE id = ?", [$userId]);
      loginUser($user);
      sendWelcomeEmail($user);
      redirect(SITE_BASE . '/set-pin.php');
    }
  }
}

$countries = ['Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan', 'Bolivia', 'Bosnia', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon', 'Canada', 'Chad', 'Chile', 'China', 'Colombia', 'Congo', 'Costa Rica', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador', 'Estonia', 'Ethiopia', 'Fiji', 'Finland', 'France', 'Gabon', 'Georgia', 'Germany', 'Ghana', 'Greece', 'Guatemala', 'Guinea', 'Haiti', 'Honduras', 'Hungary', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel', 'Italy', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kuwait', 'Kyrgyzstan', 'Laos', 'Latvia', 'Lebanon', 'Liberia', 'Libya', 'Lithuania', 'Luxembourg', 'Madagascar', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Mauritania', 'Mauritius', 'Mexico', 'Moldova', 'Mongolia', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia', 'Nepal', 'Netherlands', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'North Korea', 'Norway', 'Oman', 'Pakistan', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal', 'Qatar', 'Romania', 'Russia', 'Rwanda', 'Saudi Arabia', 'Senegal', 'Serbia', 'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Somalia', 'South Africa', 'South Korea', 'Spain', 'Sri Lanka', 'Sudan', 'Sweden', 'Switzerland', 'Syria', 'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Togo', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan', 'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Welthflow</title>
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
      <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div><?php endif; ?>
      <form method="POST" class="auth-form">
        <div class="form-row">
          <div class="form-group">
            <label>Username <span class="req">*</span></label>
            <div class="input-wrap"><span class="input-icon">&#128100;</span>
              <input type="text" name="username" placeholder="Enter Unique Username" required
                value="<?= sanitize($_POST['username'] ?? '') ?>">
            </div>
          </div>
          <div class="form-group">
            <label>Full Name <span class="req">*</span></label>
            <div class="input-wrap"><span class="input-icon">&#128100;</span>
              <input type="text" name="full_name" placeholder="Enter FullName" required
                value="<?= sanitize($_POST['full_name'] ?? '') ?>">
            </div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Email Address <span class="req">*</span></label>
            <div class="input-wrap"><span class="input-icon">&#9993;</span>
              <input type="email" name="email" placeholder="name@example.com" required
                value="<?= sanitize($_POST['email'] ?? '') ?>">
            </div>
          </div>
          <div class="form-group">
            <label>Phone Number <span class="req">*</span></label>
            <div class="input-wrap"><span class="input-icon">&#128222;</span>
              <input type="tel" name="phone" placeholder="Enter Phone number" required
                value="<?= sanitize($_POST['phone'] ?? '') ?>">
            </div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Password <span class="req">*</span></label>
            <div class="input-wrap"><span class="input-icon">&#128273;</span>
              <input type="password" name="password" placeholder="Enter Password" required>
            </div>
          </div>
          <div class="form-group">
            <label>Confirm Password <span class="req">*</span></label>
            <div class="input-wrap"><span class="input-icon">&#128273;</span>
              <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Country <span class="req">*</span></label>
            <div class="input-wrap select-wrap"><span class="input-icon">&#128205;</span>
              <select name="country" required>
                <option value="">Choose Country</option>
                <?php foreach ($countries as $c): ?>
                  <option value="<?= $c ?>" <?= (($_POST['country'] ?? '') === $c) ? 'selected' : '' ?>><?= $c ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Referral ID</label>
            <div class="input-wrap"><span class="input-icon">&#128100;</span>
              <input type="text" name="referral_id" placeholder="Optional referral Id"
                value="<?= sanitize($_POST['referral_id'] ?? '') ?>">
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>What is 4 + 5? <span class="req">*</span></label>
          <div class="input-wrap"><span class="input-icon">&#128270;</span>
            <input type="number" name="captcha" placeholder="Enter the answer" required>
          </div>
        </div>
        <div class="form-group checkbox-group">
          <label class="checkbox-label">
            <input type="checkbox" name="terms" value="1">
            I Accept the <a href="<?= SITE_BASE ?>/terms.php" class="link-orange">Terms And Privacy Policy</a>
          </label>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Register</button>
        <p class="auth-footer">Already have an account? <a href="<?= SITE_BASE ?>/login.php" class="link-orange">Login</a></p>
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