<?php
/**
 * Admin Login
 * Вход в админ-панель с 2FA
 */
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

if (isAdminLoggedIn()) {
    redirect(url('/admin/index.php'));
}

$error = '';
$step = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = db();
    
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Ошибка безопасности';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $two_factor_code = trim($_POST['two_factor_code'] ?? '');
        
        if (isset($_POST['action']) && $_POST['action'] === 'login') {
            if (empty($email) || empty($password)) {
                $error = 'Введите email и пароль';
            } else {
                $stmt = $db->prepare("SELECT * FROM admin_users WHERE email = ? AND is_active = 1");
                $stmt->execute([$email]);
                $admin = $stmt->fetch();
                
                if (!$admin) {
                    $error = 'Неверный email или пароль';
                } elseif ($admin['locked_until'] && strtotime($admin['locked_until']) > time()) {
                    $mins = round((strtotime($admin['locked_until']) - time()) / 60);
                    $error = 'Аккаунт заблокирован на ' . $mins . ' мин.';
                } elseif (!verifyPassword($password, $admin['password_hash'])) {
                    $attempts = $admin['failed_login_attempts'] + 1;
                    $locked_until = null;
                    
                    if ($attempts >= MAX_LOGIN_ATTEMPTS) {
                        $locked_until = date('Y-m-d H:i:s', strtotime('+' . LOCKOUT_DURATION . ' minutes'));
                        $error = 'Аккаунт заблокирован на ' . LOCKOUT_DURATION . ' мин.';
                    } else {
                        $error = 'Неверный email или пароль (осталось ' . (MAX_LOGIN_ATTEMPTS - $attempts) . ' попыток)';
                    }
                    
                    $db->prepare("UPDATE admin_users SET failed_login_attempts = ?, locked_until = ? WHERE id = ?")->execute([$attempts, $locked_until, $admin['id']]);
                } else {
                    $db->prepare("UPDATE admin_users SET failed_login_attempts = 0, locked_until = NULL WHERE id = ?")->execute([$admin['id']]);
                    
                    if ($admin['two_factor_enabled'] && $admin['two_factor_secret']) {
                        $_SESSION['pending_admin_id'] = $admin['id'];
                        $_SESSION['pending_admin_email'] = $admin['email'];
                        $step = 2;
                    } else {
                        $_SESSION['admin_id'] = $admin['id'];
                        $_SESSION['admin_email'] = $admin['email'];
                        $_SESSION['admin_name'] = $admin['name'];
                        $_SESSION['admin_role'] = $admin['role'];
                        
                        $db->prepare("UPDATE admin_users SET last_login = CURRENT_TIMESTAMP, last_ip = ? WHERE id = ?")->execute([getClientIP(), $admin['id']]);
                        
                        logAdminAction('admin_login', 'admin_users', $admin['id']);
                        
                        redirect(url('/admin/index.php'), 'Добро пожаловать, ' . e($admin['name']));
                    }
                }
            }
        } elseif (isset($_POST['action']) && $_POST['action'] === 'verify_2fa') {
            if (empty($_SESSION['pending_admin_id']) || empty($two_factor_code)) {
                $error = 'Ошибка сессии или код не введён';
                $step = 1;
            } else {
                $admin_id = $_SESSION['pending_admin_id'];
                
                if (strlen($two_factor_code) === 6 && ctype_digit($two_factor_code)) {
                    $stmt = $db->prepare("SELECT * FROM admin_users WHERE id = ?");
                    $stmt->execute([$admin_id]);
                    $admin = $stmt->fetch();
                    
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['admin_name'] = $admin['name'];
                    $_SESSION['admin_role'] = $admin['role'];
                    
                    unset($_SESSION['pending_admin_id'], $_SESSION['pending_admin_email']);
                    
                    $db->prepare("UPDATE admin_users SET last_login = CURRENT_TIMESTAMP, last_ip = ? WHERE id = ?")->execute([getClientIP(), $admin['id']]);
                    
                    logAdminAction('admin_login_2fa', 'admin_users', $admin['id']);
                    
                    redirect(url('/admin/index.php'), 'Добро пожаловать, ' . e($admin['name']));
                } else {
                    $error = 'Неверный код 2FA';
                }
            }
        }
    }
}

$page_title = 'Вход в админ-панель';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> | <?= SITE_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { width: 100%; max-width: 420px; }
        .logo { text-align: center; margin-bottom: 32px; }
        .logo-icon { width: 64px; height: 64px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; color: white; }
        .logo-text { margin-top: 12px; color: white; font-size: 20px; font-weight: 800; }
        .logo-sub { color: #94a3b8; font-size: 10px; letter-spacing: 3px; text-transform: uppercase; }
        .card { background: white; border-radius: 20px; padding: 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        h1 { font-size: 24px; font-weight: 800; color: #1e293b; margin-bottom: 8px; }
        .subtitle { color: #64748b; font-size: 14px; margin-bottom: 24px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px; }
        input { width: 100%; height: 44px; padding: 0 16px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; transition: all 0.2s; }
        input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.15); }
        .btn { width: 100%; height: 48px; background: #2563eb; color: white; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #1d4ed8; }
        .error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
        .footer { text-align: center; margin-top: 24px; color: #64748b; font-size: 12px; }
        .footer a { color: #94a3b8; text-decoration: none; }
        .footer a:hover { color: white; }
        .two-factor-input { text-align: center; font-size: 28px; letter-spacing: 8px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <div class="logo-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <div class="logo-text">PROFESSIONAL</div>
            <div class="logo-sub">ADMIN PANEL</div>
        </div>

        <div class="card">
            <?php if ($step === 1): ?>
                <h1>Вход в админ-панель</h1>
                <p class="subtitle">Введите учётные данные администратора</p>

                <?php if ($error): ?>
                    <div class="error"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="action" value="login">

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="admin@mastersoftware.ru">
                    </div>

                    <div class="form-group">
                        <label>Пароль</label>
                        <input type="password" name="password" required placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn">Продолжить</button>
                </form>

            <?php else: ?>
                <h1>Двухфакторная аутентификация</h1>
                <p class="subtitle">Введите код из приложения-аутентификатора</p>

                <?php if ($error): ?>
                    <div class="error"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="action" value="verify_2fa">

                    <div class="form-group">
                        <label>Код подтверждения</label>
                        <input type="text" name="two_factor_code" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autofocus class="two-factor-input" placeholder="000000">
                    </div>

                    <button type="submit" class="btn">Войти</button>
                </form>

                <p style="margin-top: 16px; font-size: 12px; color: #64748b; text-align: center;">
                    Откройте Google Authenticator или Яндекс.Ключ
                </p>
            <?php endif; ?>
        </div>

        <div class="footer">
            <a href="<?= url('/') ?>">← Вернуться на сайт</a>
            <p style="margin-top: 8px;">Вход только для администраторов. Все попытки логируются.</p>
        </div>
    </div>
</body>
</html>
