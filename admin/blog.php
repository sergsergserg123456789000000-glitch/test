<?php
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
requireAdmin();

$db = db();
$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Администратор';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'admin';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';
$post = null;

// На старых БД добавляем колонку обложки, если её ещё нет.
try {
    $db->exec("ALTER TABLE blog_posts ADD COLUMN cover_image TEXT");
} catch (Exception $e) {}

function blogUploadFile($field, $prefix) {
    if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = array('jpg', 'jpeg', 'png', 'webp', 'gif');
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        throw new Exception('Недопустимый формат изображения. Разрешены: jpg, jpeg, png, webp, gif');
    }

    if (!is_dir(IMAGES_DIR)) {
        mkdir(IMAGES_DIR, 0755, true);
    }
    $dir = IMAGES_DIR . DS . 'blog';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    if (!is_writable($dir)) {
        throw new Exception('Папка images/blog недоступна для записи. Установите права 775.');
    }

    $filename = $prefix . '-' . time() . '.' . $ext;
    $target = $dir . DS . $filename;
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
        throw new Exception('Не удалось сохранить изображение. Проверьте права папки images/blog.');
    }

    return 'images/blog/' . $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
        $error = 'Ошибка безопасности';
    } else {
        try {
            // Загрузка картинки для вставки в текст. Не требует сохранения статьи.
            if (isset($_POST['upload_inline_image'])) {
                $inlinePath = blogUploadFile('inline_image', 'inline');
                if ($inlinePath) {
                    $absolutePath = url('/' . $inlinePath);
                    $message = 'Картинка загружена. Вставьте в текст: <code style="background:#eef2ff;padding:2px 6px;border-radius:4px;">![Описание](' . e($absolutePath) . ')</code>';
                } else {
                    $error = 'Выберите файл изображения';
                }
            }

            if (isset($_POST['save_post'])) {
                $title = trim(isset($_POST['title']) ? $_POST['title'] : '');
                $content = trim(isset($_POST['content']) ? $_POST['content'] : '');
                $slugSource = trim(isset($_POST['slug']) && $_POST['slug'] !== '' ? $_POST['slug'] : $title);
                $slug = sanitizeSlug($slugSource);
                $excerpt = trim(isset($_POST['excerpt']) ? $_POST['excerpt'] : '');
                $category = trim(isset($_POST['category']) ? $_POST['category'] : '');
                $meta_title = trim(isset($_POST['meta_title']) ? $_POST['meta_title'] : '');
                $meta_description = trim(isset($_POST['meta_description']) ? $_POST['meta_description'] : '');
                $tags = trim(isset($_POST['tags']) ? $_POST['tags'] : '');
                $is_published = isset($_POST['is_published']) ? 1 : 0;
                $published_at = $is_published ? date('Y-m-d H:i:s') : null;

                if ($title === '' || $content === '') {
                    $error = 'Заполните заголовок и текст статьи';
                } elseif ($slug === '') {
                    $error = 'Не удалось сформировать slug. Укажите его вручную.';
                } else {
                    $cover = blogUploadFile('cover_image', $slug . '-cover');

                    if ($id > 0) {
                        $sql = "UPDATE blog_posts SET slug=?, title=?, excerpt=?, content=?, category=?, author_name=?, meta_title=?, meta_description=?, tags=?, is_published=?, published_at=CASE WHEN ?=1 AND published_at IS NULL THEN CURRENT_TIMESTAMP ELSE published_at END, updated_at=CURRENT_TIMESTAMP";
                        $params = array($slug, $title, $excerpt, $content, $category, $admin_name, $meta_title, $meta_description, $tags, $is_published, $is_published);
                        if ($cover) {
                            $sql .= ", cover_image=?";
                            $params[] = $cover;
                        }
                        $sql .= " WHERE id=?";
                        $params[] = $id;
                        $db->prepare($sql)->execute($params);
                        $message = 'Статья обновлена' . ($cover ? ' (обложка загружена)' : '');
                    } else {
                        $fields = 'slug, title, excerpt, content, category, author_name, meta_title, meta_description, tags, is_published, published_at';
                        $placeholders = '?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?';
                        $params = array($slug, $title, $excerpt, $content, $category, $admin_name, $meta_title, $meta_description, $tags, $is_published, $published_at);
                        if ($cover) {
                            $fields .= ', cover_image';
                            $placeholders .= ', ?';
                            $params[] = $cover;
                        }
                        $fields .= ', created_at, updated_at';
                        $placeholders .= ', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP';

                        $db->prepare("INSERT INTO blog_posts ($fields) VALUES ($placeholders)")->execute($params);
                        $id = (int)$db->lastInsertId();
                        $action = 'edit';
                        $message = 'Статья создана' . ($cover ? ' (обложка загружена)' : '');
                    }
                }
            }

            if (isset($_POST['delete_cover']) && $id > 0) {
                $db->prepare("UPDATE blog_posts SET cover_image=NULL WHERE id=?")->execute(array($id));
                $message = 'Обложка удалена';
            }

            if (isset($_POST['delete_post']) && !empty($_POST['post_id'])) {
                $db->prepare("DELETE FROM blog_posts WHERE id=?")->execute(array((int)$_POST['post_id']));
                $message = 'Статья удалена';
                $action = 'list';
                $id = 0;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

if ($action === 'edit' && $id > 0) {
    $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id=?");
    $stmt->execute(array($id));
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$post) {
        $action = 'list';
        $error = 'Статья не найдена';
    }
}

$posts = $db->query("SELECT * FROM blog_posts ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$currentCover = ($post && !empty($post['cover_image'])) ? $post['cover_image'] : '';

$page_title = $action === 'edit' ? 'Редактор статьи' : ($action === 'new' ? 'Новая статья' : 'Блог');
include APP_ROOT . '/admin/templates/header.php';
?>

<style>
.blog-layout { display:grid; grid-template-columns: 1.8fr 1fr; gap:16px; }
.editor-toolbar { display:flex; flex-wrap:wrap; gap:6px; padding:10px; border-bottom:1px solid #e2e8f0; background:#f8fafc; }
.editor-toolbar button { border:1px solid #dbe3ef; background:#fff; color:#334155; border-radius:6px; padding:5px 10px; font-size:12px; font-weight:600; cursor:pointer; }
.editor-toolbar button:hover { background:#eff6ff; border-color:#93c5fd; }
.editor-area { min-height:420px; font-family:Inter, sans-serif; line-height:1.6; }
.list-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
.post-meta { font-size:12px; color:#64748b; margin-top:4px; }
.post-actions { display:flex; gap:8px; justify-content:flex-end; }
.cover-preview { width:160px; height:90px; object-fit:contain; border-radius:8px; border:1px solid #e2e8f0; background:#f8fafc; }
@media (max-width: 1100px){ .blog-layout { grid-template-columns: 1fr; } }
</style>

<?php if ($action === 'list'): ?>
    <div class="list-header">
        <div>
            <h1 style="font-size:26px; font-weight:800; color:#0f172a;">Блог</h1>
            <div style="color:#64748b; font-size:14px; margin-top:4px;">Управление статьями, обложками и SEO</div>
        </div>
        <a href="<?= url('/admin/blog.php?action=new') ?>" class="btn btn-primary">+ Новая статья</a>
    </div>

    <?php if ($message): ?><div class="flash-message flash-success"><?= $message ?></div><?php endif; ?>
    <?php if ($error): ?><div class="flash-message flash-error"><?= e($error) ?></div><?php endif; ?>

    <div class="card" style="overflow:hidden;">
        <table class="table">
            <thead><tr><th>Обложка</th><th>Заголовок</th><th>Категория</th><th>Автор</th><th>Статус</th><th>Просмотры</th><th>Обновлена</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($posts as $item): ?>
                <tr>
                    <td>
                        <?php if (!empty($item['cover_image'])): ?>
                            <img src="<?= url('/' . $item['cover_image']) ?>?v=<?= time() ?>" alt="" class="cover-preview">
                        <?php else: ?><span style="color:#94a3b8;">—</span><?php endif; ?>
                    </td>
                    <td><div style="font-weight:700; color:#0f172a;"><?= e($item['title']) ?></div><div class="post-meta">/blog/<?= e($item['slug']) ?></div></td>
                    <td><span class="badge badge-info"><?= e(!empty($item['category']) ? $item['category'] : '—') ?></span></td>
                    <td><?= e(!empty($item['author_name']) ? $item['author_name'] : '—') ?></td>
                    <td><?= (int)$item['is_published'] ? '<span class="badge badge-success">Опубликована</span>' : '<span class="badge badge-warning">Черновик</span>' ?></td>
                    <td><?= (int)$item['views_count'] ?></td>
                    <td><?= formatDate($item['updated_at'], 'd.m.Y H:i') ?></td>
                    <td>
                        <div class="post-actions">
                            <a href="<?= url('/admin/blog.php?action=edit&id=' . $item['id']) ?>" class="btn btn-secondary btn-sm">Ред.</a>
                            <form method="POST" onsubmit="return confirm('Удалить статью?');"><input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>"><input type="hidden" name="post_id" value="<?= (int)$item['id'] ?>"><button type="submit" name="delete_post" class="btn btn-danger btn-sm">Удалить</button></form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="list-header">
        <div>
            <h1 style="font-size:26px; font-weight:800; color:#0f172a;"><?= $action === 'new' ? 'Новая статья' : 'Редактор статьи' ?></h1>
            <div style="color:#64748b; font-size:14px; margin-top:4px;">Пишите, добавляйте картинки, загружайте обложку</div>
        </div>
        <a href="<?= url('/admin/blog.php') ?>" class="btn btn-secondary">← К списку</a>
    </div>

    <?php if ($message): ?><div class="flash-message flash-success" style="word-break:break-all;"><?= $message ?></div><?php endif; ?>
    <?php if ($error): ?><div class="flash-message flash-error"><?= e($error) ?></div><?php endif; ?>

    <form method="POST" class="blog-layout" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
        <div class="card" style="overflow:hidden;">
            <div class="editor-toolbar">
                <button type="button" onclick="insertAtCursor('content', '\n## Подзаголовок\n')">H2</button>
                <button type="button" onclick="insertAtCursor('content', '\n### Подзаголовок\n')">H3</button>
                <button type="button" onclick="insertAtCursor('content', '**жирный**')">Жирный</button>
                <button type="button" onclick="insertAtCursor('content', '*курсив*')">Курсив</button>
                <button type="button" onclick="insertAtCursor('content', '\n- Пункт\n- Пункт\n')">Список</button>
                <button type="button" onclick="insertAtCursor('content', '\n> Цитата\n')">Цитата</button>
                <button type="button" onclick="insertAtCursor('content', '\n[текст ссылки](https://)\n')">Ссылка</button>
                <button type="button" onclick="insertAtCursor('content', '\n![Описание картинки](url-картинки)\n')" style="background:#dbeafe; border-color:#93c5fd; color:#1e40af;">🖼 Картинка</button>
            </div>
            <div style="padding:20px;">
                <div class="form-group"><label>Заголовок</label><input type="text" name="title" value="<?= e($post ? $post['title'] : '') ?>" class="form-control" required></div>
                <div class="form-group"><label>Кратко (excerpt)</label><textarea name="excerpt" rows="2" class="form-control"><?= e($post ? $post['excerpt'] : '') ?></textarea></div>
                <div class="form-group" style="margin-bottom:0;"><label>Текст статьи</label><textarea id="content" name="content" rows="18" class="form-control editor-area" required placeholder="Пишите... Для вставки картинки: ![описание](url)"><?= e($post ? $post['content'] : '') ?></textarea></div>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:16px;">
            <div class="card" style="padding:20px;"><h3 style="font-size:16px; font-weight:800; color:#0f172a; margin-bottom:16px;">Публикация</h3><label style="display:flex; align-items:center; gap:8px; margin-bottom:16px;"><input type="checkbox" name="is_published" <?= (!$post || !empty($post['is_published'])) ? 'checked' : '' ?>> <span>Опубликовать</span></label><div style="display:flex; gap:10px; flex-wrap:wrap;"><button type="submit" name="save_post" class="btn btn-primary">Сохранить</button><a href="<?= url('/admin/blog.php') ?>" class="btn btn-secondary">Отмена</a></div></div>

            <div class="card" style="padding:20px;"><h3 style="font-size:14px; font-weight:800; color:#0f172a; margin-bottom:12px;">🖼 Загрузить картинку в статью</h3><div class="form-group" style="margin-bottom:8px;"><input type="file" name="inline_image" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif,image/*"><div style="font-size:11px; color:#94a3b8; margin-top:4px;">После загрузки скопируйте строку и вставьте в текст</div></div><button type="submit" name="upload_inline_image" class="btn btn-secondary btn-sm" formnovalidate>Загрузить</button></div>

            <div class="card" style="padding:20px;"><h3 style="font-size:14px; font-weight:800; color:#0f172a; margin-bottom:12px;">📷 Обложка статьи</h3><?php if ($currentCover): ?><div style="display:flex; align-items:center; gap:14px; margin-bottom:12px;"><img src="<?= url('/' . $currentCover) ?>?v=<?= time() ?>" alt="Обложка" class="cover-preview"><div style="font-size:12px; color:#64748b;"><?= e($currentCover) ?></div></div><button type="submit" name="delete_cover" value="1" class="btn btn-danger btn-sm" style="margin-bottom:12px;">Удалить обложку</button><?php endif; ?><div class="form-group" style="margin:0;"><label><?= $currentCover ? 'Заменить обложку' : 'Загрузить обложку' ?></label><input type="file" name="cover_image" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/*"><div style="font-size:11px; color:#94a3b8; margin-top:4px;">1200×630px, jpg/png/webp</div></div></div>

            <div class="card" style="padding:20px;"><h3 style="font-size:14px; font-weight:800; color:#0f172a; margin-bottom:12px;">Параметры</h3><div class="form-group"><label>Slug</label><input type="text" name="slug" value="<?= e($post ? $post['slug'] : '') ?>" class="form-control"></div><div class="form-group"><label>Категория</label><input type="text" name="category" value="<?= e($post ? $post['category'] : '') ?>" class="form-control"></div><div class="form-group" style="margin-bottom:0;"><label>Теги</label><input type="text" name="tags" value="<?= e($post ? $post['tags'] : '') ?>" class="form-control"></div></div>

            <div class="card" style="padding:20px;"><h3 style="font-size:14px; font-weight:800; color:#0f172a; margin-bottom:12px;">SEO</h3><div class="form-group"><label>Meta title</label><input type="text" name="meta_title" value="<?= e($post ? $post['meta_title'] : '') ?>" class="form-control"></div><div class="form-group" style="margin-bottom:0;"><label>Meta description</label><textarea name="meta_description" rows="3" class="form-control"><?= e($post ? $post['meta_description'] : '') ?></textarea></div></div>
        </div>
    </form>

    <script>
    function insertAtCursor(id, text) { var ta=document.getElementById(id); if(!ta)return; var s=ta.selectionStart,e=ta.selectionEnd; ta.value=ta.value.substring(0,s)+text+ta.value.substring(e); ta.focus(); var p=s+text.length; ta.setSelectionRange(p,p); }
    </script>
<?php endif; ?>

<?php include APP_ROOT . '/admin/templates/footer.php'; ?>
