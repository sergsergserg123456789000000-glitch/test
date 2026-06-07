<?php
/**
 * Visitor Tracker API
 * Endpoint для приёма данных о посетителях с фронтенда
 */
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$db = db();
$input = json_decode(file_get_contents('php://input'), true) ?: [];

// Гарантируем наличие колонки created_ts (Unix-время) для корректного расчёта длительности
try {
    $cols = $db->query("PRAGMA table_info(visitors)")->fetchAll(PDO::FETCH_ASSOC);
    $hasTs = false;
    foreach ($cols as $col) { if ($col['name'] === 'created_ts') { $hasTs = true; break; } }
    if (!$hasTs) {
        $db->exec("ALTER TABLE visitors ADD COLUMN created_ts INTEGER DEFAULT 0");
    }
} catch (Exception $e) {}

$action = $_GET['action'] ?? $input['action'] ?? 'pageview';
$visitor_id = $_COOKIE['ps_visitor'] ?? null;
$session_id = $_COOKIE['ps_session'] ?? null;

// Создаём visitor_id если нет
if (!$visitor_id) {
    $visitor_id = bin2hex(random_bytes(16));
    setcookie('ps_visitor', $visitor_id, time() + 365*24*3600, '/', '', false, true);
}

// Создаём session_id если нет или истёк
if (!$session_id) {
    $session_id = bin2hex(random_bytes(16));
    setcookie('ps_session', $session_id, time() + 30*60, '/', '', false, true); // 30 минут
}

$ip = getClientIP();
$user_agent = getUserAgent();
$referer = $_SERVER['HTTP_REFERER'] ?? $input['referer'] ?? '';
$url = $input['url'] ?? '';
$page_title = $input['title'] ?? '';

