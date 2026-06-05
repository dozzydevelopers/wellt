<?php
define('SITE_NAME', 'Welthflow');
define('SESSION_SECRET', 'assetgain-secret-change-in-production');
define('BONUS_ON_REGISTER', 8.00);
define('SITE_BASE', rtrim(getenv('PHP_BASE_PATH') ?: '', '/'));
define('DB_PATH', __DIR__ . '/../storage/database.sqlite');
define('SITE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost:8000'));

function assetUrl(string $path): string
{
    return SITE_BASE . '/' . ltrim($path, '/');
}

function siteUrl(string $path = ''): string
{
    return SITE_BASE . '/' . ltrim($path, '/');
}

function getDB(): PDO
{
    static $pdo = null;
    if ($pdo !== null)
        return $pdo;

    $dbDir = dirname(DB_PATH);
    if (!is_dir($dbDir))
        mkdir($dbDir, 0755, true);
    $isNew = !file_exists(DB_PATH);

    $pdo = new PDO('sqlite:' . DB_PATH, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA journal_mode = WAL');
    $pdo->sqliteCreateFunction('NOW', fn() => date('Y-m-d H:i:s'), 0);

    if ($isNew) {
        _createSchema($pdo);
        _seedData($pdo);
    }
    _runMigrations($pdo);
    return $pdo;
}

function _createSchema(PDO $db): void
{
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            full_name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            phone TEXT NOT NULL,
            password TEXT NOT NULL,
            country TEXT NOT NULL,
            referral_id TEXT UNIQUE NOT NULL,
            referred_by INTEGER NULL REFERENCES users(id) ON DELETE SET NULL,
            balance REAL DEFAULT 0.00,
            bonus REAL DEFAULT 0.00,
            total_deposited REAL DEFAULT 0.00,
            total_withdrawn REAL DEFAULT 0.00,
            total_profit REAL DEFAULT 0.00,
            portfolio REAL DEFAULT 0.00,
            kyc_status TEXT DEFAULT 'pending',
            kyc_document TEXT NULL,
            status TEXT DEFAULT 'active',
            is_admin INTEGER DEFAULT 0,
            avatar TEXT NULL,
            email_verified INTEGER DEFAULT 1,
            last_login TEXT NULL,
            remember_token TEXT NULL,
            created_at TEXT DEFAULT (datetime('now')),
            updated_at TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS plans (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT NULL,
            daily_roi REAL NOT NULL,
            duration_days INTEGER NOT NULL,
            min_deposit REAL NOT NULL,
            max_deposit REAL NOT NULL,
            max_return_percent REAL DEFAULT 99.00,
            status TEXT DEFAULT 'active',
            color TEXT DEFAULT '#1E40AF',
            created_at TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS investments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            plan_id INTEGER NOT NULL REFERENCES plans(id) ON DELETE CASCADE,
            amount REAL NOT NULL,
            daily_profit REAL DEFAULT 0.00,
            total_profit REAL DEFAULT 0.00,
            status TEXT DEFAULT 'active',
            start_date TEXT DEFAULT (datetime('now')),
            end_date TEXT NOT NULL,
            last_profit_date TEXT NULL
        );

        CREATE TABLE IF NOT EXISTS transactions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            type TEXT NOT NULL,
            amount REAL NOT NULL,
            description TEXT NULL,
            status TEXT DEFAULT 'pending',
            reference TEXT UNIQUE NOT NULL,
            payment_method TEXT NULL,
            payment_address TEXT NULL,
            proof_image TEXT NULL,
            admin_note TEXT NULL,
            created_at TEXT DEFAULT (datetime('now')),
            updated_at TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS copy_traders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            avatar TEXT NULL,
            followers INTEGER DEFAULT 0,
            roi REAL NOT NULL,
            profit_percent REAL NOT NULL,
            fee_percent REAL DEFAULT 5.00,
            period TEXT DEFAULT '1w',
            bio TEXT NULL,
            status TEXT DEFAULT 'active',
            created_at TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS active_copies (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            trader_id INTEGER NOT NULL REFERENCES copy_traders(id) ON DELETE CASCADE,
            amount REAL NOT NULL,
            profit REAL DEFAULT 0.00,
            status TEXT DEFAULT 'active',
            started_at TEXT DEFAULT (datetime('now')),
            stopped_at TEXT NULL
        );

        CREATE TABLE IF NOT EXISTS binary_trades (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            asset TEXT NOT NULL DEFAULT 'BTC/USD',
            direction TEXT NOT NULL,
            amount REAL NOT NULL,
            entry_price REAL NOT NULL,
            close_price REAL NULL,
            payout_percent REAL DEFAULT 85.00,
            result TEXT DEFAULT 'pending',
            profit REAL DEFAULT 0.00,
            expires_at TEXT NOT NULL,
            created_at TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS stocks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            symbol TEXT UNIQUE NOT NULL,
            name TEXT NOT NULL,
            price REAL NOT NULL,
            change_percent REAL DEFAULT 0.00,
            volume INTEGER DEFAULT 0,
            market_cap INTEGER DEFAULT 0,
            status TEXT DEFAULT 'active',
            updated_at TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS stock_holdings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            stock_id INTEGER NOT NULL REFERENCES stocks(id) ON DELETE CASCADE,
            shares REAL NOT NULL,
            avg_buy_price REAL NOT NULL,
            current_value REAL DEFAULT 0.00,
            profit_loss REAL DEFAULT 0.00,
            created_at TEXT DEFAULT (datetime('now')),
            updated_at TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS crypto_swaps (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            from_currency TEXT NOT NULL,
            to_currency TEXT NOT NULL,
            from_amount REAL NOT NULL,
            to_amount REAL NOT NULL,
            rate REAL NOT NULL,
            fee REAL DEFAULT 0.00,
            status TEXT DEFAULT 'completed',
            created_at TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS withdrawal_addresses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            currency TEXT NOT NULL,
            address TEXT NOT NULL,
            label TEXT NULL,
            is_default INTEGER DEFAULT 0,
            created_at TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS notifications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            title TEXT NOT NULL,
            message TEXT NOT NULL,
            type TEXT DEFAULT 'info',
            is_read INTEGER DEFAULT 0,
            created_at TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            setting_key TEXT UNIQUE NOT NULL,
            setting_value TEXT NOT NULL,
            updated_at TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS kyc_documents (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            doc_type TEXT NOT NULL,
            doc_front TEXT NOT NULL,
            doc_back TEXT NULL,
            selfie TEXT NULL,
            status TEXT DEFAULT 'pending',
            admin_note TEXT NULL,
            submitted_at TEXT DEFAULT (datetime('now')),
            reviewed_at TEXT NULL
        );

        CREATE TABLE IF NOT EXISTS support_tickets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            subject TEXT NOT NULL,
            status TEXT DEFAULT 'open',
            priority TEXT DEFAULT 'normal',
            created_at TEXT DEFAULT (datetime('now')),
            updated_at TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS support_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ticket_id INTEGER NOT NULL REFERENCES support_tickets(id) ON DELETE CASCADE,
            sender_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            message TEXT NOT NULL,
            is_admin INTEGER DEFAULT 0,
            created_at TEXT DEFAULT (datetime('now'))
        );
    ");
}

function _seedData(PDO $db): void
{
    // Admin user — password: password
    $db->exec("INSERT INTO users
        (username, full_name, email, phone, password, country, referral_id, status, is_admin, email_verified)
        VALUES ('admin','Administrator','admin@welthflow.com','+10000000000',
        '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'United States','ADMIN001','active',1,1)");

    $db->exec("INSERT INTO copy_traders (name, followers, roi, profit_percent, fee_percent, period, bio) VALUES
        ('David Kim',    21450, 478.3, 25, 7, '3d', 'Expert forex & crypto scalper with 8 years experience.'),
        ('Marcus Chen',  14832, 312.5, 18, 5, '1w', 'Swing trader specializing in tech stocks and BTC.'),
        ('James Walker', 11263, 234.6, 15, 4, '1w', 'Algorithmic trading across multiple asset classes.'),
        ('Sofia Martinez', 9247, 189.8, 12, 4, '5d', 'Derivatives and options strategist.')");

    $db->exec("INSERT INTO plans (name, description, daily_roi, duration_days, min_deposit, max_deposit, max_return_percent, color) VALUES
        ('Starter Plan',  'Perfect for beginners. Low risk, steady returns.',         20.00, 1, 100.00,   499.00,   20.00, '#22C55E'),
        ('Basic Plan',    'Great for intermediate investors.',                         50.00, 2, 500.00,   999.00,   50.00, '#3B82F6'),
        ('Standard Plan', 'Higher returns for committed investors.',                   75.00, 3, 1000.00,  4999.00,  75.00, '#8B5CF6'),
        ('Diamond Plan',  'Premium plan with excellent daily returns.',                95.00, 4, 1000.00,  5999.00,  95.00, '#F59E0B'),
        ('VIP Plan',      'Exclusive VIP membership with maximum returns.',           100.00, 5, 6000.00, 100000.00, 99.00, '#EF4444')");

    $db->exec("INSERT INTO stocks (symbol, name, price, change_percent, volume) VALUES
        ('AAPL',  'Apple Inc.',      189.50,   1.23, 45000000),
        ('TSLA',  'Tesla Inc.',      248.76,  -2.15, 32000000),
        ('GOOGL', 'Alphabet Inc.',   141.80,   0.87, 18000000),
        ('MSFT',  'Microsoft Corp.', 415.32,   0.54, 22000000),
        ('AMZN',  'Amazon.com Inc.', 185.60,   1.89, 28000000),
        ('BTC',   'Bitcoin',       67450.00,   3.21,        0),
        ('ETH',   'Ethereum',       3512.00,   2.45,        0)");

    $db->exec("INSERT INTO settings (setting_key, setting_value) VALUES
        ('site_name',              'Welthflow'),
        ('site_email',             'support@welthflow.com'),
        ('site_phone',             '+1-800-000-0000'),
        ('min_deposit',            '100'),
        ('max_deposit',            '100000'),
        ('min_withdrawal',         '50'),
        ('max_withdrawal',         '50000'),
        ('withdrawal_fee_percent', '2'),
        ('referral_bonus_percent', '5'),
        ('registration_bonus',     '8'),
        ('btc_wallet',             '1BvBMSEYstWetqTFn5Au4m4GFg7xJaNVN2'),
        ('eth_wallet',             '0x742d35Cc6634C0532925a3b844Bc9e7595f5b63a'),
        ('usdt_wallet',            'TQn9Y2khEsLJW1ChVWFMSMeRDow5KcbLSE'),
        ('maintenance_mode',       '0'),
        ('profit_cron_key',        'secret-cron-key-change-me'),
        ('site_url',               ''),
        ('smtp_host',              ''),
        ('smtp_port',              '587'),
        ('smtp_user',              ''),
        ('smtp_pass',              ''),
        ('smtp_from_name',         'Welthflow'),
        ('smtp_from_email',        'noreply@welthflow.com'),
        ('smtp_encryption',        'tls')");
}

function query($sql, $params = [])
{
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function fetchOne($sql, $params = [])
{
    return query($sql, $params)->fetch();
}

function fetchAll($sql, $params = [])
{
    return query($sql, $params)->fetchAll();
}

function insert($sql, $params = [])
{
    query($sql, $params);
    return getDB()->lastInsertId();
}

function _runMigrations(PDO $db): void
{
    // Idempotent — safe to run every time DB opens
    $cols = [
        "ALTER TABLE users ADD COLUMN transaction_pin TEXT NULL",
        "ALTER TABLE users ADD COLUMN pin_set INTEGER DEFAULT 0",
        "ALTER TABLE copy_traders ADD COLUMN photo_url TEXT NULL",
        "ALTER TABLE copy_traders ADD COLUMN stats_updated_at TEXT NULL",
        "ALTER TABLE plans ADD COLUMN referral_percent REAL DEFAULT 3.0",
        "ALTER TABLE users ADD COLUMN biometric_enabled INTEGER DEFAULT 0",
        "ALTER TABLE users ADD COLUMN face_verified INTEGER DEFAULT 0",
        "ALTER TABLE users ADD COLUMN fingerprint_verified INTEGER DEFAULT 0",
        "ALTER TABLE users ADD COLUMN webauthn_credential TEXT NULL",
        "ALTER TABLE users ADD COLUMN security_level TEXT DEFAULT 'basic'",
    ];
    foreach ($cols as $sql) {
        try {
            $db->exec($sql);
        } catch (\Throwable $e) {
        }
    }

    // Update wallet addresses to the user-configured ones
    $wallets = [
        'btc_wallet' => 'bc1qf4fwmd848wqzpjs4747tvmkds2kchhmqfftke2',
        'eth_wallet' => '0x7204739349596445BCeB53926EdFb22c21D3BACf',
        'usdt_wallet' => '0x7204739349596445BCeB53926EdFb22c21D3BACf',
    ];
    foreach ($wallets as $k => $v) {
        $db->prepare("UPDATE settings SET setting_value=? WHERE setting_key=?")->execute([$v, $k]);
    }
    $db->prepare("INSERT OR IGNORE INTO settings (setting_key,setting_value) VALUES (?,?)")->execute(['xrp_wallet', 'rU3SDRW16ebgjJUeCiXE927BdcY4nwrCWX']);

    // Seed trader photos on first run
    $photos = [
        'David Kim' => 'https://i.pravatar.cc/150?img=33',
        'Marcus Chen' => 'https://i.pravatar.cc/150?img=68',
        'James Walker' => 'https://i.pravatar.cc/150?img=12',
        'Sofia Martinez' => 'https://i.pravatar.cc/150?img=5',
    ];
    foreach ($photos as $name => $url) {
        $db->prepare("UPDATE copy_traders SET photo_url=? WHERE name=? AND photo_url IS NULL")->execute([$url, $name]);
    }

    // Replace old plans with 4 Welthflow plans (check by signature)
    $r = $db->query("SELECT id FROM plans WHERE name='STARTER PLAN'")->fetch(PDO::FETCH_ASSOC);
    if (!$r) {
        // Remove old investments + plans
        $db->exec("DELETE FROM investments");
        $db->exec("DELETE FROM plans");
        $db->exec("INSERT INTO plans (name,description,daily_roi,duration_days,min_deposit,max_deposit,max_return_percent,status,color,referral_percent) VALUES
            ('STARTER PLAN',  '5% ROI in 24 hours. Perfect for new investors.',           5.00, 1,   100.00,    999.00,  5.00,'active','#22C55E',3.0),
            ('GROWTH PLAN',   '10% ROI in 2 days. Grow your capital steadily.',           5.00, 2,  1000.00,   9999.00, 10.00,'active','#3B82F6',3.0),
            ('PREMIUM PLAN',  '15% ROI in 4 days. Accelerate your portfolio.',            3.75, 4, 10000.00,  39999.00, 15.00,'active','#8B5CF6',5.0),
            ('ULTIMATE PLAN', '35% ROI in 7 days. Maximum returns for serious investors.',5.00, 7, 40000.00,9999999.00, 35.00,'active','#F97316',5.0)");
    }

    // Auto-rotate trader stats every 24h
    _autoRotateTraderStats($db);
}

function _autoRotateTraderStats(PDO $db): void
{
    $traders = $db->query("SELECT * FROM copy_traders WHERE status='active'")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($traders as $t) {
        $last = $t['stats_updated_at'] ?? null;
        $stale = !$last || (time() - strtotime($last)) > 86400;
        if ($stale) {
            $roi = round(mt_rand(12000, 58000) / 100, 1);
            $prof = round(mt_rand(800, 4200) / 100, 1);
            $fol = mt_rand(6000, 38000);
            $fee = mt_rand(3, 9);
            $per = ['1d', '3d', '5d', '1w', '2w'][array_rand(['1d', '3d', '5d', '1w', '2w'])];
            $db->prepare("UPDATE copy_traders SET roi=?,profit_percent=?,followers=?,fee_percent=?,period=?,stats_updated_at=datetime('now') WHERE id=?")
                ->execute([$roi, $prof, $fol, $fee, $per, $t['id']]);
        }
    }
}
