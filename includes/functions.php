<?php
/**
 * Core Functions
 * Общие функции для всего проекта
 */

// Полифиллы для PHP < 8.0
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
    }
}
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}
if (!function_exists('mb_chr')) {
    function mb_chr($cp, $encoding = 'UTF-8') {
        return html_entity_decode('&#' . intval($cp) . ';', ENT_QUOTES, $encoding);
    }
}

/**
 * Безопасный вывод данных
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Генерация случайного кода подтверждения
 */
function generateVerificationCode($length = VERIFICATION_CODE_LENGTH) {
    return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

/**
 * Хэширование пароля
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Проверка пароля
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Генерация безопасного токена
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Валидация email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Валидация телефона (российский формат)
 */
function isValidPhone($phone) {
    $clean = preg_replace('/[^0-9]/', '', $phone);
    return strlen($clean) === 11 && in_array(substr($clean, 0, 1), ['7', '8']);
}

/**
 * Форматирование телефона
 */
function formatPhone($phone) {
    $clean = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($clean) === 11) {
        return sprintf('+%s (%s) %s-%s-%s',
            substr($clean, 0, 1),
            substr($clean, 1, 3),
            substr($clean, 4, 3),
            substr($clean, 7, 2),
            substr($clean, 9, 2)
        );
    }
    return $phone;
}

/**
 * Отправка email
 */
function sendEmail($to, $subject, $body, $altBody = '') {
    require_once INCLUDES_DIR . '/phpmailer/Exception.php';
    require_once INCLUDES_DIR . '/phpmailer/PHPMailer.php';
    require_once INCLUDES_DIR . '/phpmailer/SMTP.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        
        $mail->setFrom(SITE_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);
        
        $result = $mail->send();
        
        // Логирование
        logEmail($to, $subject, $result ? 'sent' : 'failed', $mail->ErrorInfo ?? '');
        
        return $result;
    } catch (Exception $e) {
        error_log("Email send failed: " . $mail->ErrorInfo);
        logEmail($to, $subject, 'failed', $e->getMessage());
        return false;
    }
}

/**
 * Логирование отправки email
 */
function logEmail($recipient, $subject, $status, $error = '') {
    $db = db();
    $stmt = $db->prepare("INSERT INTO email_logs (recipient, subject, status, error_message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$recipient, $subject, $status, $error]);
}

/**
 * Получение IP адреса
 */
function getClientIP() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    }
    
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

/**
 * Получение User Agent
 */
function getUserAgent() {
    return substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 500);
}

/**
 * Проверка CSRF токена
 */
function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Генерация CSRF токена
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateToken();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Редирект с сообщением
 */
function redirect($url, $message = '', $type = 'success') {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: " . $url);
    exit;
}

/**
 * Получение flash сообщения
 */
function getFlashMessage() {
    $message = $_SESSION['flash_message'] ?? '';
    $type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    return ['message' => $message, 'type' => $type];
}

/**
 * Форматирование цены
 */
function formatPrice($price) {
    return number_format($price, 0, '', ' ') . ' ₽';
}

/**
 * Форматирование даты
 */
function formatDate($date, $format = 'd.m.Y H:i') {
    if (!$date) return '';
    $dt = is_string($date) ? new DateTime($date) : $date;
    return $dt->format($format);
}

/**
 * Проверка авторизации пользователя
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Проверка авторизации администратора
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Требовать авторизацию пользователя
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/login.php', 'Необходимо войти в систему', 'error');
    }
}

/**
 * Требовать авторизацию администратора
 */
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        redirect('/admin/login.php', 'Доступ запрещён', 'error');
    }
}

/**
 * Логирование действий администратора
 */
function logAdminAction($action, $table = null, $recordId = null, $oldValues = null, $newValues = null) {
    if (!isAdminLoggedIn()) return;
    
    $db = db();
    $stmt = $db->prepare("INSERT INTO admin_activity_log (admin_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['admin_id'],
        $action,
        $table,
        $recordId,
        $oldValues ? json_encode($oldValues) : null,
        $newValues ? json_encode($newValues) : null,
        getClientIP(),
        getUserAgent()
    ]);
}

/**
 * Безопасная загрузка файла
 */
function uploadFile($file, $directory = 'products') {
    $allowed = ALLOWED_EXTENSIONS;
    $maxSize = MAX_FILE_SIZE;
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Ошибка загрузки файла'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'Файл слишком большой'];
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        return ['success' => false, 'error' => 'Недопустимый тип файла'];
    }
    
    $uploadDir = UPLOAD_DIR . DS . $directory;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    $filepath = $uploadDir . DS . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'error' => 'Не удалось сохранить файл'];
    }
    
    // Вычисление хэша
    $hash = hash_file('sha256', $filepath);
    
    return [
        'success' => true,
        'filename' => $filename,
        'filepath' => $directory . '/' . $filename,
        'size' => $file['size'],
        'hash' => $hash
    ];
}

