<?php
/**
 * PROFESSIONAL SOFTWARE - Configuration
 */

// Запрет прямого доступа
defined('APP_ROOT') or define('APP_ROOT', __DIR__);
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// Относительные пути — работает на любом домене и в любой директории
define('INCLUDES_DIR', APP_ROOT . DS . 'includes');
define('UPLOADS_DIR', APP_ROOT . DS . 'uploads');
define('IMAGES_DIR', APP_ROOT . DS . 'images');
define('TEMPLATES_DIR', APP_ROOT . DS . 'templates');
define('LOGS_DIR', APP_ROOT . DS . 'logs');
define('DB_FILE', APP_ROOT . DS . 'database.sqlite');
define('DB_SCHEMA_FILE', APP_ROOT . DS . 'database.sqlite.sql');

// Создаём необходимые директории
foreach ([UPLOADS_DIR, IMAGES_DIR, LOGS_DIR] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// ============================================
// DATABASE CONFIGURATION
// По умолчанию используется SQLite-файл рядом с index.html.
// Это позволяет переносить сайт на любой домен/поддомен/папку без настройки MySQL.
// ============================================
define('DB_DRIVER', 'sqlite'); // sqlite | mysql
define('DB_PATH', DB_FILE);

// MySQL-настройки нужны только если DB_DRIVER = mysql
define('DB_HOST', 'localhost');
define('DB_NAME', 'professional_software');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ============================================
// SITE CONFIGURATION — автоопределение домена
// ============================================
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$base_path = preg_replace('#/(admin|auth)(/.*)?$#', '', $script_dir);
$base_path = rtrim($base_path, '/');
$base_path = ($base_path === '/' || $base_path === '.') ? '' : $base_path;

define('SITE_NAME', 'PROFESSIONAL SOFTWARE');
define('SITE_URL', $protocol . '://' . $host . ($base_path === '' ? '' : $base_path));
define('SITE_BASE', $base_path === '' ? '' : $base_path);
define('SITE_EMAIL', 'info@mastersoftware.ru');
define('SITE_PHONE', '+7 (812) 945-31-43');
define('SITE_ADDRESS', '198334, г. Санкт-Петербург, пр. Ветеранов, д. 140, офис 1');

// ============================================
// SECURITY
// ============================================
define('SESSION_LIFETIME', 3600 * 8);
define('VERIFICATION_CODE_LENGTH', 6);
define('VERIFICATION_CODE_EXPIRY', 30);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 30);
define('PASSWORD_MIN_LENGTH', 8);

// ============================================
// FILE UPLOADS
// ============================================
define('MAX_FILE_SIZE', 524288000);
define('ALLOWED_EXTENSIONS', ['exe', 'msi', 'dmg', 'pkg', 'zip', 'pdf', 'jpg', 'jpeg', 'png', 'webp', 'ico', 'svg']);

// ============================================
// EMAIL CONFIGURATION (SMTP)
// ============================================
define('SMTP_HOST', 'smtp.yandex.ru');
define('SMTP_PORT', 465);
define('SMTP_USER', 'info@mastersoftware.ru');
define('SMTP_PASS', '');
define('SMTP_SECURE', 'ssl');
define('SMTP_FROM_NAME', SITE_NAME);

// ============================================
// SOCIAL AUTH (OAuth) — относительные URL
// ============================================
define('VK_CLIENT_ID', '');
define('VK_CLIENT_SECRET', '');
define('VK_REDIRECT_URI', SITE_URL . '/auth/vk/callback.php');

define('OK_CLIENT_ID', '');
define('OK_CLIENT_SECRET', '');
define('OK_REDIRECT_URI', SITE_URL . '/auth/ok/callback.php');

define('YANDEX_CLIENT_ID', '');
define('YANDEX_CLIENT_SECRET', '');
define('YANDEX_REDIRECT_URI', SITE_URL . '/auth/yandex/callback.php');

define('MAILRU_CLIENT_ID', '');
define('MAILRU_CLIENT_SECRET', '');
define('MAILRU_REDIRECT_URI', SITE_URL . '/auth/mail/callback.php');

// ============================================
// TIMEZONE & ERROR LOGGING
// ============================================
date_default_timezone_set('Europe/Moscow');
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', LOGS_DIR . DS . 'error.log');
error_reporting(E_ALL);

// ============================================
// SESSION — только если сессия ещё не запущена
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
}