// ============================================
// PAGEVIEW: фиксируем просмотр страницы
// ============================================
if ($action === 'pageview') {
    // Проверяем есть ли сессия в БД
    $stmt = $db->prepare("SELECT * FROM visitors WHERE session_id = ?");
    $stmt->execute([$session_id]);
    $visitor = $stmt->fetch();
    
    if (!$visitor) {
        // Новая сессия — собираем гео-данные
        $geo = getGeoData($ip);
        $device = parseUserAgent($user_agent);
        $source = parseReferer($referer);
        
        $stmt = $db->prepare("INSERT INTO visitors (
            visitor_id, session_id, ip_address, user_agent, 
            country, country_code, region, city, isp, 
            browser, os, device_type, 
            referer, source, first_url, 
            pages_count, duration_seconds, created_ts,
            created_at, last_activity
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
        
        $stmt->execute([
            $visitor_id, $session_id, $ip, $user_agent,
            $geo['country'], $geo['country_code'], $geo['region'], $geo['city'], $geo['isp'],
            $device['browser'], $device['os'], $device['device_type'],
            $referer, $source, $url,
            time()
        ]);
        
        $visitor_db_id = $db->lastInsertId();
    } else {
        // Существующая сессия — обновляем
        $startTs = !empty($visitor['created_ts']) ? (int)$visitor['created_ts'] : time();
        $duration = max(0, time() - $startTs);
        // Защита: длительность не может превышать 12 часов (отсекаем мусор)
        if ($duration > 43200) { $duration = (int)$visitor['duration_seconds']; }
        $stmt = $db->prepare("UPDATE visitors SET pages_count = pages_count + 1, duration_seconds = ?, last_activity = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$duration, $visitor['id']]);
        $visitor_db_id = $visitor['id'];
    }
    
    // Сохраняем pageview
    $stmt = $db->prepare("INSERT INTO visitor_pageviews (visitor_id, url, page_title, created_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
    $stmt->execute([$visitor_db_id, $url, $page_title]);
    
    echo json_encode(['success' => true, 'visitor_id' => $visitor_id, 'session_id' => $session_id]);
    exit;
}

// ============================================
// HEARTBEAT: обновление времени на сайте
// ============================================
if ($action === 'heartbeat') {
    $stmt = $db->prepare("SELECT id, created_ts, duration_seconds FROM visitors WHERE session_id = ?");
    $stmt->execute([$session_id]);
    $visitor = $stmt->fetch();
    
    if ($visitor) {
        $startTs = !empty($visitor['created_ts']) ? (int)$visitor['created_ts'] : time();
        $duration = max(0, time() - $startTs);
        // Защита от мусора: не больше 12 часов
        if ($duration > 43200) { $duration = (int)$visitor['duration_seconds']; }
        $db->prepare("UPDATE visitors SET duration_seconds = ?, last_activity = CURRENT_TIMESTAMP WHERE id = ?")
           ->execute([$duration, $visitor['id']]);
    }
    
    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['error' => 'Unknown action']);

// ============================================
// HELPER FUNCTIONS
// ============================================
function getGeoData($ip) {
    $default = ['country' => 'Unknown', 'country_code' => '', 'region' => '', 'city' => '', 'isp' => ''];
    
    if (in_array($ip, ['127.0.0.1', '::1', '0.0.0.0']) || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0) {
        return ['country' => 'Local', 'country_code' => 'LO', 'region' => '', 'city' => 'Localhost', 'isp' => 'Local Network'];
    }
    
    // Используем ip-api.com (бесплатно, без ключа)
    $url = "http://ip-api.com/json/{$ip}?lang=ru&fields=status,country,countryCode,regionName,city,isp,org";
    
    $context = stream_context_create(['http' => ['timeout' => 2]]);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return $default;
    }
    
    $data = json_decode($response, true);
    if (!$data || ($data['status'] ?? '') !== 'success') {
        return $default;
    }
    
    return [
        'country' => $data['country'] ?? 'Unknown',
        'country_code' => $data['countryCode'] ?? '',
        'region' => $data['regionName'] ?? '',
        'city' => $data['city'] ?? '',
        'isp' => $data['isp'] ?? $data['org'] ?? ''
    ];
}

function parseUserAgent($ua) {
    $browser = 'Unknown';
    $os = 'Unknown';
    $device = 'desktop';
    
    // Браузер
    if (preg_match('/Edg\/[\d.]+/', $ua)) $browser = 'Edge';
    elseif (preg_match('/YaBrowser\/[\d.]+/', $ua)) $browser = 'Яндекс.Браузер';
    elseif (preg_match('/OPR\/[\d.]+/', $ua) || preg_match('/Opera/', $ua)) $browser = 'Opera';
    elseif (preg_match('/Chrome\/[\d.]+/', $ua)) $browser = 'Chrome';
    elseif (preg_match('/Firefox\/[\d.]+/', $ua)) $browser = 'Firefox';
    elseif (preg_match('/Safari\/[\d.]+/', $ua)) $browser = 'Safari';
    
    // ОС
    if (preg_match('/Windows NT 10/', $ua)) $os = 'Windows 10/11';
    elseif (preg_match('/Windows NT/', $ua)) $os = 'Windows';
    elseif (preg_match('/Mac OS X/', $ua)) $os = 'macOS';
    elseif (preg_match('/Android/', $ua)) $os = 'Android';
    elseif (preg_match('/iPhone|iPad/', $ua)) $os = 'iOS';
    elseif (preg_match('/Linux/', $ua)) $os = 'Linux';
    
    // Устройство
    if (preg_match('/Mobile|Android|iPhone/i', $ua)) $device = 'mobile';
    elseif (preg_match('/iPad|Tablet/i', $ua)) $device = 'tablet';
    
    return ['browser' => $browser, 'os' => $os, 'device_type' => $device];
}

function parseReferer($referer) {
    if (!$referer) return 'Прямой заход';
    
    $host = parse_url($referer, PHP_URL_HOST);
    if (!$host) return 'Прямой заход';
    
    // Текущий домен = прямой
    if ($host === ($_SERVER['HTTP_HOST'] ?? '')) return 'Прямой заход';
    
    // Поисковики
    if (preg_match('/yandex\./i', $host)) return 'Яндекс';
    if (preg_match('/google\./i', $host)) return 'Google';
    if (preg_match('/mail\.ru/i', $host)) return 'Mail.ru';
    if (preg_match('/bing\./i', $host)) return 'Bing';
    if (preg_match('/duckduckgo\./i', $host)) return 'DuckDuckGo';
    
    // Соцсети
    if (preg_match('/vk\.com|vkontakte/i', $host)) return 'ВКонтакте';
    if (preg_match('/ok\.ru|odnoklassniki/i', $host)) return 'Одноклассники';
    if (preg_match('/t\.me|telegram/i', $host)) return 'Telegram';
    if (preg_match('/wa\.me|whatsapp/i', $host)) return 'WhatsApp';
    if (preg_match('/facebook|instagram/i', $host)) return 'Facebook/Instagram';
    if (preg_match('/twitter|x\.com/i', $host)) return 'Twitter';
    if (preg_match('/youtube/i', $host)) return 'YouTube';
    
    return $host;
}
