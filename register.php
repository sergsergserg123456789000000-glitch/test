<?php
/**
 * User Registration
 * Регистрация пользователя с double opt-in (6-значный код)
 */
session_start();
require_once 'config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

$error = '';
$success = '';
$step = 1; // 1 = форма, 2 = код подтверждения

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = db();
    
    // Проверка CSRF
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Ошибка безопасности. Обновите страницу.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $verification_code = trim($_POST['verification_code'] ?? '');
        
        if (isset($_POST['action']) && $_POST['action'] === 'register') {
            // Шаг 1: Регистрация и отправка кода
            if (empty($email) || empty($name) || empty($password)) {
                $error = 'Заполните все обязательные поля';
            } elseif (!isValidEmail($email)) {
                $error = 'Некорректный email';
            } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
                $error = 'Пароль должен быть не менее ' . PASSWORD_MIN_LENGTH . ' символов';
            } elseif ($password !== $password_confirm) {
                $error = 'Пароли не совпадают';
            } else {
                // Проверка существующего пользователя
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'Пользователь с таким email уже зарегистрирован';
                } else {
                    // Генерация кода подтверждения
                    $code = generateVerificationCode();
                    $expires = date('Y-m-d H:i:s', strtotime('+' . VERIFICATION_CODE_EXPIRY . ' minutes'));
                    
                    // Создание пользователя (неактивен до подтверждения)
                    $stmt = $db->prepare("INSERT INTO users (email, password_hash, name, phone, company, verification_code, verification_code_expires) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $email,
                        hashPassword($password),
                        $name,
                        $phone ?: null,
                        $company ?: null,
                        $code,
                        $expires
                    ]);
                    
                    $user_id = $db->lastInsertId();
                    
                    // Отправка email с кодом
                    $subject = 'Подтверждение регистрации - ' . SITE_NAME;
                    $body = file_get_contents(TEMPLATES_DIR . '/email/verification.html');
                    $body = str_replace(
                        ['{name}', '{code}', '{site_name}', '{site_url}'],
                        [$name, $code, SITE_NAME, SITE_URL],
                        $body
                    );
                    
                    if (sendEmail($email, $subject, $body)) {
                        $_SESSION['pending_user_id'] = $user_id;
                        $_SESSION['pending_email'] = $email;
                        $step = 2;
                        $success = 'Код подтверждения отправлен на ' . e($email);
                    } else {
                        $error = 'Ошибка отправки email. Попробуйте позже.';
                        // Удаляем пользователя
                        $db->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);
                    }
                }
            }
        } elseif (isset($_POST['action']) && $_POST['action'] === 'verify') {
            // Шаг 2: Проверка кода
            if (empty($verification_code)) {
                $error = 'Введите код подтверждения';
            } elseif (empty($_SESSION['pending_user_id'])) {
                $error = 'Сессия истекла. Зарегистрируйтесь заново.';
                $step = 1;
            } else {
                $user_id = $_SESSION['pending_user_id'];
                $email = $_SESSION['pending_email'];
                
                // Проверка кода
                $stmt = $db->prepare("SELECT id, verification_code, verification_code_expires FROM users WHERE id = ? AND email = ?");
                $stmt->execute([$user_id, $email]);
                $user = $stmt->fetch();
                
                if (!$user) {
                    $error = 'Пользователь не найден';
                    $step = 1;
                } elseif ($user['verification_code'] !== $verification_code) {
                    $error = 'Неверный код подтверждения';
                } elseif (strtotime($user['verification_code_expires']) < time()) {
                    $error = 'Срок действия кода истёк';
                    $step = 1;
                } else {
                    // Активация пользователя
                    $stmt = $db->prepare("UPDATE users SET email_verified = 1, verification_code = NULL, verification_code_expires = NULL, created_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $stmt->execute([$user_id]);
                    
                    // Автоматический вход
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_email'] = $email;
                    
                    // Очистка сессии
                    unset($_SESSION['pending_user_id'], $_SESSION['pending_email']);
                    
                    // Логирование входа
                    $db->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?")->execute([$user_id]);
                    
                    redirect(url('/account.php'), 'Регистрация успешна! Добро пожаловать.');
                }
            }
        }
    }
}

// Если уже авторизован
if (isLoggedIn()) {
    redirect(url('/account.php'));
}

$page_title = 'Регистрация';
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
            <?php if ($step === 1): ?>
                <h1 class="text-2xl font-extrabold text-slate-900 mb-2">Создать аккаунт</h1>
                <p class="text-sm text-slate-600 mb-6">Заполните форму для регистрации. После этого вы получите код подтверждения на email.</p>

                <?php if ($error): ?>
                    <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="action" value="register">

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-1">Имя *</label>
                        <input type="text" name="name" required value="<?= e($_POST['name'] ?? '') ?>" 
                            class="w-full h-11 px-4 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                            placeholder="Иван Петров">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-1">Email *</label>
                        <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" 
                            class="w-full h-11 px-4 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                            placeholder="you@company.ru">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-1">Телефон</label>
                        <input type="tel" name="phone" value="<?= e($_POST['phone'] ?? '') ?>" 
                            class="w-full h-11 px-4 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                            placeholder="+7 (___) ___-__-__">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-1">Компания</label>
                        <input type="text" name="company" value="<?= e($_POST['company'] ?? '') ?>" 
                            class="w-full h-11 px-4 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                            placeholder='ООО "Пример"'>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-1">Пароль *</label>
                        <input type="password" name="password" required minlength="<?= PASSWORD_MIN_LENGTH ?>" 
                            class="w-full h-11 px-4 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                            placeholder="Минимум <?= PASSWORD_MIN_LENGTH ?> символов">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-1">Подтверждение пароля *</label>
                        <input type="password" name="password_confirm" required 
                            class="w-full h-11 px-4 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                            placeholder="Повторите пароль">
                    </div>

                    <div class="flex items-start gap-2">
                        <input type="checkbox" id="terms" required class="mt-1 rounded">
                        <label for="terms" class="text-xs text-slate-600">
                            Я согласен на <a href="/privacy.php" class="text-blue-600 hover:underline">обработку персональных данных</a> и согласен с <a href="/terms.php" class="text-blue-600 hover:underline">условиями использования</a>
                        </label>
                    </div>

                    <button type="submit" class="w-full h-12 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                        Зарегистрироваться
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
                    Уже есть аккаунт? <a href="<?= url('/login.php') ?>" class="text-blue-600 font-semibold hover:underline">Войти</a>
                </p>

            <?php elseif ($step === 2): ?>
                <h1 class="text-2xl font-extrabold text-slate-900 mb-2">Подтверждение email</h1>
                <p class="text-sm text-slate-600 mb-6">
                    Введите 6-значный код, отправленный на <strong><?= e($_SESSION['pending_email'] ?? '') ?></strong>
                </p>

                <?php if ($error): ?>
                    <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm"><?= e($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm"><?= e($success) ?></div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="action" value="verify">

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-1">Код подтверждения</label>
                        <input type="text" name="verification_code" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autofocus
                            class="w-full h-14 px-4 text-center text-2xl font-mono tracking-widest rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                            placeholder="000000">
                    </div>

                    <button type="submit" class="w-full h-12 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                        Подтвердить
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="/resend-code.php" class="text-sm text-blue-600 hover:underline">Отправить код повторно</a>
                </div>

                <p class="mt-6 text-center text-sm text-slate-600">
                    Неверный email? <button onclick="location.reload()" class="text-blue-600 font-semibold hover:underline">Изменить</button>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include TEMPLATES_DIR . '/footer.php'; ?>
