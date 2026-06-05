<?php
if (!defined('DB_HOST'))
    require_once __DIR__ . '/db.php';
$_user = $user ?? getCurrentUser();
$_notifCount = 0;
if ($_user) {
    $r = fetchOne("SELECT COUNT(*) as c FROM notifications WHERE user_id=? AND is_read=0", [$_user['id']]);
    $_notifCount = $r['c'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' - ' : '' ?>Welthflow</title>
    <link rel="stylesheet" href="<?= assetUrl('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= assetUrl('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= assetUrl('assets/css/animations.css') ?>">
</head>

<body class="dashboard-body">