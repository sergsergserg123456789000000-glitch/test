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
        $keys = array('max_login_attempts', 'lockout_duration', 'verification_code_expiry');
        foreach ($keys as $k) {
            if (isset($_POST[$k]) && is_numeric($_POST[$k])) {
                saveSetting2($db, $k, (int)$_POST[$k]);
            }
        }
        $message = 'Настройки безопасности сохранены';
    }
}

$s = getSettingsMap($db);
$page_title = 'Безопасность';
include APP_ROOT . '/admin/templates/header.php';
?>

<div style="margin-bottom:24px;">
    <h1 style="font-size:26px; font-weight:800; color:#0f172a;">Безопасность</h1>
    <div style="color:#64748b; font-size:14px; margin-top:4px;">Параметры блокировки входа и подтверждения email</div>
</div>

<?php if ($message): ?><div class="flash-message flash-success"><?= e($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="flash-message flash-error"><?= e($error) ?></div><?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

    <div class="card" style="padding:24px; max-width:640px;">
        <h3 style="font-size:16px; font-weight:700; margin-bottom:20px; color:#0f172a;">Параметры входа</h3>

        <div class="form-group">
            <label>Максимум попыток входа (до блокировки)</label>
            <input type="number" name="max_login_attempts" min="1" max="20" value="<?= e(isset($s['max_login_attempts']) ? $s['max_login_attempts'] : '5') ?>" class="form-control" style="max-width:200px;">
            <div style="font-size:12px; color:#64748b; margin-top:6px;">После этого количества неверных попыток аккаунт временно блокируется</div>
        </div>

        <div class="form-group">
            <label>Длительность блокировки (минуты)</label>
            <input type="number" name="lockout_duration" min="1" max="1440" value="<?= e(isset($s['lockout_duration']) ? $s['lockout_duration'] : '30') ?>" class="form-control" style="max-width:200px;">
            <div style="font-size:12px; color:#64748b; margin-top:6px;">На сколько минут блокировать аккаунт после превышения лимита попыток</div>
        </div>

        <div class="form-group" style="margin-bottom:0;">
            <label>Время жизни кода подтверждения email (минуты)</label>
            <input type="number" name="verification_code_expiry" min="5" max="120" value="<?= e(isset($s['verification_code_expiry']) ? $s['verification_code_expiry'] : '30') ?>" class="form-control" style="max-width:200px;">
            <div style="font-size:12px; color:#64748b; margin-top:6px;">Через сколько минут истекает 6-значный код, отправленный при регистрации</div>
        </div>
    </div>

    <div style="margin-top:16px;">
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </div>
</form>

<?php include APP_ROOT . '/admin/templates/footer.php'; ?>
