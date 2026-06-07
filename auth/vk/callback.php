<?php
/**
 * VKontakte OAuth Callback
 */
session_start();
require_once '../../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

if (empty($_GET['code'])) {
    redirect(url('/register.php'), 'Ошибка авторизации', 'error');
}

$code = $_GET['code'];
$redirect_uri = VK_REDIRECT_URI;
$clientId = getSetting('oauth_vk_client_id', VK_CLIENT_ID);
$clientSecret = getSetting('oauth_vk_client_secret', VK_CLIENT_SECRET);

$token_url = "https://oauth.vk.com/access_token?client_id=" . $clientId . 
    "&client_secret=" . $clientSecret . 
    "&redirect_uri=" . urlencode($redirect_uri) . 
    "&code=" . $code;

$response = file_get_contents($token_url);
$data = json_decode($response, true);

if (!isset($data['access_token']) || !isset($data['user_id'])) {
    redirect(url('/register.php'), 'Ошибка получения токена', 'error');
}

$access_token = $data['access_token'];
$user_id = $data['user_id'];
$email = $data['email'] ?? null;

if ($email) {
    $user_info_url = "https://api.vk.com/method/users.get?user_ids=" . $user_id . 
        "&fields=first_name,last_name&access_token=" . $access_token . 
        "&v=5.131";
    
    $user_response = file_get_contents($user_info_url);
    $user_data = json_decode($user_response, true);
    
    if (isset($user_data['response'][0])) {
        $vk_user = $user_data['response'][0];
        $name = trim(($vk_user['first_name'] ?? '') . ' ' . ($vk_user['last_name'] ?? ''));
    }
}

$db = db();

$stmt = $db->prepare("SELECT * FROM users WHERE social_provider = 'vk' AND social_id = ?");
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
    if (!$email) {
        redirect(url('/register.php'), 'VK не предоставил email. Зарегистрируйтесь через форму.', 'error');
    }
    
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        redirect(url('/login.php'), 'Пользователь с таким email уже существует. Войдите с паролем.', 'error');
    }
    
    $stmt = $db->prepare("INSERT INTO users (email, name, social_provider, social_id, email_verified, created_at) VALUES (?, ?, 'vk', ?, 1, CURRENT_TIMESTAMP)");
    $stmt->execute([$email, $name ?: 'Пользователь VK', $user_id]);
    
    $user_id_new = $db->lastInsertId();
    
    $_SESSION['user_id'] = $user_id_new;
    $_SESSION['user_email'] = $email;
    
    redirect(url('/account.php'), 'Регистрация успешна! Добро пожаловать.');
}
