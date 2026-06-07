<?php
/**
 * Odnoklassniki OAuth Callback
 */
session_start();
require_once '../../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

if (empty($_GET['code'])) {
    redirect(url('/register.php'), 'Ошибка авторизации', 'error');
}

$code = $_GET['code'];
$redirect_uri = OK_REDIRECT_URI;
$clientId = getSetting('oauth_ok_client_id', OK_CLIENT_ID);
$clientSecret = getSetting('oauth_ok_client_secret', OK_CLIENT_SECRET);

$token_url = "https://api.ok.ru/oauth/token";
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

if (!isset($data['access_token']) || !isset($data['uid'])) {
    redirect(url('/register.php'), 'Ошибка получения токена', 'error');
}

$access_token = $data['access_token'];
$user_id = $data['uid'];

$db = db();

$stmt = $db->prepare("SELECT * FROM users WHERE social_provider = 'ok' AND social_id = ?");
$stmt->execute([$user_id]);
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
    $temp_email = 'ok_' . $user_id . '@temp.ok.ru';
    
    $stmt = $db->prepare("INSERT INTO users (email, name, social_provider, social_id, email_verified, created_at) VALUES (?, ?, 'ok', ?, 0, CURRENT_TIMESTAMP)");
    $stmt->execute([$temp_email, 'Пользователь OK', $user_id]);
    
    $user_id_new = $db->lastInsertId();
    
    $_SESSION['user_id'] = $user_id_new;
    $_SESSION['user_email'] = $temp_email;
    
    redirect(url('/account.php?verify_email=1'), 'Требуется подтверждение email');
}
