<?php
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
requireAdmin();

$db = db();
$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Администратор';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'admin';
$message = '';
$error = '';

// Создание таблицы, если нет
$db->exec("CREATE TABLE IF NOT EXISTS product_categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    sort_order INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
        $error = 'Ошибка безопасности';
    } else {
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        if ($action === 'save') {
            $id   = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? sanitizeSlug($name));
            $sort = (int)($_POST['sort_order'] ?? 0);
            if ($name === '') {
                $error = 'Введите название категории';
            } elseif ($id > 0) {
                $db->prepare("UPDATE product_categories SET name=?, slug=?, sort_order=? WHERE id=?")->execute(array($name, $slug, $sort, $id));
                $message = 'Категория обновлена';
            } else {
                $db->prepare("INSERT INTO product_categories (name, slug, sort_order) VALUES (?, ?, ?)")->execute(array($name, $slug, $sort));
                $message = 'Категория добавлена';
            }
        } elseif ($action === 'delete') {
            $db->prepare("DELETE FROM product_categories WHERE id=?")->execute(array((int)$_POST['id']));
            $message = 'Категория удалена';
        }
    }
}

$cats = $db->query("SELECT * FROM product_categories ORDER BY sort_order ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Категории';
include APP_ROOT . '/admin/templates/header.php';
?>

<style>
.modal-bg { position:fixed; inset:0; background:rgba(15,23,42,0.5); display:none; align-items:center; justify-content:center; z-index:1000; padding:20px; }
.modal-bg.show { display:flex; }
</style>

<div style="margin-bottom:24px;">
    <h1 style="font-size:26px; font-weight:800; color:#0f172a;">Категории продуктов</h1>
    <div style="color:#64748b; font-size:14px; margin-top:4px;">Управление категориями, в которые группируются продукты</div>
</div>

<?php if ($message): ?><div class="flash-message flash-success"><?= e($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="flash-message flash-error"><?= e($error) ?></div><?php endif; ?>

<div style="margin-bottom:16px;">
    <button class="btn btn-primary" onclick="openCatModal()">+ Добавить категорию</button>
</div>

<div class="card" style="overflow:hidden;">
    <table class="table">
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th>Название</th>
                <th>Slug</th>
                <th>Сортировка</th>
                <th style="width:200px;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cats as $c): ?>
            <tr>
                <td><?= (int)$c['id'] ?></td>
                <td style="font-weight:700;"><?= e($c['name']) ?></td>
                <td style="color:#64748b; font-family:monospace;"><?= e($c['slug']) ?></td>
                <td><?= (int)$c['sort_order'] ?></td>
                <td>
                    <button class="btn btn-secondary btn-sm" onclick='editCat(<?= json_encode($c, JSON_UNESCAPED_UNICODE) ?>)'>Изменить</button>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Удалить категорию?')">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Модалка -->
<div class="modal-bg" id="catModal">
    <div class="card" style="padding:24px; width:100%; max-width:420px;">
        <h2 style="font-size:20px; font-weight:800; color:#0f172a; margin-bottom:16px;" id="catModalTitle">Новая категория</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" id="f_cat_id" value="0">

            <div class="form-group">
                <label>Название</label>
                <input type="text" name="name" id="f_cat_name" class="form-control" required placeholder="Безопасность">
            </div>
            <div class="form-group">
                <label>Slug (URL)</label>
                <input type="text" name="slug" id="f_cat_slug" class="form-control" placeholder="bezopasnost">
                <div style="font-size:11px; color:#94a3b8; margin-top:4px;">Если оставить пустым, slug формируется из названия</div>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Порядок сортировки</label>
                <input type="number" name="sort_order" id="f_cat_sort" class="form-control" value="0" style="max-width:120px;">
            </div>
            <div style="display:flex; gap:10px; margin-top:18px;">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('catModal').classList.remove('show')">Отмена</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCatModal() {
    document.getElementById('catModalTitle').textContent = 'Новая категория';
    document.getElementById('f_cat_id').value = 0;
    document.getElementById('f_cat_name').value = '';
    document.getElementById('f_cat_slug').value = '';
    document.getElementById('f_cat_sort').value = '0';
    document.getElementById('catModal').classList.add('show');
}
function editCat(c) {
    document.getElementById('catModalTitle').textContent = 'Редактировать категорию';
    document.getElementById('f_cat_id').value = c.id;
    document.getElementById('f_cat_name').value = c.name;
    document.getElementById('f_cat_slug').value = c.slug;
    document.getElementById('f_cat_sort').value = c.sort_order;
    document.getElementById('catModal').classList.add('show');
}
document.getElementById('catModal').addEventListener('click', function(e){ if(e.target===this) this.classList.remove('show'); });
</script>

<?php include APP_ROOT . '/admin/templates/footer.php'; ?>
