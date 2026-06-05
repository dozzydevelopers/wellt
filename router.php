<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Strip configured base path prefix (set via PHP_BASE_PATH env var).
// The Replit proxy does NOT rewrite paths — the full path arrives here.
$basePath = rtrim(getenv('PHP_BASE_PATH') ?: '', '/');
if ($basePath !== '' && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}
if ($uri === '' || $uri === false) {
    $uri = '/';
}

$rootDir   = __DIR__;
$publicDir = $rootDir . '/public';
$spaIndex  = $rootDir . '/dist/index.html';

// ── Helper: serve a static file with the correct Content-Type ────────────────
function serveStatic(string $path): void {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $map = [
        'css'   => 'text/css; charset=utf-8',
        'js'    => 'application/javascript; charset=utf-8',
        'mjs'   => 'application/javascript; charset=utf-8',
        'json'  => 'application/json; charset=utf-8',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'webp'  => 'image/webp',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'otf'   => 'font/otf',
        'pdf'   => 'application/pdf',
        'txt'   => 'text/plain; charset=utf-8',
        'html'  => 'text/html; charset=utf-8',
    ];
    header('Content-Type: ' . ($map[$ext] ?? 'application/octet-stream'));
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

// ── Files in public/ ──────────────────────────────────────────────────────────
$publicFile = $publicDir . $uri;
if (file_exists($publicFile) && !is_dir($publicFile)) {
    if (pathinfo($publicFile, PATHINFO_EXTENSION) === 'php') {
        chdir(dirname($publicFile));
        include $publicFile;
        exit;
    }
    serveStatic($publicFile);
}

// ── Files outside public/ (admin/, etc.) ─────────────────────────────────────
$rootFile = $rootDir . $uri;
if (file_exists($rootFile) && !is_dir($rootFile)) {
    if (pathinfo($rootFile, PATHINFO_EXTENSION) === 'php') {
        chdir(dirname($rootFile));
        include $rootFile;
        exit;
    }
    serveStatic($rootFile);
}


// ── Dist assets ───────────────────────────────────────────────────────────────
$distFile = $rootDir . '/dist' . $uri;
if (file_exists($distFile) && !is_dir($distFile)) {
    serveStatic($distFile);
}

// ── Root → public/index.php ───────────────────────────────────────────────────
if ($uri === '/' || $uri === '') {
    $indexPhp = $publicDir . '/index.php';
    if (file_exists($indexPhp)) {
        chdir($publicDir);
        include $indexPhp;
        exit;
    }
    if (file_exists($spaIndex)) {
        header('Content-Type: text/html; charset=utf-8');
        readfile($spaIndex);
        exit;
    }
    header('Location: ' . $basePath . '/login.php');
    exit;
}

// ── Clean URL fallback: /faq → /faq.php, /about → /about.php etc. ────────────
$cleanPhp = $publicDir . rtrim($uri, '/') . '.php';
if (file_exists($cleanPhp)) {
    chdir($publicDir);
    include $cleanPhp;
    exit;
}

// ── SPA fallback ──────────────────────────────────────────────────────────────
if (file_exists($spaIndex)) {
    header('Content-Type: text/html; charset=utf-8');
    readfile($spaIndex);
    exit;
}

// ── 404 ───────────────────────────────────────────────────────────────────────
http_response_code(404);
echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body style="font-family:sans-serif;text-align:center;padding:60px"><h1>404 Not Found</h1><p><a href="' . htmlspecialchars($basePath . '/') . '">Go to Home</a></p></body></html>';
