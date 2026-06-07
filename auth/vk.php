<?php
/**
 * VKontakte OAuth Authentication
 */
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

$clientId = getSetting('oauth_vk_client_id', VK_CLIENT_ID);
$clientSecret = getSetting('oauth_vk_client_secret', VK_CLIENT_SECRET);

if (empty($clientId) || empty($clientSecret)) {
    oauthNotConfigured('vk', VK_REDIRECT_URI);
}

$redirect_uri = VK_REDIRECT_URI;
$auth_url = "https://oauth.vk.com/authorize?client_id=" . $clientId . 
    "&redirect_uri=" . urlencode($redirect_uri) . 
    "&display=popup&response_type=code&scope=email";

header("Location: " . $auth_url);
exit;
