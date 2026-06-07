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

// Гарантируем наличие таблицы (на случай старой БД)
$db->exec("CREATE TABLE IF NOT EXISTS testimonials (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    author_name TEXT NOT NULL,
    author_role TEXT,
    text TEXT NOT NULL,
    rating INTEGER DEFAULT 5,
    avatar TEXT,
    is_published INTEGER DEFAULT 1,
    sort_order INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
        $error = 'Ошибка безопасности';
    } else {
        $action = isset($_POST['action']) ? $_POST['action'] : '';

        if ($action === 'save') {
            $id     = (int)($_POST['id'] ?? 0);
            $name   = trim($_POST['author_name'] ?? '');
            $role   = trim($_POST['author_role'] ?? '');
            $text   = trim($_POST['text'] ?? '');
            $rating = (int)($_POST['rating'] ?? 5);
            $pub    = isset($_POST['is_published']) ? 1 : 0;
            $avatar = mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8');

            if ($name === '' || $text === '') {
                $error = 'Заполните имя и текст отзыва';
            } elseif ($id > 0) {
                $db->prepare("UPDATE testimonials SET author_name=?, author_role=?, text=?, rating=?, avatar=?, is_published=? WHERE id=?")
                   ->execute(array($name, $role, $text, $rating, $avatar, $pub, $id));
                $message = 'Отзыв обновлён';
            } else {
                $maxOrder = (int)$db->query("SELECT COALESCE(MAX(sort_order),0) FROM testimonials")->fetchColumn();
                $db->prepare("INSERT INTO testimonials (author_name, author_role, text, rating, avatar, is_published, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)")
                   ->execute(array($name, $role, $text, $rating, $avatar, $pub, $maxOrder + 1));
                $message = 'Отзыв добавлен';
            }
        } elseif ($action === 'delete') {
            $db->prepare("DELETE FROM testimonials WHERE id=?")->execute(array((int)$_POST['id']));
            $message = 'Отзыв удалён';
        } elseif ($action === 'toggle') {
            $db->prepare("UPDATE testimonials SET is_published = 1 - is_published WHERE id=?")->execute(array((int)$_POST['id']));
            $message = 'Статус изменён';
        }
    }
}

