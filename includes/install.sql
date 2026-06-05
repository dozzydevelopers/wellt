-- Asset Gain Investment - Database Schema
-- Run this SQL in your phpMyAdmin or MySQL client

CREATE DATABASE IF NOT EXISTS asset_gain_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE asset_gain_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    country VARCHAR(60) NOT NULL,
    referral_id VARCHAR(20) UNIQUE NOT NULL,
    referred_by INT NULL,
    balance DECIMAL(18,2) DEFAULT 0.00,
    bonus DECIMAL(18,2) DEFAULT 0.00,
    total_deposited DECIMAL(18,2) DEFAULT 0.00,
    total_withdrawn DECIMAL(18,2) DEFAULT 0.00,
    total_profit DECIMAL(18,2) DEFAULT 0.00,
    portfolio DECIMAL(18,2) DEFAULT 0.00,
    kyc_status ENUM('pending','submitted','approved','rejected') DEFAULT 'pending',
    kyc_document VARCHAR(255) NULL,
    status ENUM('active','suspended','banned') DEFAULT 'active',
    is_admin TINYINT(1) DEFAULT 0,
    avatar VARCHAR(255) NULL,
    email_verified TINYINT(1) DEFAULT 1,
    last_login DATETIME NULL,
    remember_token VARCHAR(100) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (referred_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Investment plans
CREATE TABLE IF NOT EXISTS plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    daily_roi DECIMAL(5,2) NOT NULL COMMENT 'Daily ROI percentage',
    duration_days INT NOT NULL,
    min_deposit DECIMAL(18,2) NOT NULL,
    max_deposit DECIMAL(18,2) NOT NULL,
    max_return_percent DECIMAL(5,2) DEFAULT 99.00,
    status ENUM('active','inactive') DEFAULT 'active',
    color VARCHAR(20) DEFAULT '#1E40AF',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- User investments (active plans)
CREATE TABLE IF NOT EXISTS investments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    amount DECIMAL(18,2) NOT NULL,
    daily_profit DECIMAL(18,2) DEFAULT 0.00,
    total_profit DECIMAL(18,2) DEFAULT 0.00,
    status ENUM('active','completed','cancelled') DEFAULT 'active',
    start_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    end_date DATETIME NOT NULL,
    last_profit_date DATE NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('deposit','withdrawal','profit','bonus','referral','transfer') NOT NULL,
    amount DECIMAL(18,2) NOT NULL,
    description TEXT NULL,
    status ENUM('pending','approved','rejected','completed') DEFAULT 'pending',
    reference VARCHAR(100) UNIQUE NOT NULL,
    payment_method VARCHAR(50) NULL,
    payment_address VARCHAR(255) NULL,
    proof_image VARCHAR(255) NULL,
    admin_note TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Copy traders
CREATE TABLE IF NOT EXISTS copy_traders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    avatar VARCHAR(255) NULL,
    followers INT DEFAULT 0,
    roi DECIMAL(7,2) NOT NULL,
    profit_percent DECIMAL(5,2) NOT NULL,
    fee_percent DECIMAL(5,2) DEFAULT 5.00,
    period VARCHAR(20) DEFAULT '1w',
    bio TEXT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Active copies (user copying a trader)
CREATE TABLE IF NOT EXISTS active_copies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    trader_id INT NOT NULL,
    amount DECIMAL(18,2) NOT NULL,
    profit DECIMAL(18,2) DEFAULT 0.00,
    status ENUM('active','stopped') DEFAULT 'active',
    started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    stopped_at DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (trader_id) REFERENCES copy_traders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Binary trade history
CREATE TABLE IF NOT EXISTS binary_trades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    asset VARCHAR(20) NOT NULL DEFAULT 'BTC/USD',
    direction ENUM('up','down') NOT NULL,
    amount DECIMAL(18,2) NOT NULL,
    entry_price DECIMAL(18,8) NOT NULL,
    close_price DECIMAL(18,8) NULL,
    payout_percent DECIMAL(5,2) DEFAULT 85.00,
    result ENUM('win','lose','pending') DEFAULT 'pending',
    profit DECIMAL(18,2) DEFAULT 0.00,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Stocks
CREATE TABLE IF NOT EXISTS stocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    symbol VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(18,4) NOT NULL,
    change_percent DECIMAL(7,2) DEFAULT 0.00,
    volume BIGINT DEFAULT 0,
    market_cap BIGINT DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- User stock portfolio
CREATE TABLE IF NOT EXISTS stock_holdings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    stock_id INT NOT NULL,
    shares DECIMAL(18,8) NOT NULL,
    avg_buy_price DECIMAL(18,4) NOT NULL,
    current_value DECIMAL(18,2) DEFAULT 0.00,
    profit_loss DECIMAL(18,2) DEFAULT 0.00,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (stock_id) REFERENCES stocks(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Swap crypto records
CREATE TABLE IF NOT EXISTS crypto_swaps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    from_currency VARCHAR(20) NOT NULL,
    to_currency VARCHAR(20) NOT NULL,
    from_amount DECIMAL(18,8) NOT NULL,
    to_amount DECIMAL(18,8) NOT NULL,
    rate DECIMAL(18,8) NOT NULL,
    fee DECIMAL(18,8) DEFAULT 0.00,
    status ENUM('completed','pending','failed') DEFAULT 'completed',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Withdrawal addresses
CREATE TABLE IF NOT EXISTS withdrawal_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    currency VARCHAR(20) NOT NULL,
    address VARCHAR(255) NOT NULL,
    label VARCHAR(100) NULL,
    is_default TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Notifications
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info','success','warning','error') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Site settings
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- KYC documents
CREATE TABLE IF NOT EXISTS kyc_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    doc_type ENUM('passport','national_id','drivers_license','utility_bill') NOT NULL,
    doc_front VARCHAR(255) NOT NULL,
    doc_back VARCHAR(255) NULL,
    selfie VARCHAR(255) NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    admin_note TEXT NULL,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    reviewed_at DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Support tickets
CREATE TABLE IF NOT EXISTS support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(200) NOT NULL,
    status ENUM('open','in_progress','closed') DEFAULT 'open',
    priority ENUM('low','normal','high','urgent') DEFAULT 'normal',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Support messages
CREATE TABLE IF NOT EXISTS support_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES support_tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================
-- SEED DATA
-- =====================

-- Default admin
INSERT INTO users (username, full_name, email, phone, password, country, referral_id, status, is_admin, email_verified)
VALUES ('admin', 'Administrator', 'admin@assetgain.com', '+1000000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'United States', 'ADMIN001', 'active', 1, 1);
-- Default admin password is: password

-- Sample copy traders
INSERT INTO copy_traders (name, avatar, followers, roi, profit_percent, fee_percent, period) VALUES
('David Kim', NULL, 21450, 478.3, 25, 7, '3d'),
('Marcus Chen', NULL, 14832, 312.5, 18, 5, '1w'),
('James Walker', NULL, 11263, 234.6, 15, 4, '1w'),
('Sofia Martinez', NULL, 9247, 189.8, 12, 4, '5d');

-- Investment plans
INSERT INTO plans (name, description, daily_roi, duration_days, min_deposit, max_deposit, max_return_percent, color) VALUES
('Starter Plan', 'Perfect for beginners. Low risk, steady returns.', 20.00, 1, 100.00, 499.00, 20.00, '#22C55E'),
('Basic Plan', 'Great for intermediate investors.', 50.00, 2, 500.00, 999.00, 50.00, '#3B82F6'),
('Standard Plan', 'Higher returns for committed investors.', 75.00, 3, 1000.00, 4999.00, 75.00, '#8B5CF6'),
('Diamond Plan', 'Premium plan with excellent daily returns.', 95.00, 4, 1000.00, 5999.00, 95.00, '#F59E0B'),
('VIP Plan', 'Exclusive VIP membership with maximum returns.', 100.00, 5, 6000.00, 100000.00, 99.00, '#EF4444');

-- Sample stocks
INSERT INTO stocks (symbol, name, price, change_percent, volume) VALUES
('AAPL', 'Apple Inc.', 189.50, 1.23, 45000000),
('TSLA', 'Tesla Inc.', 248.76, -2.15, 32000000),
('GOOGL', 'Alphabet Inc.', 141.80, 0.87, 18000000),
('MSFT', 'Microsoft Corp.', 415.32, 0.54, 22000000),
('AMZN', 'Amazon.com Inc.', 185.60, 1.89, 28000000),
('BTC', 'Bitcoin', 67450.00, 3.21, 0),
('ETH', 'Ethereum', 3512.00, 2.45, 0);

-- Default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Asset Gain Investment'),
('site_email', 'support@assetgain.com'),
('site_phone', '+1-800-000-0000'),
('min_deposit', '100'),
('max_deposit', '100000'),
('min_withdrawal', '50'),
('max_withdrawal', '50000'),
('withdrawal_fee_percent', '2'),
('referral_bonus_percent', '5'),
('registration_bonus', '8'),
('btc_wallet', '1BvBMSEYstWetqTFn5Au4m4GFg7xJaNVN2'),
('eth_wallet', '0x742d35Cc6634C0532925a3b844Bc9e7595f5b63a'),
('usdt_wallet', 'TQn9Y2khEsLJW1ChVWFMSMeRDow5KcbLSE'),
('maintenance_mode', '0'),
('profit_cron_key', 'your-secret-cron-key-here');
