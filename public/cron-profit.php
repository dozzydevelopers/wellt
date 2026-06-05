<?php
/**
 * CRON JOB - Daily Profit Runner
 * Schedule: 0 0 * * * (every day at midnight)
 * URL: https://yourdomain.com/public/cron-profit.php?key=YOUR_CRON_KEY
 */

require_once '../includes/db.php';

$key = $_GET['key'] ?? '';
$setting = fetchOne("SELECT setting_value FROM settings WHERE setting_key='profit_cron_key'");
$cronKey = $setting['setting_value'] ?? 'change-me';

if ($key !== $cronKey) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized']));
}

// Check if already ran today
$lastRun = fetchOne("SELECT setting_value FROM settings WHERE setting_key='last_profit_run'");
if ($lastRun && $lastRun['setting_value'] === date('Y-m-d')) {
    die(json_encode(['message' => 'Profit already run today', 'date' => date('Y-m-d')]));
}

$count = 0;
$totalPaid = 0.0;
$completed = 0;

$investments = fetchAll(
    "SELECT i.*, p.daily_roi, p.name as plan_name FROM investments i JOIN plans p ON i.plan_id=p.id WHERE i.status='active'"
);

foreach ($investments as $inv) {
    // Check if investment has expired
    if (strtotime($inv['end_date']) < time()) {
        query("UPDATE investments SET status='completed' WHERE id=?", [$inv['id']]);
        query("UPDATE users SET portfolio=portfolio-? WHERE id=?", [$inv['amount'], $inv['user_id']]);
        $ref = 'TXN' . strtoupper(uniqid());
        insert("INSERT INTO notifications (user_id,title,message,type) VALUES (?,?,?,?)",
            [$inv['user_id'], 'Investment Completed', "Your {$inv['plan_name']} investment has completed. Principal returned to portfolio.", 'success']);
        $completed++;
        continue;
    }

    $profit = floatval($inv['daily_profit']);
    query("UPDATE investments SET total_profit=total_profit+?, last_profit_date=date('now') WHERE id=?", [$profit, $inv['id']]);
    query("UPDATE users SET balance=balance+?, total_profit=total_profit+? WHERE id=?", [$profit, $profit, $inv['user_id']]);

    $ref = 'TXN' . strtoupper(uniqid());
    insert("INSERT INTO transactions (user_id,type,amount,description,status,reference) VALUES (?,?,?,?,?,?)",
        [$inv['user_id'], 'profit', $profit, "Daily profit - {$inv['plan_name']}", 'completed', $ref]);


    $count++;
    $totalPaid += $profit;
}

// Record last run
$lr = fetchOne("SELECT id FROM settings WHERE setting_key='last_profit_run'");
if ($lr) query("UPDATE settings SET setting_value=? WHERE setting_key='last_profit_run'", [date('Y-m-d')]);
else insert("INSERT INTO settings (setting_key,setting_value) VALUES ('last_profit_run',?)", [date('Y-m-d')]);

echo json_encode([
    'success'         => true,
    'date'            => date('Y-m-d H:i:s'),
    'profits_credited' => $count,
    'total_paid'      => number_format($totalPaid, 2),
    'completed'       => $completed,
]);