$rows = $db->query("SELECT * FROM testimonials ORDER BY sort_order ASC, created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Отзывы';
include APP_ROOT . '/admin/templates/header.php';
?>

<style>
.t-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap:16px; }
.t-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:18px; position:relative; }
.t-card.unpub { opacity:0.7; border-style:dashed; }
.t-stars { color:#f97316; font-size:14px; letter-spacing:2px; }
.t-text { color:#334155; font-size:14px; line-height:1.5; margin:10px 0; }
.t-author { display:flex; align-items:center; gap:10px; }
.t-avatar { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#2563eb,#1d4ed8); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; }
.t-actions { display:flex; gap:6px; margin-top:14px; }
.modal-bg { position:fixed; inset:0; background:rgba(15,23,42,0.5); display:none; align-items:center; justify-content:center; z-index:1000; padding:20px; }
.modal-bg.show { display:flex; }
.modal-box { background:#fff; border-radius:16px; padding:24px; width:100%; max-width:520px; max-height:90vh; overflow:auto; }
</style>

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
    <div>
        <h1 style="font-size:26px; font-weight:800; color:#0f172a;">Отзывы</h1>
        <div style="color:#64748b; font-size:14px; margin-top:4px;">Управление отзывами, которые показываются на главной</div>
    </div>
    <button class="btn btn-primary" onclick="openModal()">+ Добавить отзыв</button>
</div>

<?php if ($message): ?><div class="flash-message flash-success"><?= e($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="flash-message flash-error"><?= e($error) ?></div><?php endif; ?>

<div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:12px 16px; margin-bottom:18px; font-size:13px; color:#1e40af;">
    Опубликовано: <strong><?= count(array_filter($rows, function($r){ return $r['is_published'] == 1; })) ?></strong> ·
    Если опубликовано более 3 отзывов — на сайте они показываются в виде карусели.
</div>

<div class="t-grid">
    <?php foreach ($rows as $r): ?>
    <div class="t-card <?= $r['is_published'] ? '' : 'unpub' ?>">
        <div class="t-stars"><?= str_repeat('★', (int)$r['rating']) . str_repeat('☆', 5 - (int)$r['rating']) ?></div>
        <div class="t-text">«<?= e($r['text']) ?>»</div>
        <div class="t-author">
            <div class="t-avatar"><?= e($r['avatar'] ?: mb_substr($r['author_name'],0,1)) ?></div>
            <div>
                <div style="font-weight:700; color:#0f172a; font-size:14px;"><?= e($r['author_name']) ?></div>
                <div style="color:#64748b; font-size:12px;"><?= e($r['author_role']) ?></div>
            </div>
            <div style="margin-left:auto;">
                <?php if ($r['is_published']): ?>
                    <span class="badge badge-success">Опубликован</span>
                <?php else: ?>
                    <span class="badge" style="background:#fef3c7; color:#854d0e; border-radius:9999px; padding:2px 8px; font-size:11px; font-weight:600;">На модерации</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="t-actions">
            <button class="btn btn-secondary btn-sm" onclick='editTestimonial(<?= json_encode($r, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>Изменить</button>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                <button class="btn btn-secondary btn-sm" type="submit"><?= $r['is_published'] ? 'Скрыть' : 'Опубликовать' ?></button>
            </form>
            <form method="POST" style="display:inline;" onsubmit="return confirm('Удалить отзыв?')">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                <button class="btn btn-sm" type="submit" style="background:#fee2e2; color:#991b1b;">Удалить</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Модалка -->
<div class="modal-bg" id="modal">
    <div class="modal-box">
        <h2 style="font-size:20px; font-weight:800; color:#0f172a; margin-bottom:16px;" id="modalTitle">Новый отзыв</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" id="f_id" value="0">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div class="form-group"><label>Имя автора</label><input type="text" name="author_name" id="f_name" class="form-control" required></div>
                <div class="form-group"><label>Должность / компания</label><input type="text" name="author_role" id="f_role" class="form-control"></div>
            </div>
            <div class="form-group"><label>Текст отзыва</label><textarea name="text" id="f_text" rows="4" class="form-control" required></textarea></div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; align-items:center;">
                <div class="form-group" style="margin:0;">
                    <label>Оценка</label>
                    <select name="rating" id="f_rating" class="form-control">
                        <option value="5">★★★★★ (5)</option>
                        <option value="4">★★★★ (4)</option>
                        <option value="3">★★★ (3)</option>
                        <option value="2">★★ (2)</option>
                        <option value="1">★ (1)</option>
                    </select>
                </div>
                <label style="display:flex; align-items:center; gap:8px; margin-top:18px;">
                    <input type="checkbox" name="is_published" id="f_pub" checked style="width:18px; height:18px;">
                    <span>Опубликовать</span>
                </label>
            </div>
            <div style="display:flex; gap:10px; margin-top:18px;">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Отмена</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('modalTitle').textContent = 'Новый отзыв';
    document.getElementById('f_id').value = 0;
    document.getElementById('f_name').value = '';
    document.getElementById('f_role').value = '';
    document.getElementById('f_text').value = '';
    document.getElementById('f_rating').value = '5';
    document.getElementById('f_pub').checked = true;
    document.getElementById('modal').classList.add('show');
}
function editTestimonial(r) {
    document.getElementById('modalTitle').textContent = 'Редактировать отзыв';
    document.getElementById('f_id').value = r.id;
    document.getElementById('f_name').value = r.author_name;
    document.getElementById('f_role').value = r.author_role || '';
    document.getElementById('f_text').value = r.text;
    document.getElementById('f_rating').value = r.rating;
    document.getElementById('f_pub').checked = (r.is_published == 1);
    document.getElementById('modal').classList.add('show');
}
function closeModal() {
    document.getElementById('modal').classList.remove('show');
}
document.getElementById('modal').addEventListener('click', function(e){
    if (e.target === this) closeModal();
});
</script>

<?php include APP_ROOT . '/admin/templates/footer.php'; ?>
