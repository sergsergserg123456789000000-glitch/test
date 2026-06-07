<?php
/**
 * Возвращает JSON со списком включённых OAuth-провайдеров.
 * Галочка включена = показываем кнопку (даже если ключи не заполнены).
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$providers = array();
$allProviders = array(
    'vk'     => array('label' => 'VK',     'href' => 'auth/vk.php'),
    'ok'     => array('label' => 'OK',     'href' => 'auth/ok.php'),
    'yandex' => array('label' => 'Яндекс', 'href' => 'auth/yandex.php'),
    'mail'   => array('label' => 'Mail',   'href' => 'auth/mail.php'),
);

foreach ($allProviders as $key => $info) {
    $enabled = getSetting('oauth_' . $key . '_enabled', '0');

    // Галочка стоит = показываем кнопку
    if ($enabled === '1') {
        $providers[] = array(
            'key'   => $key,
            'label' => $info['label'],
            'href'  => $info['href'],
        );
    }
}

echo json_encode(array('providers' => $providers), JSON_UNESCAPED_UNICODE);