/**
 * Формирование URL с учётом базового пути
 * Делает все ссылки относительными — работает на любом домене
 */
function url($path) {
    return SITE_BASE . ($path === '' || $path === '/' ? '/' : $path);
}

/**
 * Показать страницу "OAuth не настроен" с инструкцией
 */
function oauthNotConfigured($provider, $callbackUrl) {
    $providerNames = [
        'vk' => 'ВКонтакте',
        'ok' => 'Одноклассники',
        'yandex' => 'Яндекс',
        'mail' => 'Mail.ru'
    ];
    $name = $providerNames[$provider] ?? $provider;
    
    $devLinks = [
        'vk' => 'https://dev.vk.com/',
        'ok' => 'https://apiok.ru/',
        'yandex' => 'https://oauth.yandex.ru/client/new',
        'mail' => 'https://api.mail.ru/apps/my/add'
    ];
    $devLink = $devLinks[$provider] ?? '#';
    
    http_response_code(200);
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Вход через <?= e($name) ?> не настроен</title>
        <style>
            * { margin:0; padding:0; box-sizing:border-box; }
            body { font-family: -apple-system, system-ui, sans-serif; background:#f1f5f9; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
            .box { background:#fff; border-radius:16px; padding:32px; max-width:560px; box-shadow:0 10px 40px rgba(0,0,0,0.1); }
            h1 { font-size:22px; color:#0f172a; margin-bottom:8px; }
            p { color:#475569; font-size:14px; line-height:1.6; margin-bottom:12px; }
            .steps { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px; margin:16px 0; }
            .steps li { margin-left:18px; margin-bottom:8px; font-size:13px; color:#334155; }
            code { background:#eef2ff; padding:2px 8px; border-radius:6px; font-size:12px; color:#1e40af; word-break:break-all; }
            .btn { display:inline-block; background:#2563eb; color:#fff; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; margin-top:8px; }
            .btn-sec { background:#e2e8f0; color:#334155; margin-left:8px; }
            .alert { background:#fef3c7; border:1px solid #fde68a; color:#854d0e; padding:12px; border-radius:8px; font-size:13px; margin-bottom:16px; }
        </style>
    </head>
    <body>
        <div class="box">
            <div class="alert">⚠️ Вход через <strong><?= e($name) ?></strong> пока не настроен администратором</div>
            <h1>Нужно подключить <?= e($name) ?></h1>
            <p>Чтобы заработал вход и регистрация через <?= e($name) ?>, администратору нужно получить ключи приложения и указать их в админ-панели.</p>
            <div class="steps">
                <ol>
                    <li>Создайте приложение в <a href="<?= e($devLink) ?>" target="_blank">кабинете разработчика <?= e($name) ?></a></li>
                    <li>Укажите Redirect URI: <code><?= e($callbackUrl) ?></code></li>
                    <li>Скопируйте Client ID и Client Secret</li>
                    <li>Вставьте их в Админка → Настройки сайта</li>
                </ol>
            </div>
            <a href="<?= e(url('/login.php')) ?>" class="btn">← Войти по email</a>
            <a href="<?= e(url('/')) ?>" class="btn btn-sec">На главную</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

/**
 * Получить настройку сайта из БД
 */
function getSetting($key, $default = '') {
    try {
        $stmt = db()->prepare('SELECT setting_value FROM site_settings WHERE setting_key = ?');
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        return $value !== false && $value !== null && $value !== '' ? $value : $default;
    } catch (Throwable $e) {
        return $default;
    }
}

/**
 * Sanitize slug
 */
function sanitizeSlug($string) {
    $ru = array(
        'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
        'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
        'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
        'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
    );
    $en = array(
        'a','b','v','g','d','e','yo','zh','z','i','y','k','l','m','n','o','p',
        'r','s','t','u','f','kh','ts','ch','sh','shch','','y','','e','yu','ya',
        'A','B','V','G','D','E','Yo','Zh','Z','I','Y','K','L','M','N','O','P',
        'R','S','T','U','F','Kh','Ts','Ch','Sh','Shch','','Y','','E','Yu','Ya'
    );
    $string = str_replace($ru, $en, $string);
    $string = mb_strtolower($string, 'UTF-8');
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}
