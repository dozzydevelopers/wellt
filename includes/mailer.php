<?php
/**
 * Welthflow SMTP Mailer
 * Production-ready email system — no Composer required.
 * Configure SMTP in Admin → Settings → Email / SMTP.
 */

class WelthflowMailer {
    private string $host;
    private int    $port;
    private string $username;
    private string $password;
    private string $encryption;
    private string $fromEmail;
    private string $fromName;

    public function __construct(array $s) {
        $this->host       = $s['smtp_host']       ?? '';
        $this->port       = intval($s['smtp_port'] ?? 587);
        $this->username   = $s['smtp_user']        ?? '';
        $this->password   = $s['smtp_pass']        ?? '';
        $this->encryption = strtolower($s['smtp_encryption'] ?? 'tls');
        $this->fromEmail  = $s['smtp_from_email']  ?? ($s['site_email'] ?? 'noreply@welthflow.com');
        $this->fromName   = $s['smtp_from_name']   ?? ($s['site_name']  ?? 'Welthflow');
    }

    public function send(string $toEmail, string $toName, string $subject, string $html): bool {
        if (empty($this->host) || empty($this->username) || empty($this->password)) {
            return $this->fallbackMail($toEmail, $subject, $html);
        }
        return $this->sendSmtp($toEmail, $toName, $subject, $html);
    }

