<?php
/**
 * Общий хелпер для всех страниц настроек.
 * Подключается вместо admin/settings.php там, где нужна общая логика.
 */
if (!function_exists('saveSetting2')) {
    function saveSetting2($db, $key, $value) {
        $stmt = $db->prepare('SELECT COUNT(*) FROM site_settings WHERE setting_key = ?');
        $stmt->execute(array($key));
        if ((int)$stmt->fetchColumn() > 0) {
            $db->prepare('UPDATE site_settings SET setting_value=?, updated_at=CURRENT_TIMESTAMP WHERE setting_key=?')->execute(array($value, $key));
        } else {
            $db->prepare('INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)')->execute(array($key, $value, 'text', $key));
        }
    }
}

function getSettingsMap($db, $prefix = '') {
    if ($prefix) {
        $rows = $db->query("SELECT * FROM site_settings WHERE setting_key LIKE '" . $prefix . "%' ORDER BY setting_key")->fetchAll();
    } else {
        $rows = $db->query('SELECT * FROM site_settings ORDER BY setting_key')->fetchAll();
    }
    $map = array();
    foreach ($rows as $r) {
        $map[$r['setting_key']] = $r['setting_value'];
    }
    return $map;
}

function renderSaveButton($label = 'Сохранить') {
    echo '<div style="margin-top:20px;"><button type="submit" class="btn btn-primary">' . $label . '</button></div>';
}
