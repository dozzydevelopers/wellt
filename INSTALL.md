# Asset Gain Investment - PHP Version Installation Guide

## Requirements
- PHP 7.4+ (PHP 8.0+ recommended)
- MySQL 5.7+ or MariaDB 10.3+
- Apache with mod_rewrite enabled
- cURL extension (for future API calls)

## Installation Steps

### 1. Upload Files
Upload all files to your shared hosting root or subdirectory via FTP/cPanel File Manager.

### 2. Create Database
1. Login to cPanel → MySQL Databases
2. Create a new database (e.g., `yourusername_assetgain`)
3. Create a database user with a strong password
4. Assign the user to the database with ALL privileges

### 3. Import Database Schema
1. Go to cPanel → phpMyAdmin
2. Select your new database
3. Click **Import** tab
4. Choose the file `includes/install.sql`
5. Click **Go**

### 4. Configure Database Connection
Edit `includes/db.php`:
```php
define('DB_HOST', 'localhost');         // Usually 'localhost'
define('DB_USER', 'yourusername_dbuser');
define('DB_PASS', 'yourpassword');
define('DB_NAME', 'yourusername_assetgain');
define('SITE_URL', 'https://yourdomain.com');
define('SITE_NAME', 'Your Site Name');
```

### 5. Set File Permissions
```
uploads/           → 755
uploads/kyc/       → 755
uploads/avatars/   → 755
uploads/deposits/  → 755
```
Create these folders if they don't exist.

### 6. Enable mod_rewrite (Apache)
Make sure `.htaccess` is allowed. In cPanel, this is usually enabled by default.

### 7. First Login
- URL: `https://yourdomain.com/public/login.php`
- Admin Email: `admin@assetgain.com`
- Admin Password: `password`
**⚠️ Change the admin password immediately after first login!**

## Setting Up Automated Daily Profits

### Cron Job Setup
1. Login to cPanel → Cron Jobs
2. Set frequency: **Daily at midnight** (`0 0 * * *`)
3. Command:
```bash
curl -s "https://yourdomain.com/public/cron-profit.php?key=YOUR_CRON_KEY" > /dev/null
```
4. Update the cron key in **Admin → Settings** and replace `YOUR_CRON_KEY` above.

### Manual Profit Run
Go to **Admin Panel → Run Daily Profit** and click the button.

## Directory Structure
```
/
├── admin/                    ← Admin panel pages
│   ├── includes/             ← Admin sidebar & topbar
│   └── *.php                 ← Admin pages
├── assets/
│   ├── css/                  ← Stylesheets
│   └── js/                   ← JavaScript
├── includes/                 ← Core files (DB, Auth, SQL)
├── public/                   ← User-facing pages
├── uploads/                  ← File uploads (create manually)
│   ├── avatars/
│   ├── deposits/
│   └── kyc/
└── .htaccess
```

## Admin Credentials (Default)
- URL: `/public/admin/index.php` or `/admin/index.php`  
- Username: `admin`
- Password: `password`

## Security Checklist
- [ ] Change default admin password
- [ ] Set unique SESSION_SECRET in db.php
- [ ] Set strong profit cron key in Settings
- [ ] Enable HTTPS (uncomment redirect in .htaccess)
- [ ] Set correct file permissions on uploads/
- [ ] Keep PHP error display OFF in production
- [ ] Backup database regularly

## Customization
- **Logo/Branding**: Edit `includes/header.php` and `assets/css/style.css`
- **Colors**: Change CSS variables in `assets/css/style.css` (`:root` block)
- **Investment Plans**: Admin → Manage Plans
- **Crypto Wallets**: Admin → Settings
- **Site Name/Contact**: Admin → Settings
