<?php
require_once __DIR__ . '/db.php';

function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.cookie_path', '/');
        session_start();
    }
}

function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    startSession();
    return isLoggedIn() && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_BASE . '/login.php');
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ' . SITE_BASE . '/login.php');
        exit;
    }
}

function getCurrentUser() {
    startSession();
    if (!isLoggedIn()) return null;
    return fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

function loginUser($user) {
    startSession();
    session_regenerate_id(false);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['is_admin'] = $user['is_admin'];
    $_SESSION['full_name'] = $user['full_name'];
    session_write_close();
    query("UPDATE users SET last_login = datetime('now') WHERE id = ?", [$user['id']]);
}

function logoutUser() {
    startSession();
    session_destroy();
    setcookie('remember_token', '', time() - 3600, '/');
    header('Location: ' . SITE_BASE . '/login.php');
    exit;
}

function generateReferralId($length = 8) {
    do {
        $id = strtoupper(substr(md5(uniqid(rand(), true)), 0, $length));
        $exists = fetchOne("SELECT id FROM users WHERE referral_id = ?", [$id]);
    } while ($exists);
    return $id;
}

function generateReference() {
    return 'TXN' . strtoupper(uniqid());
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function sendNotification($userId, $title, $message, $type = 'info') {
    query("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)",
        [$userId, $title, $message, $type]);
}

function formatMoney($amount) {
    return '$' . number_format((float)$amount, 2);
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function redirect($url) {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    header("Location: $url");
    exit;
}

function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function hashPin(string $pin): string {
    return password_hash($pin, PASSWORD_BCRYPT);
}
function verifyPin(string $hash, string $input): bool {
    return password_verify($input, $hash);
}
function requirePinSetup(): void {
    $user = getCurrentUser();
    if (!$user) return;
    if (empty($user['transaction_pin'])) {
        $_SESSION['pin_redirect'] = $_SERVER['REQUEST_URI'];
        redirect(SITE_BASE . '/set-pin.php');
    }
}

require_once __DIR__ . '/mailer.php';
