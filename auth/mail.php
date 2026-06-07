<?php
/**
 * Mail.ru OAuth Authentication
 */
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

$clientId = getSetting('oauth_mail_client_id', MAILRU_CLIENT_ID);
$clientSecret = getSetting('oauth_mail_client_secret', MAILRU_CLIENT_SECRET);

if (empty($clientId) || empty($clientSecret)) {
    oauthNotConfigured('mail', MAILRU_REDIRECT_URI);
}

$authUrl = 'https://oauth.mail.ru/login?' . http_build_query([
    'client_id' => $clientId,
    'response_type' => 'code',
    'redirect_uri' => MAILRU_REDIRECT_URI,
    'scope' => 'userinfo'
]);

header('Location: ' . $authUrl);
exit;
