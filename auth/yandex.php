<?php
/**
 * Yandex OAuth Authentication
 */
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

$clientId = getSetting('oauth_yandex_client_id', YANDEX_CLIENT_ID);
$clientSecret = getSetting('oauth_yandex_client_secret', YANDEX_CLIENT_SECRET);

if (empty($clientId) || empty($clientSecret)) {
    oauthNotConfigured('yandex', YANDEX_REDIRECT_URI);
}

$redirect_uri = YANDEX_REDIRECT_URI;
$auth_url = "https://oauth.yandex.ru/authorize?client_id=" . $clientId . 
    "&redirect_uri=" . urlencode($redirect_uri) . 
    "&response_type=code&scope=login:email";

header("Location: " . $auth_url);
exit;
