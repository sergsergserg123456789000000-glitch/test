<?php
/**
 * Dynamic logo/favicon endpoint.
 * Совместим с PHP 7.x+
 */
require_once __DIR__ . '/config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'logo';
$settingKey = $type === 'favicon' ? 'site_favicon_path' : 'site_logo_path';
$default = 'images/Logo-Master-Software.ico';

$path = $default;
try {
    $path = getSetting($settingKey, $default);
} catch (Exception $e) {
    $path = $default;
}

// Безопасность: убираем ../ и обратные слэши
$path = str_replace(array('..', '\\'), array('', '/'), $path);
$path = ltrim($path, '/');
$fullPath = APP_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);

if (!is_file($fullPath)) {
    $fullPath = APP_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $default);
}

if (!is_file($fullPath)) {
    http_response_code(404);
    echo 'File not found: ' . $path;
    exit;
}

$ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

// Совместимость с PHP 7.x (без match)
$mimeTypes = array(
    'ico'  => 'image/x-icon',
    'svg'  => 'image/svg+xml',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'webp' => 'image/webp',
    'gif'  => 'image/gif',
);
$mime = isset($mimeTypes[$ext]) ? $mimeTypes[$ext] : 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($fullPath));
header('Cache-Control: public, max-age=300');
readfile($fullPath);
exit;
