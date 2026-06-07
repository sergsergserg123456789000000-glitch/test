<?php
// Основные настройки: логотип, фавикон, название, email, телефон
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
        try {
            $textKeys = array('site_name', 'site_email', 'site_phone');
            foreach ($textKeys as $k) {
                if (isset($_POST[$k])) {
                    saveSetting2($db, $k, trim($_POST[$k]));
                }
            }

            // Загрузка логотипа
            if (!empty($_FILES['logo_file']['name']) && $_FILES['logo_file']['error'] == UPLOAD_ERR_OK) {
                $allowed = array('ico','png','jpg','jpeg','webp','svg');
                $ext = strtolower(pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed)) throw new Exception('Недопустимый формат файла');
                if (!is_dir(IMAGES_DIR)) mkdir(IMAGES_DIR, 0755, true);
                $filename = 'site-logo.' . $ext;
                if (move_uploaded_file($_FILES['logo_file']['tmp_name'], IMAGES_DIR . DS . $filename)) {
                    saveSetting2($db, 'site_logo_path', 'images/' . $filename);
                }
            }
            // Загрузка фавикона
            if (!empty($_FILES['favicon_file']['name']) && $_FILES['favicon_file']['error'] == UPLOAD_ERR_OK) {
                $allowed = array('ico','png','jpg','jpeg','webp','svg');
                $ext = strtolower(pathinfo($_FILES['favicon_file']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed)) throw new Exception('Недопустимый формат файла');
                if (!is_dir(IMAGES_DIR)) mkdir(IMAGES_DIR, 0755, true);
                $filename = 'site-favicon.' . $ext;
                if (move_uploaded_file($_FILES['favicon_file']['tmp_name'], IMAGES_DIR . DS . $filename)) {
                    saveSetting2($db, 'site_favicon_path', 'images/' . $filename);
                }
            }
            $message = 'Настройки сохранены';
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

$s = getSettingsMap($db);
$page_title = 'Основные настройки';
include APP_ROOT . '/admin/templates/header.php';
?>

<div style="display:flex; align-items:center; gap:16px; margin-bottom:24px;">
    <div>
        <h1 style="font-size:26px; font-weight:800; color:#0f172a;">Основные настройки</h1>
        <div style="color:#64748b; font-size:14px; margin-top:4px;">Название сайта, контакты, логотип и фавикон</div>
    </div>
</div>

<?php if ($message): ?><div class="flash-message flash-success"><?= e($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="flash-message flash-error"><?= e($error) ?></div><?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

    <div class="card" style="padding:24px; margin-bottom:16px;">
        <h3 style="font-size:16px; font-weight:700; margin-bottom:16px; color:#0f172a;">Реквизиты компании</h3>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div class="form-group" style="margin:0;">
                <label>Название сайта</label>
                <input type="text" name="site_name" value="<?= e(isset($s['site_name']) ? $s['site_name'] : 'PROFESSIONAL SOFTWARE') ?>" class="form-control">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Email сайта</label>
                <input type="email" name="site_email" value="<?= e(isset($s['site_email']) ? $s['site_email'] : '') ?>" class="form-control" placeholder="info@mastersoftware.ru">
            </div>
            <div class="form-group" style="margin:0; grid-column:1/-1;">
                <label>Телефон</label>
                <input type="text" name="site_phone" value="<?= e(isset($s['site_phone']) ? $s['site_phone'] : '') ?>" class="form-control" placeholder="+7 (812) 945-31-43">
            </div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
        <div class="card" style="padding:24px;">
            <h3 style="font-size:16px; font-weight:700; margin-bottom:16px; color:#0f172a;">Логотип</h3>
            <div style="display:flex; align-items:center; gap:14px; padding:14px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0; margin-bottom:14px;">
                <img src="<?= url('/site-assets.php?type=logo') ?>&v=<?= time() ?>" alt="Логотип" style="width:56px; height:56px; object-fit:contain; background:#fff; border:1px solid #e2e8f0; border-radius:10px;">
                <div>
                    <div style="font-weight:700; color:#0f172a; font-size:14px;">Текущий логотип</div>
                    <div style="font-size:12px; color:#64748b;"><?= e(isset($s['site_logo_path']) ? $s['site_logo_path'] : 'images/Logo-Master-Software.ico') ?></div>
                </div>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Загрузить новый логотип</label>
                <input type="file" name="logo_file" class="form-control" accept=".ico,.png,.jpg,.jpeg,.webp,.svg,image/*">
                <div style="font-size:12px; color:#64748b; margin-top:6px;">Форматы: ico, png, jpg, svg, webp</div>
            </div>
        </div>

        <div class="card" style="padding:24px;">
            <h3 style="font-size:16px; font-weight:700; margin-bottom:16px; color:#0f172a;">Фавикон</h3>
            <div style="display:flex; align-items:center; gap:14px; padding:14px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0; margin-bottom:14px;">
                <img src="<?= url('/site-assets.php?type=favicon') ?>&v=<?= time() ?>" alt="Фавикон" style="width:56px; height:56px; object-fit:contain; background:#fff; border:1px solid #e2e8f0; border-radius:10px;">
                <div>
                    <div style="font-weight:700; color:#0f172a; font-size:14px;">Текущий фавикон</div>
                    <div style="font-size:12px; color:#64748b;"><?= e(isset($s['site_favicon_path']) ? $s['site_favicon_path'] : 'images/Logo-Master-Software.ico') ?></div>
                </div>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Загрузить новый фавикон</label>
                <input type="file" name="favicon_file" class="form-control" accept=".ico,.png,.jpg,.jpeg,.webp,.svg,image/*">
                <div style="font-size:12px; color:#64748b; margin-top:6px;">Рекомендуется: ico или PNG 512×512</div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Сохранить настройки</button>
</form>

<?php include APP_ROOT . '/admin/templates/footer.php'; ?>
