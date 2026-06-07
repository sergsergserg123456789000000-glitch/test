<?php
/**
 * Yandex OAuth Callback
 */
session_start();
require_once '../../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

if (empty($_GET['code'])) {
    redirect(url('/register.php'), 'Ошибка авторизации', 'error');
}

$code = $_GET['code'];
$redirect_uri = YANDEX_REDIRECT_URI;
$clientId = getSetting('oauth_yandex_client_id', YANDEX_CLIENT_ID);
$clientSecret = getSetting('oauth_yandex_client_secret', YANDEX_CLIENT_SECRET);

$token_url = "https://oauth.yandex.ru/token";
$params = [
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'redirect_uri' => $redirect_uri,
    'code' => $code,
    'grant_type' => 'authorization_code'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (!isset($data['access_token'])) {
    redirect(url('/register.php'), 'Ошибка получения токена', 'error');
}

$access_token = $data['access_token'];

$user_info_url = "https://login.yandex.ru/info";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $user_info_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: OAuth ' . $access_token]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$user_response = curl_exec($ch);
curl_close($ch);

$user_data = json_decode($user_response, true);

if (!isset($user_data['default_email'])) {
    redirect(url('/register.php'), 'Yandex не предоставил email', 'error');
}

$email = $user_data['default_email'];
$name = trim(($user_data['first_name'] ?? '') . ' ' . ($user_data['last_name'] ?? ''));
$yandex_user_id = $user_data['id'];

$db = db();

$stmt = $db->prepare("SELECT * FROM users WHERE social_provider = 'yandex' AND social_id = ?");
$stmt->execute([$yandex_user_id]);
$user = $stmt->fetch();

if ($user) {
    if (!$user['is_active']) {
        redirect(url('/register.php'), 'Аккаунт заблокирован', 'error');
    }
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    
    $db->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?")->execute([$user['id']]);
    
    redirect(url('/account.php'), 'С возвращением!');
} else {
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        redirect(url('/login.php'), 'Пользователь с таким email уже существует', 'error');
    }
    
    $stmt = $db->prepare("INSERT INTO users (email, name, social_provider, social_id, email_verified, created_at) VALUES (?, ?, 'yandex', ?, 1, CURRENT_TIMESTAMP)");
    $stmt->execute([$email, $name ?: 'Пользователь Яндекс', $yandex_user_id]);
    
    $user_id_new = $db->lastInsertId();
    
    $_SESSION['user_id'] = $user_id_new;
    $_SESSION['user_email'] = $email;
    
    redirect(url('/account.php'), 'Регистрация успешна! Добро пожаловать.');
}