    private function sendSmtp(string $to, string $name, string $subj, string $body): bool {
        try {
            $ctx  = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
            $addr = $this->encryption === 'ssl' ? "ssl://{$this->host}" : $this->host;
            $sock = @stream_socket_client("{$addr}:{$this->port}", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $ctx);
            if (!$sock) return false;
            stream_set_timeout($sock, 30);

            $this->read($sock);
            $this->cmd($sock, "EHLO welthflow.local");

            if ($this->encryption === 'tls') {
                $this->cmd($sock, "STARTTLS");
                stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                $this->cmd($sock, "EHLO welthflow.local");
            }

            $this->cmd($sock, "AUTH LOGIN");
            $this->cmd($sock, base64_encode($this->username));
            $r = $this->cmd($sock, base64_encode($this->password));
            if (strpos($r, '235') === false) { fclose($sock); return false; }

            $this->cmd($sock, "MAIL FROM:<{$this->fromEmail}>");
            $this->cmd($sock, "RCPT TO:<{$to}>");
            $this->cmd($sock, "DATA");

            $msgId = '<' . time() . '.' . mt_rand() . '@welthflow.com>';
            $msg  = "Date: " . date('r') . "\r\n";
            $msg .= "From: =?UTF-8?B?" . base64_encode($this->fromName) . "?= <{$this->fromEmail}>\r\n";
            $msg .= "To: =?UTF-8?B?" . base64_encode($name) . "?= <{$to}>\r\n";
            $msg .= "Subject: =?UTF-8?B?" . base64_encode($subj) . "?=\r\n";
            $msg .= "Message-ID: {$msgId}\r\n";
            $msg .= "MIME-Version: 1.0\r\n";
            $msg .= "Content-Type: text/html; charset=UTF-8\r\n";
            $msg .= "Content-Transfer-Encoding: base64\r\n";
            $msg .= "\r\n";
            $msg .= chunk_split(base64_encode($body));
            $msg .= "\r\n.\r\n";

            fwrite($sock, $msg);
            $this->read($sock);
            $this->cmd($sock, "QUIT");
            fclose($sock);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function fallbackMail(string $to, string $subj, string $body): bool {
        $h  = "MIME-Version: 1.0\r\n";
        $h .= "Content-type: text/html; charset=UTF-8\r\n";
        $h .= "From: {$this->fromName} <{$this->fromEmail}>\r\n";
        return @mail($to, $subj, $body, $h);
    }

    private function read($sock): string {
        $r = '';
        while ($line = fgets($sock, 512)) {
            $r .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return $r;
    }

    private function cmd($sock, string $c): string {
        fwrite($sock, $c . "\r\n");
        return $this->read($sock);
    }
}

// ─── Bootstrap ───────────────────────────────────────────────────────────────

function getMailer(): WelthflowMailer {
    static $m = null;
    if ($m) return $m;
    $rows = fetchAll("SELECT setting_key, setting_value FROM settings");
    $s = [];
    foreach ($rows as $r) $s[$r['setting_key']] = $r['setting_value'];
    $m = new WelthflowMailer($s);
    return $m;
}

function getSetting(string $key, string $default = ''): string {
    $r = fetchOne("SELECT setting_value FROM settings WHERE setting_key=?", [$key]);
    return $r ? $r['setting_value'] : $default;
}

// ─── Email Template Base ──────────────────────────────────────────────────────

function emailBase(string $preheader, string $bodyHtml): string {
    $siteName = getSetting('site_name', 'Welthflow');
    $year     = date('Y');
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{$siteName}</title>
<style>
  body{margin:0;padding:0;background:#F1F5F9;font-family:'Segoe UI',Arial,sans-serif;}
  a{color:#F97316;text-decoration:none;}
  .preheader{display:none;max-height:0;overflow:hidden;font-size:1px;color:#F1F5F9;}
</style>
</head>
<body>
<div class="preheader">{$preheader}</div>
<table width="100%" cellpadding="0" cellspacing="0" style="background:#F1F5F9;padding:32px 16px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.12);">
      <!-- Header -->
      <tr>
        <td style="background:#0F172A;padding:28px 32px;">
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td>
                <div style="display:inline-block;border-left:4px solid #F97316;padding-left:12px;">
                  <div style="color:#fff;font-size:18px;font-weight:800;letter-spacing:2px;">{$siteName}</div>
                  <div style="color:rgba(255,255,255,.4);font-size:9px;letter-spacing:3px;text-transform:uppercase;">Investment Platform</div>
                </div>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <!-- Body -->
      <tr>
        <td style="background:#fff;padding:36px 32px;">
          {$bodyHtml}
        </td>
      </tr>
      <!-- Footer -->
      <tr>
        <td style="background:#0F172A;padding:20px 32px;text-align:center;">
          <p style="color:rgba(255,255,255,.4);font-size:12px;margin:0 0 6px;">
            &copy; {$year} {$siteName}. All rights reserved.
          </p>
          <p style="color:rgba(255,255,255,.25);font-size:11px;margin:0;">
            This is an automated message, please do not reply directly.
          </p>
        </td>
      </tr>
    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
}

function emailBtn(string $url, string $label): string {
    return "<a href=\"{$url}\" style=\"display:inline-block;background:#F97316;color:#fff;font-weight:700;font-size:14px;padding:13px 28px;border-radius:8px;text-decoration:none;\">{$label}</a>";
}

function emailAlert(string $msg, string $color = '#22C55E'): string {
    return "<div style=\"background:{$color}15;border-left:4px solid {$color};padding:14px 18px;border-radius:0 8px 8px 0;margin:20px 0;color:#1E293B;font-size:14px;\">{$msg}</div>";
}

function emailDivider(): string {
    return "<hr style=\"border:none;border-top:1px solid #E2E8F0;margin:24px 0;\">";
}

function emailRow(string $label, string $value): string {
    return "<tr><td style=\"padding:8px 0;color:#64748B;font-size:13px;\">{$label}</td><td style=\"padding:8px 0;color:#0F172A;font-weight:600;font-size:13px;text-align:right;\">{$value}</td></tr>";
}

// ─── Template Functions ───────────────────────────────────────────────────────

function tplWelcome(array $user): string {
    $name     = htmlspecialchars($user['full_name']);
    $username = htmlspecialchars($user['username']);
    $siteUrl  = getSetting('site_url', '');
    $siteName = getSetting('site_name', 'Welthflow');
    $loginBtn = emailBtn($siteUrl . '/portal/public/login.php', 'Log In to Your Account');
    $body = "
      <h2 style=\"color:#0F172A;font-size:22px;font-weight:800;margin:0 0 8px;\">Welcome to {$siteName}! 🎉</h2>
      <p style=\"color:#64748B;font-size:15px;margin:0 0 20px;\">Hi <strong>{$name}</strong>, your account has been created successfully.</p>
      " . emailAlert("Your account is now active with a registration bonus already credited to your wallet!", '#22C55E') . "
      <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"background:#F8FAFC;border-radius:8px;padding:16px;margin-bottom:24px;\">
        " . emailRow('Username', $username) . "
        " . emailRow('Registration Bonus', formatMoney(BONUS_ON_REGISTER)) . "
        " . emailRow('Status', '<span style=\"color:#22C55E;\">&#9679; Active</span>') . "
      </table>
      <p style=\"color:#64748B;font-size:14px;margin:0 0 24px;\">Start investing today and grow your portfolio with our professionally managed plans.</p>
      {$loginBtn}
      " . emailDivider() . "
      <p style=\"color:#94A3B8;font-size:12px;margin:0;\">If you did not create this account, please contact our support team immediately.</p>
    ";
    return emailBase("Welcome to {$siteName} — your account is ready!", $body);
}

function tplDepositConfirmed(array $user, array $txn): string {
    $name    = htmlspecialchars($user['full_name']);
    $siteUrl = getSetting('site_url', '');
    $btn     = emailBtn($siteUrl . '/portal/public/transactions.php', 'View Transactions');
    $body = "
      <h2 style=\"color:#0F172A;font-size:22px;font-weight:800;margin:0 0 8px;\">Deposit Confirmed ✅</h2>
      <p style=\"color:#64748B;font-size:15px;margin:0 0 20px;\">Hi <strong>{$name}</strong>, your deposit has been approved and credited to your account.</p>
      " . emailAlert("Your account balance has been updated successfully.") . "
      <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"background:#F8FAFC;border-radius:8px;padding:16px;margin-bottom:24px;\">
        " . emailRow('Amount', '<span style="color:#22C55E;font-size:16px;font-weight:800;">' . formatMoney($txn['amount']) . '</span>') . "
        " . emailRow('Method', htmlspecialchars($txn['payment_method'] ?? 'Crypto')) . "
        " . emailRow('Reference', htmlspecialchars($txn['reference'])) . "
        " . emailRow('Status', '<span style="color:#22C55E;">Approved</span>') . "
        " . emailRow('Date', date('M j, Y H:i', strtotime($txn['created_at']))) . "
      </table>
      <p style=\"color:#64748B;font-size:14px;margin:0 0 24px;\">You can now use your balance to invest, trade, or copy top traders.</p>
      {$btn}
    ";
    return emailBase("Deposit of " . formatMoney($txn['amount']) . " has been approved!", $body);
}

function tplDepositRejected(array $user, array $txn, string $note): string {
    $name    = htmlspecialchars($user['full_name']);
    $siteUrl = getSetting('site_url', '');
    $btn     = emailBtn($siteUrl . '/portal/public/deposit.php', 'Try Again');
    $reasonHtml = $note ? "<p style=\"color:#64748B;font-size:14px;\"><strong>Reason:</strong> " . htmlspecialchars($note) . "</p>" : '';
    $body = "
      <h2 style=\"color:#0F172A;font-size:22px;font-weight:800;margin:0 0 8px;\">Deposit Not Approved ❌</h2>
      <p style=\"color:#64748B;font-size:15px;margin:0 0 20px;\">Hi <strong>{$name}</strong>, unfortunately your deposit could not be processed.</p>
      " . emailAlert("Your deposit of " . formatMoney($txn['amount']) . " was not approved.", '#EF4444') . "
      <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"background:#F8FAFC;border-radius:8px;padding:16px;margin-bottom:16px;\">
        " . emailRow('Amount', formatMoney($txn['amount'])) . "
        " . emailRow('Reference', htmlspecialchars($txn['reference'])) . "
        " . emailRow('Status', '<span style="color:#EF4444;">Rejected</span>') . "
      </table>
      {$reasonHtml}
      <p style=\"color:#64748B;font-size:14px;margin:0 0 24px;\">If you believe this is an error, please contact our support team with your transaction reference.</p>
      {$btn}
    ";
    return emailBase("Your deposit of " . formatMoney($txn['amount']) . " was not approved", $body);
}

function tplWithdrawalApproved(array $user, array $txn): string {
    $name    = htmlspecialchars($user['full_name']);
    $siteUrl = getSetting('site_url', '');
    $btn     = emailBtn($siteUrl . '/portal/public/transactions.php', 'View Transactions');
    $body = "
      <h2 style=\"color:#0F172A;font-size:22px;font-weight:800;margin:0 0 8px;\">Withdrawal Approved ✅</h2>
      <p style=\"color:#64748B;font-size:15px;margin:0 0 20px;\">Hi <strong>{$name}</strong>, your withdrawal request has been approved and is being processed.</p>
      " . emailAlert("Your funds are on the way! Transfer typically completes within 1-24 hours depending on the network.") . "
      <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"background:#F8FAFC;border-radius:8px;padding:16px;margin-bottom:24px;\">
        " . emailRow('Amount', '<span style="color:#F97316;font-size:16px;font-weight:800;">' . formatMoney($txn['amount']) . '</span>') . "
        " . emailRow('Method', htmlspecialchars($txn['payment_method'] ?? 'Crypto')) . "
        " . emailRow('Wallet', '<code style="font-size:11px;">' . htmlspecialchars($txn['payment_address'] ?? '-') . '</code>') . "
        " . emailRow('Reference', htmlspecialchars($txn['reference'])) . "
        " . emailRow('Status', '<span style="color:#22C55E;">Approved</span>') . "
      </table>
      {$btn}
    ";
    return emailBase("Withdrawal of " . formatMoney($txn['amount']) . " approved!", $body);
}

function tplWithdrawalRejected(array $user, array $txn, string $note): string {
    $name    = htmlspecialchars($user['full_name']);
    $siteUrl = getSetting('site_url', '');
    $btn     = emailBtn($siteUrl . '/portal/public/withdraw.php', 'Request Again');
    $reasonHtml = $note ? "<p style=\"color:#64748B;font-size:14px;\"><strong>Reason:</strong> " . htmlspecialchars($note) . "</p>" : '';
    $body = "
      <h2 style=\"color:#0F172A;font-size:22px;font-weight:800;margin:0 0 8px;\">Withdrawal Rejected ❌</h2>
      <p style=\"color:#64748B;font-size:15px;margin:0 0 20px;\">Hi <strong>{$name}</strong>, your withdrawal request could not be processed.</p>
      " . emailAlert("Your balance of " . formatMoney($txn['amount']) . " has been refunded to your account.", '#F59E0B') . "
      <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"background:#F8FAFC;border-radius:8px;padding:16px;margin-bottom:16px;\">
        " . emailRow('Amount (Refunded)', formatMoney($txn['amount'])) . "
        " . emailRow('Reference', htmlspecialchars($txn['reference'])) . "
        " . emailRow('Status', '<span style="color:#EF4444;">Rejected</span>') . "
      </table>
      {$reasonHtml}
      <p style=\"color:#64748B;font-size:14px;margin:0 0 24px;\">Your funds have been returned to your wallet. Please contact support if you need assistance.</p>
      {$btn}
    ";
    return emailBase("Withdrawal of " . formatMoney($txn['amount']) . " was rejected — balance refunded", $body);
}

function tplKycApproved(array $user): string {
    $name    = htmlspecialchars($user['full_name']);
    $siteUrl = getSetting('site_url', '');
    $btn     = emailBtn($siteUrl . '/portal/public/dashboard.php', 'Go to Dashboard');
    $body = "
      <h2 style=\"color:#0F172A;font-size:22px;font-weight:800;margin:0 0 8px;\">KYC Verified ✅</h2>
      <p style=\"color:#64748B;font-size:15px;margin:0 0 20px;\">Hi <strong>{$name}</strong>, your identity has been successfully verified!</p>
      " . emailAlert("Your account now has full KYC-verified status, unlocking all platform features.") . "
      <p style=\"color:#64748B;font-size:14px;margin:0 0 24px;\">You now have access to higher withdrawal limits and all premium investment plans.</p>
      {$btn}
    ";
    return emailBase("Your KYC verification is approved!", $body);
}

function tplKycRejected(array $user, string $note): string {
    $name    = htmlspecialchars($user['full_name']);
    $siteUrl = getSetting('site_url', '');
    $btn     = emailBtn($siteUrl . '/portal/public/kyc.php', 'Resubmit Documents');
    $reasonHtml = $note ? "<p style=\"color:#64748B;font-size:14px;\"><strong>Reason:</strong> " . htmlspecialchars($note) . "</p>" : '';
    $body = "
      <h2 style=\"color:#0F172A;font-size:22px;font-weight:800;margin:0 0 8px;\">KYC Not Approved ❌</h2>
      <p style=\"color:#64748B;font-size:15px;margin:0 0 20px;\">Hi <strong>{$name}</strong>, your KYC documents could not be verified.</p>
      " . emailAlert("Please resubmit your documents with the corrections noted below.", '#EF4444') . "
      {$reasonHtml}
      <p style=\"color:#64748B;font-size:14px;margin:0 0 24px;\">Please ensure your documents are clear, valid, and match your account details.</p>
      {$btn}
    ";
    return emailBase("KYC verification requires resubmission", $body);
}

function tplInvestmentStarted(array $user, array $investment, array $plan): string {
    $name    = htmlspecialchars($user['full_name']);
    $siteUrl = getSetting('site_url', '');
    $btn     = emailBtn($siteUrl . '/portal/public/my-plans.php', 'Track Investment');
    $body = "
      <h2 style=\"color:#0F172A;font-size:22px;font-weight:800;margin:0 0 8px;\">Investment Activated 🚀</h2>
      <p style=\"color:#64748B;font-size:15px;margin:0 0 20px;\">Hi <strong>{$name}</strong>, your investment is now live and generating returns.</p>
      " . emailAlert("Daily profits will be credited to your balance automatically.") . "
      <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"background:#F8FAFC;border-radius:8px;padding:16px;margin-bottom:24px;\">
        " . emailRow('Plan', htmlspecialchars($plan['name'])) . "
        " . emailRow('Amount Invested', '<span style="color:#F97316;font-weight:800;">' . formatMoney($investment['amount']) . '</span>') . "
        " . emailRow('Daily ROI', $plan['daily_roi'] . '%') . "
        " . emailRow('Duration', $plan['duration_days'] . ' day(s)') . "
        " . emailRow('Ends On', date('M j, Y', strtotime($investment['end_date']))) . "
      </table>
      {$btn}
    ";
    return emailBase("Your investment of " . formatMoney($investment['amount']) . " is now active!", $body);
}

function tplAdminNewDeposit(array $user, array $txn): string {
    $siteUrl = getSetting('site_url', '');
    $btn     = emailBtn($siteUrl . '/portal/admin/deposits.php', 'Review Deposit');
    $body = "
      <h2 style=\"color:#0F172A;font-size:22px;font-weight:800;margin:0 0 8px;\">New Deposit Submitted 📥</h2>
      <p style=\"color:#64748B;font-size:15px;margin:0 0 20px;\">A user has submitted a new deposit that requires your approval.</p>
      <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"background:#F8FAFC;border-radius:8px;padding:16px;margin-bottom:24px;\">
        " . emailRow('User', htmlspecialchars($user['username']) . ' &lt;' . htmlspecialchars($user['email']) . '&gt;') . "
        " . emailRow('Amount', '<span style="color:#22C55E;font-weight:800;">' . formatMoney($txn['amount']) . '</span>') . "
        " . emailRow('Method', htmlspecialchars($txn['payment_method'] ?? '-')) . "
        " . emailRow('Reference', htmlspecialchars($txn['reference'])) . "
        " . emailRow('Submitted', date('M j, Y H:i', strtotime($txn['created_at']))) . "
      </table>
      {$btn}
    ";
    return emailBase("New deposit of " . formatMoney($txn['amount']) . " pending approval", $body);
}

function tplAdminNewWithdrawal(array $user, array $txn): string {
    $siteUrl = getSetting('site_url', '');
    $btn     = emailBtn($siteUrl . '/portal/admin/withdrawals.php', 'Review Withdrawal');
    $body = "
      <h2 style=\"color:#0F172A;font-size:22px;font-weight:800;margin:0 0 8px;\">New Withdrawal Request 📤</h2>
      <p style=\"color:#64748B;font-size:15px;margin:0 0 20px;\">A user has requested a withdrawal that requires your approval.</p>
      <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"background:#F8FAFC;border-radius:8px;padding:16px;margin-bottom:24px;\">
        " . emailRow('User', htmlspecialchars($user['username']) . ' &lt;' . htmlspecialchars($user['email']) . '&gt;') . "
        " . emailRow('Amount', '<span style="color:#F97316;font-weight:800;">' . formatMoney($txn['amount']) . '</span>') . "
        " . emailRow('Method', htmlspecialchars($txn['payment_method'] ?? '-')) . "
        " . emailRow('Wallet', '<code style="font-size:11px;">' . htmlspecialchars($txn['payment_address'] ?? '-') . '</code>') . "
        " . emailRow('Reference', htmlspecialchars($txn['reference'])) . "
      </table>
      {$btn}
    ";
    return emailBase("Withdrawal request of " . formatMoney($txn['amount']) . " pending approval", $body);
}

// ─── Convenience wrappers ────────────────────────────────────────────────────

function sendWelcomeEmail(array $user): void {
    try { getMailer()->send($user['email'], $user['full_name'], 'Welcome to ' . getSetting('site_name','Welthflow') . '!', tplWelcome($user)); } catch (\Throwable $e) {}
}
function sendDepositApprovedEmail(array $user, array $txn): void {
    try { getMailer()->send($user['email'], $user['full_name'], 'Deposit Confirmed — ' . formatMoney($txn['amount']), tplDepositConfirmed($user, $txn)); } catch (\Throwable $e) {}
}
function sendDepositRejectedEmail(array $user, array $txn, string $note = ''): void {
    try { getMailer()->send($user['email'], $user['full_name'], 'Deposit Not Approved — ' . formatMoney($txn['amount']), tplDepositRejected($user, $txn, $note)); } catch (\Throwable $e) {}
}
function sendWithdrawalApprovedEmail(array $user, array $txn): void {
    try { getMailer()->send($user['email'], $user['full_name'], 'Withdrawal Approved — ' . formatMoney($txn['amount']), tplWithdrawalApproved($user, $txn)); } catch (\Throwable $e) {}
}
function sendWithdrawalRejectedEmail(array $user, array $txn, string $note = ''): void {
    try { getMailer()->send($user['email'], $user['full_name'], 'Withdrawal Rejected — Amount Refunded', tplWithdrawalRejected($user, $txn, $note)); } catch (\Throwable $e) {}
}
function sendKycApprovedEmail(array $user): void {
    try { getMailer()->send($user['email'], $user['full_name'], 'KYC Verification Approved ✅', tplKycApproved($user)); } catch (\Throwable $e) {}
}
function sendKycRejectedEmail(array $user, string $note = ''): void {
    try { getMailer()->send($user['email'], $user['full_name'], 'KYC Verification — Action Required', tplKycRejected($user, $note)); } catch (\Throwable $e) {}
}
function sendInvestmentStartedEmail(array $user, array $investment, array $plan): void {
    try { getMailer()->send($user['email'], $user['full_name'], 'Investment Activated — ' . $plan['name'], tplInvestmentStarted($user, $investment, $plan)); } catch (\Throwable $e) {}
}
function sendAdminNewDepositEmail(array $user, array $txn): void {
    $adminEmail = getSetting('site_email', '');
    if (!$adminEmail) return;
    try { getMailer()->send($adminEmail, 'Admin', 'New Deposit — ' . formatMoney($txn['amount']) . ' from ' . $user['username'], tplAdminNewDeposit($user, $txn)); } catch (\Throwable $e) {}
}
function sendAdminNewWithdrawalEmail(array $user, array $txn): void {
    $adminEmail = getSetting('site_email', '');
    if (!$adminEmail) return;
    try { getMailer()->send($adminEmail, 'Admin', 'New Withdrawal — ' . formatMoney($txn['amount']) . ' from ' . $user['username'], tplAdminNewWithdrawal($user, $txn)); } catch (\Throwable $e) {}
}
