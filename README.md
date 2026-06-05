# Welthflow — Investment Platform
**PHP 8.0+ | SQLite (built-in, no MySQL needed)**

## Requirements
- PHP 8.0 or higher
- PHP extensions: `pdo`, `pdo_sqlite`, `sqlite3` (enabled by default on most hosts)
- Apache with `mod_rewrite` enabled (or Nginx)

## Installation

1. **Upload** all files to your web hosting root (e.g. `public_html/`)
2. Set **write permission (755)** on the `storage/` and `uploads/` directories
3. Visit your domain — the database is created automatically on first load
4. **Log in** at `/public/login.php`
   - Admin: `admin@welthflow.com` / `password` ← **change this immediately!**

## Configure Email (Admin → Settings → Email/SMTP)

| Provider | Host | Port | Encryption |
|----------|------|------|------------|
| Gmail    | smtp.gmail.com | 587 | TLS |
| SendGrid | smtp.sendgrid.net | 587 | TLS |
| Mailgun  | smtp.mailgun.org | 587 | TLS |

> **Gmail:** Generate an [App Password](https://myaccount.google.com/apppasswords) (requires 2FA).

## Automated Emails Sent
- **Welcome** — on registration
- **Deposit Confirmed / Rejected** — when admin approves/rejects
- **Withdrawal Approved / Rejected** — when admin acts
- **Investment Activated** — when user starts a plan
- **KYC Approved / Rejected** — when admin reviews documents
- **Admin Alerts** — new deposits & withdrawals notify admin email

## Directory Structure
```
/
├── admin/          Admin panel
├── assets/css|js   Stylesheets & scripts
├── includes/       Core PHP: db, auth, mailer
├── public/         User-facing pages
├── storage/        SQLite database (auto-created, keep writable)
├── uploads/        User-uploaded files (keep writable)
├── .htaccess       Apache routing rules
└── router.php      PHP built-in server router (dev only)
```

## Cron Job (Daily Profits)
```
0 0 * * * curl -s "https://yourdomain.com/public/cron-profit.php?key=YOUR_CRON_KEY"
```
Set your cron key in Admin → Settings → Automation.
