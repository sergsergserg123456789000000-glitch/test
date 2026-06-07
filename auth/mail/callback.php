<?php
/**
 * Mail.ru OAuth Callback
 */
session_start();
require_once '../../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

if (empty($_GET['code'])) {
    redirect(url('/login.php'), 'Ошибка авторизации Mail.ru', 'error');
}

$clientId = getSetting('oauth_mail_client_id', MAILRU_CLIENT_ID);
$clientSecret = getSetting('oauth_mail_client_secret', MAILRU_CLIENT_SECRET);

$ch = curl_init('https://oauth.mail.ru/token');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'grant_type' => 'authorization_code',
        'code' => $_GET['code'],
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri' => MAILRU_REDIRECT_URI
    ])
]);
$response = curl_exec($ch);
curl_close($ch);
$token = json_decode($response, true);

if (empty($token['access_token'])) {
    redirect(url('/login.php'), 'Не удалось получить токен Mail.ru', 'error');
}

$ch = curl_init('https://oauth.mail.ru/userinfo');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token['access_token']]
]);
$userResponse = curl_exec($ch);
curl_close($ch);
$data = json_decode($userResponse, true);

$email = $data['email'] ?? null;
$mailId = $data['id'] ?? ($data['sub'] ?? null);
$name = trim(($data['name'] ?? '') ?: (($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')));

if (!$email || !$mailId) {
    redirect(url('/login.php'), 'Mail.ru не передал email пользователя', 'error');
}

$db = db();
$stmt = $db->prepare("SELECT * FROM users WHERE social_provider = 'mail' AND social_id = ?");
$stmt->execute([$mailId]);
$user = $stmt->fetch();

if (!$user) {
    $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
}

if ($user) {
    if (!$user['is_active']) {
        redirect(url('/login.php'), 'Аккаунт заблокирован', 'error');
    }
    $db->prepare("UPDATE users SET social_provider='mail', social_id=?, email_verified=1, last_login=CURRENT_TIMESTAMP WHERE id=?")
       ->execute([$mailId, $user['id']]);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    redirect(url('/account.php'), 'С возвращением!');
}

$stmt = $db->prepare("INSERT INTO users (email, name, social_provider, social_id, email_verified, created_at, last_login) VALUES (?, ?, 'mail', ?, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
$stmt->execute([$email, $name ?: 'Пользователь Mail.ru', $mailId]);
$userId = (int)$db->lastInsertId();
$_SESSION['user_id'] = $userId;
$_SESSION['user_email'] = $email;
redirect(url('/account.php'), 'Регистрация через Mail.ru выполнена');
