<?php
/**
 * Cron endpoint: auto-rotate copy trader statistics every 24h.
 * Call via: GET /public/cron-traders.php?key=YOUR_CRON_KEY
 * Or set up a daily cron: 0 0 * * * curl -s "https://yourdomain.com/public/cron-traders.php?key=KEY"
 */
require_once '../includes/db.php';

$key = $_GET['key'] ?? '';
$dbKey = fetchOne("SELECT setting_value FROM settings WHERE setting_key='profit_cron_key'")['setting_value'] ?? '';

if ($key !== $dbKey && PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$traders = fetchAll("SELECT * FROM copy_traders WHERE status='active'");
$rotated = 0;

foreach ($traders as $trader) {
    $last = $trader['stats_updated_at'] ?? null;
    $stale = !$last || (time() - strtotime($last)) > 86400;
    if ($stale || isset($_GET['force'])) {
        $roi       = round(mt_rand(15000, 55000) / 100, 1);
        $profit    = round(mt_rand(800, 3800) / 100, 1);
        $followers = mt_rand(7000, 35000);
        $fee       = mt_rand(3, 9);
        $periods   = ['1d','3d','5d','1w','2w'];
        $period    = $periods[array_rand($periods)];
        query("UPDATE copy_traders SET roi=?,profit_percent=?,followers=?,fee_percent=?,period=?,stats_updated_at=datetime('now') WHERE id=?",
            [$roi, $profit, $followers, $fee, $period, $trader['id']]);
        $rotated++;
    }
}

echo json_encode(['status' => 'ok', 'rotated' => $rotated, 'total' => count($traders)]);
