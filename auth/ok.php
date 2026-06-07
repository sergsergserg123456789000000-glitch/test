<?php
/**
 * Odnoklassniki OAuth Authentication
 */
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

$clientId = getSetting('oauth_ok_client_id', OK_CLIENT_ID);
$clientSecret = getSetting('oauth_ok_client_secret', OK_CLIENT_SECRET);

if (empty($clientId) || empty($clientSecret)) {
    oauthNotConfigured('ok', OK_REDIRECT_URI);
}

$redirect_uri = OK_REDIRECT_URI;
$auth_url = "https://connect.ok.ru/oauth/authorize?client_id=" . $clientId . 
    "&redirect_uri=" . urlencode($redirect_uri) . 
    "&response_type=code&scope=EMAIL";

header("Location: " . $auth_url);
exit;
