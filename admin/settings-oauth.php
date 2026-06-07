<?php
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
require_once APP_ROOT . '/admin/settings-helper.php';
requireAdmin();

$db = db();
$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Администратор';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'admin';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
        $error = 'Ошибка безопасности';
    } else {
        $providers = array('vk', 'ok', 'yandex', 'mail');
        // Сначала сбрасываем все enabled
        foreach ($providers as $prov) {
            saveSetting2($db, 'oauth_' . $prov . '_enabled', '0');
        }
        // Сохраняем из POST
        foreach ($_POST as $k => $v) {
            if (strpos($k, 'oauth_') === 0) {
                saveSetting2($db, $k, trim((string)$v));
            }
        }
        $message = 'Настройки OAuth сохранены';
    }
}

$s = getSettingsMap($db, 'oauth_');
$page_title = 'Вход через соцсети';
include APP_ROOT . '/admin/templates/header.php';

$oauthProviders = array(
    'vk'     => array('name' => 'ВКонтакте',    'dev' => 'https://dev.vk.com/',                'callback' => VK_REDIRECT_URI),
    'ok'     => array('name' => 'Одноклассники', 'dev' => 'https://apiok.ru/',                  'callback' => OK_REDIRECT_URI),
    'yandex' => array('name' => 'Яндекс',       'dev' => 'https://oauth.yandex.ru/client/new',  'callback' => YANDEX_REDIRECT_URI),
    'mail'   => array('name' => 'Mail.ru',      'dev' => 'https://api.mail.ru/apps/my/add',     'callback' => MAILRU_REDIRECT_URI),
);
?>

<div style="margin-bottom:24px;">
    <h1 style="font-size:26px; font-weight:800; color:#0f172a;">Вход через соцсети (OAuth)</h1>
    <div style="color:#64748b; font-size:14px; margin-top:4px;">Управление кнопками входа и ключами приложений</div>
</div>

<?php if ($message): ?><div class="flash-message flash-success"><?= e($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="flash-message flash-error"><?= e($error) ?></div><?php endif; ?>

<div style="background:#fef3c7; border:1px solid #fde68a; border-radius:10px; padding:14px 18px; margin-bottom:20px; font-size:13px; color:#854d0e;">
    <strong>Важно:</strong> чтобы OAuth работал, нужно создать приложение в кабинете разработчика каждой соцсети и указать там Redirect URI (показан ниже). Без этого авторизация невозможна.
</div>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

    <?php foreach ($oauthProviders as $prov => $info):
        $idKey      = 'oauth_' . $prov . '_client_id';
        $secretKey  = 'oauth_' . $prov . '_client_secret';
        $enabledKey = 'oauth_' . $prov . '_enabled';
        $idVal      = isset($s[$idKey])      ? $s[$idKey]      : '';
        $secretVal  = isset($s[$secretKey])  ? $s[$secretKey]  : '';
        $isEnabled  = isset($s[$enabledKey]) && $s[$enabledKey] === '1';
        $hasCreds   = $idVal !== '' && $secretVal !== '';
    ?>
    <div style="border:2px solid <?= $isEnabled ? '#86efac' : '#e2e8f0' ?>; border-radius:14px; padding:20px; margin-bottom:14px; background:<?= $isEnabled ? '#f0fdf4' : '#fff' ?>; transition:all .2s;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px;">
            <label style="display:flex; align-items:center; gap:10px; cursor:pointer; margin:0; flex:1;">
                <input type="checkbox" name="oauth_<?= e($prov) ?>_enabled" value="1" <?= $isEnabled ? 'checked' : '' ?> style="width:20px; height:20px; accent-color:#16a34a; cursor:pointer;" onchange="this.closest('.oauth-row').style.border='2px solid '+(this.checked?'#86efac':'#e2e8f0');this.closest('.oauth-row').style.background=this.checked?'#f0fdf4':'#fff'">
                <div>
                    <div style="font-size:16px; font-weight:800; color:#0f172a;"><?= e($info['name']) ?></div>
                    <div style="font-size:12px; color:#64748b; margin-top:2px;"><?= $isEnabled ? '✓ Показывается на форме входа' : '✗ Скрыта на форме входа' ?></div>
                </div>
            </label>
            <div style="display:flex; gap:6px; flex-shrink:0;">
                <?php if ($isEnabled): ?>
                    <span class="badge badge-success">Включена</span>
                <?php else: ?>
                    <span class="badge" style="background:#fee2e2; color:#991b1b; border-radius:9999px; padding:2px 8px; font-size:11px; font-weight:600;">Выключена</span>
                <?php endif; ?>
                <?php if ($hasCreds): ?>
                    <span class="badge badge-success">Ключи ✓</span>
                <?php else: ?>
                    <span class="badge badge-warning">Ключи нужны</span>
                <?php endif; ?>
            </div>
        </div>

        <div style="background:<?= $isEnabled ? '#dcfce7' : '#f1f5f9' ?>; border-radius:8px; padding:10px 14px; margin-bottom:14px; font-size:12px; color:#334155;">
            <div><strong>Redirect URI:</strong> <code style="background:#fff; padding:2px 8px; border-radius:4px; font-size:11px;"><?= e($info['callback']) ?></code></div>
            <div style="margin-top:4px;"><strong>Кабинет:</strong> <a href="<?= e($info['dev']) ?>" target="_blank" style="color:#2563eb;"><?= e($info['dev']) ?></a></div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="form-group" style="margin:0;">
                <label>Client ID</label>
                <input type="text" name="<?= e($idKey) ?>" value="<?= e($idVal) ?>" class="form-control" placeholder="Вставьте Client ID">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Client Secret</label>
                <input type="password" name="<?= e($secretKey) ?>" value="<?= e($secretVal) ?>" class="form-control" placeholder="Вставьте Client Secret"
                    style="font-family:monospace;"
                    onclick="this.type='text';"
                    onblur="if(this.value)this.type='password';">
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary">Сохранить настройки OAuth</button>
</form>

<script>
// Обновляем стиль карточки при клике на чекбокс
document.querySelectorAll('input[type=checkbox]').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var card = this.closest('div[style*="border:2px"]');
        if (!card) return;
        if (this.checked) {
            card.style.border = '2px solid #86efac';
            card.style.background = '#f0fdf4';
        } else {
            card.style.border = '2px solid #e2e8f0';
            card.style.background = '#fff';
        }
    });
});
</script>

<?php include APP_ROOT . '/admin/templates/footer.php'; ?>
