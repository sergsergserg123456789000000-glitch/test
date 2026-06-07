<?php
/**
 * User Login
 * Вход пользователя
 */
session_start();
require_once 'config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

// Если уже авторизован
if (isLoggedIn()) {
    redirect(url('/account.php'));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = db();
    
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Ошибка безопасности';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        if (empty($email) || empty($password)) {
            $error = 'Введите email и пароль';
        } else {
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $error = 'Неверный email или пароль';
            } elseif (!verifyPassword($password, $user['password_hash'])) {
                $error = 'Неверный email или пароль';
            } else {
                // Вход успешен
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                
                if ($remember) {
                    // Remember me (30 дней)
                    $token = generateToken();
                    setcookie('remember_token', $token, time() + 30*24*3600, '/', '', true, true);
                    $db->prepare("UPDATE users SET remember_token = ? WHERE id = ?")->execute([$token, $user['id']]);
                }
                
                $db->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?")->execute([$user['id']]);
                
                redirect(url('/account.php'), 'С возвращением, ' . e($user['name']) . '!');
            }
        }
    }
}

$page_title = 'Вход';
include TEMPLATES_DIR . '/header.php';
?>

<div class="min-h-screen bg-gradient-to-b from-slate-50 to-white flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <span class="text-xl font-extrabold text-slate-900">PROFESSIONAL SOFTWARE</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
            <h1 class="text-2xl font-extrabold text-slate-900 mb-2">С возвращением</h1>
            <p class="text-sm text-slate-600 mb-6">Войдите в свой аккаунт</p>

            <?php if ($error): ?>
                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-1">Email</label>
                    <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" 
                        class="w-full h-11 px-4 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                        placeholder="you@company.ru">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-semibold text-slate-900">Пароль</label>
                        <a href="<?= url('/forgot-password.php') ?>" class="text-xs text-blue-600 hover:underline">Забыли пароль?</a>
                    </div>
                    <input type="password" name="password" required 
                        class="w-full h-11 px-4 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember" class="rounded">
                    <label for="remember" class="text-sm text-slate-600">Запомнить меня</label>
                </div>

                <button type="submit" class="w-full h-12 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                    Войти
                </button>
            </form>

            <?php
            $socialButtons = array();
            $allSocial = array(
                'vk'     => array('label' => 'VK',     'href' => url('/auth/vk.php'),     'hover' => 'hover:bg-blue-50 hover:border-blue-300'),
                'ok'     => array('label' => 'OK',     'href' => url('/auth/ok.php'),     'hover' => 'hover:bg-orange-50 hover:border-orange-300'),
                'yandex' => array('label' => 'Яндекс', 'href' => url('/auth/yandex.php'), 'hover' => 'hover:bg-red-50 hover:border-red-300'),
                'mail'   => array('label' => 'Mail',   'href' => url('/auth/mail.php'),   'hover' => 'hover:bg-blue-50 hover:border-blue-300'),
            );
            foreach ($allSocial as $sk => $si) {
                if (getSetting('oauth_' . $sk . '_enabled', '0') === '1') {
                    $socialButtons[] = $si;
                }
            }
            if ($socialButtons):
            ?>
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-2 bg-white text-slate-500">Или через соцсети</span>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-<?= count($socialButtons) ?> gap-2">
                    <?php foreach ($socialButtons as $sb): ?>
                    <a href="<?= $sb['href'] ?>" class="h-10 flex items-center justify-center rounded-lg border border-slate-200 <?= $sb['hover'] ?> transition">
                        <span class="text-xs font-semibold text-slate-700"><?= e($sb['label']) ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <p class="mt-6 text-center text-sm text-slate-600">
                Нет аккаунта? <a href="<?= url('/register.php') ?>" class="text-blue-600 font-semibold hover:underline">Зарегистрироваться</a>
            </p>
        </div>
    </div>
</div>

<?php include TEMPLATES_DIR . '/footer.php'; ?>
