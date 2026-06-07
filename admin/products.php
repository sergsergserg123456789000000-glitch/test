<?php
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
requireAdmin();

$db = db();
$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Администратор';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'admin';
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';

// Категории из БД
$allCats = $db->query("SELECT name FROM product_categories ORDER BY sort_order ASC, name ASC")->fetchAll(PDO::FETCH_COLUMN);
if (!$allCats) $allCats = array('Безопасность','Утилиты','Облако','Бизнес');

function uploadCover($field, $productSlug) {
    if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;
    $allowed = array('jpg','jpeg','png','webp');
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) throw new Exception('Недопустимый формат. Разрешены: jpg, png, webp');
    $dir = IMAGES_DIR . DS . 'products';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $filename = $productSlug . '-cover.' . $ext;
    if (move_uploaded_file($_FILES[$field]['tmp_name'], $dir . DS . $filename)) {
        return 'images/products/' . $filename;
    }
    return null;
}

function uploadProductImages($field, $productId, $productSlug, $db) {
    if (empty($_FILES[$field]['name']) || !is_array($_FILES[$field]['name'])) return 0;
    $allowed = array('jpg','jpeg','png','webp');
    $dir = IMAGES_DIR . DS . 'products' . DS . $productSlug;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $count = 0;
    for ($i = 0; $i < count($_FILES[$field]['name']); $i++) {
        if ($_FILES[$field]['error'][$i] !== UPLOAD_ERR_OK) continue;
        $ext = strtolower(pathinfo($_FILES[$field]['name'][$i], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) continue;
        $filename = 'screenshot-' . time() . '-' . $i . '.' . $ext;
        $target = $dir . DS . $filename;
        if (move_uploaded_file($_FILES[$field]['tmp_name'][$i], $target)) {
            $rel = 'images/products/' . $productSlug . '/' . $filename;
            $sort = (int)$db->query("SELECT COALESCE(MAX(sort_order),0) FROM product_images WHERE product_id=" . (int)$productId)->fetchColumn() + 1;
            $db->prepare("INSERT INTO product_images (product_id, image_path, image_type, sort_order) VALUES (?, ?, 'screenshot', ?)")->execute(array($productId, $rel, $sort));
            $count++;
        }
    }
    return $count;
}

// Создаём таблицы, если их нет
$db->exec("CREATE TABLE IF NOT EXISTS product_versions (id INTEGER PRIMARY KEY AUTOINCREMENT, product_id INTEGER NOT NULL, version TEXT NOT NULL, release_date TEXT NOT NULL, file_size TEXT, download_url TEXT, file_hash TEXT, is_current INTEGER DEFAULT 0, changelog TEXT, created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
try {
    $db->exec("CREATE TABLE IF NOT EXISTS product_images (id INTEGER PRIMARY KEY AUTOINCREMENT, product_id INTEGER NOT NULL, image_path TEXT NOT NULL, image_type TEXT DEFAULT 'screenshot', sort_order INTEGER DEFAULT 0, created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_product_images_product ON product_images(product_id)");
} catch (Exception $e) {}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Ошибка безопасности';
    } else {
        try {
        if (isset($_POST['save_product'])) {
            $slug = sanitizeSlug(trim($_POST['slug'] ?: $_POST['name']));
            $data = array(
                'slug' => $slug,
                'name' => trim($_POST['name']),
                'tagline' => trim($_POST['tagline']),
                'category' => trim($_POST['category']),
                'price' => (float)$_POST['price'],
                'old_price' => $_POST['old_price'] !== '' ? (float)$_POST['old_price'] : null,
                'description' => trim($_POST['description']),
                'features' => json_encode(array_filter(explode("\n", $_POST['features']))),
                'os_support' => json_encode($_POST['os'] ?? array()),
                'requirements' => json_encode(array(
                    'os' => $_POST['req_os'] ?? '',
                    'cpu' => $_POST['req_cpu'] ?? '',
                    'ram' => $_POST['req_ram'] ?? '',
                    'disk' => $_POST['req_disk'] ?? ''
                )),
                'badge' => $_POST['badge'] ?: null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            );

            // Обложка
            $cover = null;
            try {
                $cover = uploadCover('cover_image', $slug);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }

            if ($id > 0) {
                // UPDATE
                $fields = 'slug=?,name=?,tagline=?,category=?,price=?,old_price=?,description=?,features=?,os_support=?,requirements=?,badge=?,is_active=?,updated_at=CURRENT_TIMESTAMP';
                $vals = array_values($data);
                if ($cover) {
                    $fields .= ',cover_image=?';
                    $vals[] = $cover;
                }
                $vals[] = $id;
                $db->prepare("UPDATE products SET $fields WHERE id=?")->execute($vals);
                $message = 'Продукт обновлён';
            } else {
                // INSERT
                $fields = 'slug,name,tagline,category,price,old_price,description,features,os_support,requirements,badge,is_active';
                $ph = '?,?,?,?,?,?,?,?,?,?,?,?';
                $vals = array_values($data);
                if ($cover) {
                    $fields .= ',cover_image';
                    $ph .= ',?';
                    $vals[] = $cover;
                }
                $db->prepare("INSERT INTO products ($fields) VALUES ($ph)")->execute($vals);
                $id = (int)$db->lastInsertId();
                $message = 'Продукт создан';
            }
            $action = 'edit';
        }

        if (isset($_POST['add_version']) && $id) {
            $db->prepare("INSERT INTO product_versions (product_id, version, release_date, file_size, download_url, is_current, changelog) VALUES (?, ?, ?, ?, ?, ?, ?)")
               ->execute(array($id, $_POST['version'], $_POST['release_date'], $_POST['file_size'], $_POST['download_url'], isset($_POST['is_current'])?1:0, $_POST['changelog']));
            if (isset($_POST['is_current'])) {
                $db->prepare("UPDATE product_versions SET is_current=0 WHERE product_id=? AND id != ?")->execute(array($id, $db->lastInsertId()));
            }
            $message = 'Версия добавлена';
        }
        if (isset($_POST['delete_version'])) {
            $db->prepare("DELETE FROM product_versions WHERE id=?")->execute(array((int)$_POST['delete_version']));
            $message = 'Версия удалена';
        }
        if (isset($_POST['delete_cover'])) {
            $db->prepare("UPDATE products SET cover_image=NULL WHERE id=?")->execute(array($id));
            $message = 'Обложка удалена';
        }
        if (isset($_POST['delete_image']) && $id) {
            $db->prepare("DELETE FROM product_images WHERE id=? AND product_id=?")->execute(array((int)$_POST['delete_image'], $id));
            $message = 'Скриншот удалён';
        }
        if (isset($_POST['upload_screenshots']) && $id) {
            $stmt = $db->prepare("SELECT slug FROM products WHERE id=?");
            $stmt->execute(array($id));
            $slugForImages = $stmt->fetchColumn();
            $uploaded = 0;
            try {
                $uploaded = uploadProductImages('screenshots', $id, $slugForImages, $db);
            } catch (Exception $e) {
                $error = 'Ошибка загрузки скриншотов: ' . $e->getMessage();
            }
            $message = 'Загружено скриншотов: ' . $uploaded;
        }
    } catch (Exception $e) {
        $error = 'Ошибка: ' . $e->getMessage();
    }
    }
}

if ($action === 'delete' && $id) {
    $db->prepare("DELETE FROM products WHERE id=?")->execute(array($id));
    $action = 'list';
    $message = 'Продукт удалён';
}

$products = $db->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
$product = null;
$versions = array();
$productImages = array();
$currentCover = '';

if (in_array($action, array('edit','new')) && $id) {
    $stmt = $db->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute(array($id));
    $product = $stmt->fetch();
    if ($product) {
        $stmt = $db->prepare("SELECT * FROM product_versions WHERE product_id=? ORDER BY created_at DESC");
        $stmt->execute(array($id));
        $versions = $stmt->fetchAll();
        $stmt = $db->prepare("SELECT * FROM product_images WHERE product_id=? ORDER BY sort_order ASC, id ASC");
        $stmt->execute(array($id));
        $productImages = $stmt->fetchAll();
        $currentCover = $product['cover_image'] ?? '';
    } else {
        $action = 'new';
        $id = 0;
    }
}

$page_title = $action === 'edit' ? 'Редактировать продукт' : ($action === 'new' ? 'Новый продукт' : 'Продукты');
include APP_ROOT . '/admin/templates/header.php';
?>

<style>
.cover-preview { width:160px; height:90px; object-fit:cover; border-radius:8px; border:1px solid #e2e8f0; }
</style>

<?php if ($action === 'list'): ?>
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <div>
            <h1 style="font-size:26px; font-weight:800; color:#0f172a;">Продукты</h1>
            <div style="color:#64748b; font-size:14px; margin-top:4px;">Каталог продуктов, версии, обложки и загрузки</div>
        </div>
        <a href="<?= url('/admin/products.php?action=new') ?>" class="btn btn-primary">+ Добавить продукт</a>
    </div>

    <?php if ($message): ?><div class="flash-message flash-success"><?= e($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="flash-message flash-error"><?= e($error) ?></div><?php endif; ?>

    <div class="card" style="overflow:hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Обложка</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Цена</th>
                    <th>Статус</th>
                    <th>Создан</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <?php if (!empty($p['cover_image'])): ?>
                            <img src="<?= url('/' . $p['cover_image']) ?>?v=<?= time() ?>" alt="" class="cover-preview">
                        <?php else: ?>
                            <span style="color:#94a3b8;">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight:700; color:#0f172a;"><?= e($p['name']) ?></div>
                        <div style="font-size:12px; color:#64748b;"><?= e($p['slug']) ?></div>
                    </td>
                    <td><span class="badge badge-info"><?= e($p['category']) ?></span></td>
                    <td style="font-weight:700;"><?= formatPrice($p['price']) ?></td>
                    <td>
                        <?php if ($p['is_active']): ?>
                            <span class="badge badge-success">Активен</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Скрыт</span>
                        <?php endif; ?>
                        <?php if ($p['badge']): ?>
                            <span class="badge badge-warning"><?= e($p['badge']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="color:#64748b;"><?= formatDate($p['created_at'], 'd.m.Y') ?></td>
                    <td style="text-align:right;">
                        <a href="<?= url('/admin/products.php?action=edit&id=' . $p['id']) ?>" class="btn btn-secondary btn-sm">Редактировать</a>
                        <a href="<?= url('/admin/products.php?action=delete&id=' . $p['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Удалить продукт?')">Удалить</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php else: ?>
    <div style="margin-bottom:20px;">
        <a href="<?= url('/admin/products.php') ?>" class="btn btn-secondary btn-sm">← К списку</a>
        <span style="font-size:22px; font-weight:800; margin-left:12px; color:#0f172a;"><?= e($page_title) ?></span>
    </div>

    <?php if ($message): ?><div class="flash-message flash-success"><?= e($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="flash-message flash-error"><?= e($error) ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

        <div class="card" style="padding:20px; margin-bottom:16px;">
            <h2 style="font-size:16px; font-weight:700; margin-bottom:16px;">Основная информация</h2>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div class="form-group"><label>Slug (URL) <span id="slugAuto" style="font-weight:400; color:#94a3b8; font-size:11px;"></span></label><input type="text" name="slug" id="f_slug" value="<?= e($product['slug'] ?? '') ?>" class="form-control" placeholder="заполнится из названия"></div>
            <div class="form-group"><label>Название</label><input type="text" name="name" id="f_name" value="<?= e($product['name'] ?? '') ?>" required class="form-control" oninput="autoSlug()"></div>
            <script>
            var slugChars = {'а':'a','б':'b','в':'v','г':'g','д':'d','е':'e','ё':'yo','ж':'zh','з':'z','и':'i','й':'y','к':'k','л':'l','м':'m','н':'n','о':'o','п':'p','р':'r','с':'s','т':'t','у':'u','ф':'f','х':'kh','ц':'ts','ч':'ch','ш':'sh','щ':'shch','ъ':'','ы':'y','ь':'','э':'e','ю':'yu','я':'ya'};
            function autoSlug(){
                var slugEl=document.getElementById('f_slug');
                if(slugEl.value && slugEl._manual) return;
                var name=document.getElementById('f_name').value.toLowerCase();
                var slug=name.split('').map(function(c){return slugChars[c]||(/[a-z0-9]/.test(c)?c:'-')}).join('').replace(/-+/g,'-').replace(/^-|-$/g,'');
                slugEl.value=slug;
                slugEl._manual=false;
                document.getElementById('slugAuto').textContent='✦ авто';
            }
            document.getElementById('f_slug').addEventListener('input',function(){this._manual=true;document.getElementById('slugAuto').textContent='';});
            <?php if (!$product): ?>setTimeout(autoSlug,50);<?php endif; ?>
            </script>
                <div style="grid-column:1/-1;" class="form-group"><label>Краткое описание</label><input type="text" name="tagline" value="<?= e($product['tagline'] ?? '') ?>" class="form-control"></div>
                <div class="form-group">
                    <label>Категория</label>
                    <select name="category" class="form-control">
                        <?php foreach ($allCats as $cat): ?>
                            <option value="<?= e($cat) ?>" <?= ($product['category'] ?? '') === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Бейдж</label>
                    <select name="badge" class="form-control">
                        <option value="">—</option>
                        <option value="Хит" <?= ($product['badge'] ?? '') === 'Хит' ? 'selected' : '' ?>>Хит</option>
                        <option value="Новинка" <?= ($product['badge'] ?? '') === 'Новинка' ? 'selected' : '' ?>>Новинка</option>
                        <option value="Скидка" <?= ($product['badge'] ?? '') === 'Скидка' ? 'selected' : '' ?>>Скидка</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card" style="padding:20px; margin-bottom:16px;">
            <h2 style="font-size:16px; font-weight:700; margin-bottom:16px;">Ценообразование</h2>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div class="form-group"><label>Цена (₽)</label><input type="number" step="0.01" name="price" value="<?= e($product['price'] ?? '') ?>" required class="form-control"></div>
                <div class="form-group"><label>Старая цена (₽)</label><input type="number" step="0.01" name="old_price" value="<?= e($product['old_price'] ?? '') ?>" class="form-control"></div>
            </div>
        </div>

        <div class="card" style="padding:20px; margin-bottom:16px;">
            <h2 style="font-size:16px; font-weight:700; margin-bottom:16px;">Описание и возможности</h2>
            <div class="form-group"><label>Полное описание</label><textarea name="description" rows="4" class="form-control"><?= e($product['description'] ?? '') ?></textarea></div>
            <div class="form-group" style="margin-bottom:0;"><label>Возможности (по одной на строку)</label>
            <textarea name="features" rows="5" class="form-control"><?= $product ? implode("\n", json_decode($product['features'] ?? '[]', true)) : '' ?></textarea></div>
        </div>

        <div class="card" style="padding:20px; margin-bottom:16px;">
            <h2 style="font-size:16px; font-weight:700; margin-bottom:16px;">Обложка продукта</h2>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; align-items:start;">
                <div>
                    <?php if ($currentCover): ?>
                        <div style="display:flex; align-items:flex-start; gap:14px; margin-bottom:12px;">
                            <img src="<?= url('/' . $currentCover) ?>?v=<?= time() ?>" alt="Обложка" class="cover-preview">
                            <div style="font-size:12px; color:#64748b;"><?= e($currentCover) ?></div>
                        </div>
                        <div style="margin-bottom:10px;">
                            <button type="submit" name="delete_cover" value="1" class="btn btn-danger btn-sm" onclick="return confirm('Удалить обложку?')">Удалить обложку</button>
                        </div>
                    <?php endif; ?>
                    <div class="form-group" style="margin:0;">
                        <label><?= $currentCover ? 'Заменить обложку' : 'Загрузить обложку' ?></label>
                        <input type="file" name="cover_image" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/*">
                        <div style="font-size:11px; color:#94a3b8; margin-top:4px;">Рекомендуемый размер 1200×630px. Форматы: jpg, png, webp.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="padding:20px; margin-bottom:16px;">
            <h2 style="font-size:16px; font-weight:700; margin-bottom:16px;">Скриншоты / внешний вид</h2>
            <?php if ($id): ?>
                <?php if ($productImages): ?>
                    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:12px; margin-bottom:16px;">
                        <?php foreach ($productImages as $img): ?>
                            <div style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; background:#fff;">
                                <img src="<?= url('/' . $img['image_path']) ?>?v=<?= time() ?>" alt="Скриншот" style="width:100%; height:110px; object-fit:cover; display:block;">
                                <div style="padding:8px;">
                                    <button type="submit" name="delete_image" value="<?= (int)$img['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Удалить скриншот?')">Удалить</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="font-size:13px; color:#64748b; margin-bottom:14px;">Скриншоты ещё не загружены.</p>
                <?php endif; ?>
                <div class="form-group" style="margin:0;">
                    <label>Загрузить скриншоты</label>
                    <input type="file" name="screenshots[]" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/*" multiple>
                    <div style="font-size:11px; color:#94a3b8; margin-top:4px;">Можно выбрать несколько файлов. Форматы: jpg, png, webp.</div>
                </div>
                <div style="margin-top:10px;">
                    <button type="submit" name="upload_screenshots" class="btn btn-secondary btn-sm">Загрузить скриншоты</button>
                </div>
            <?php else: ?>
                <p style="font-size:13px; color:#64748b;">Сначала сохраните продукт, затем можно будет загружать скриншоты.</p>
            <?php endif; ?>
        </div>

        <div class="card" style="padding:20px; margin-bottom:16px;">
            <h2 style="font-size:16px; font-weight:700; margin-bottom:16px;">Системные требования</h2>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div>
                    <label style="font-size:13px; font-weight:600; color:#334155; margin-bottom:6px; display:block;">ОС</label>
                    <?php $os = $product ? json_decode($product['os_support'] ?? '[]', true) : array(); ?>
                    <div style="display:flex; flex-direction:column; gap:4px;">
                        <?php foreach (array('Windows','macOS','Linux','Android','iOS') as $osName): ?>
                            <label style="display:flex; align-items:center; gap:6px; font-size:13px;">
                                <input type="checkbox" name="os[]" value="<?= $osName ?>" <?= in_array($osName, $os) ? 'checked' : '' ?>>
                                <?= $osName ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <div class="form-group"><label>ОС (текст)</label><input type="text" name="req_os" value="<?= e(json_decode($product['requirements'] ?? '{}', true)['os'] ?? '') ?>" class="form-control"></div>
                    <div class="form-group"><label>Процессор</label><input type="text" name="req_cpu" value="<?= e(json_decode($product['requirements'] ?? '{}', true)['cpu'] ?? '') ?>" class="form-control"></div>
                    <div class="form-group"><label>RAM</label><input type="text" name="req_ram" value="<?= e(json_decode($product['requirements'] ?? '{}', true)['ram'] ?? '') ?>" class="form-control"></div>
                    <div class="form-group"><label>Диск</label><input type="text" name="req_disk" value="<?= e(json_decode($product['requirements'] ?? '{}', true)['disk'] ?? '') ?>" class="form-control"></div>
                </div>
            </div>
        </div>

        <div class="card" style="padding:20px; margin-bottom:16px;">
            <h2 style="font-size:16px; font-weight:700; margin-bottom:16px;">Версии</h2>
            <?php if ($versions): ?>
                <table class="table" style="margin-bottom:14px;">
                    <thead><tr><th>Версия</th><th>Дата</th><th>Размер</th><th>Статус</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($versions as $v): ?>
                        <tr>
                            <td class="font-mono"><?= e($v['version']) ?></td>
                            <td><?= e($v['release_date']) ?></td>
                            <td><?= e($v['file_size']) ?></td>
                            <td><?= $v['is_current'] ? '<span class="badge badge-success">Текущая</span>' : '' ?></td>
                            <td style="text-align:right;">
                                    <button type="submit" name="delete_version" value="<?= (int)$v['id'] ?>" class="text-red-600 hover:underline text-sm" onclick="return confirm('Удалить версию?')">Удалить</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="font-size:13px; color:#64748b; margin-bottom:14px;">Версий пока нет</p>
            <?php endif; ?>
            <?php if ($id): ?>
            <button type="button" onclick="document.getElementById('versionModal').classList.add('show')" class="btn btn-secondary btn-sm">+ Добавить версию</button>
            <?php endif; ?>
        </div>

        <div style="display:flex; align-items:center; gap:12px;">
            <button type="submit" name="save_product" class="btn btn-primary">Сохранить продукт</button>
            <a href="<?= url('/admin/products.php') ?>" class="btn btn-secondary">Отмена</a>
            <label style="display:flex; align-items:center; gap:6px; margin-left:auto; font-size:13px; cursor:pointer;">
                <input type="checkbox" name="is_active" <?= ($product['is_active'] ?? 1) ? 'checked' : '' ?>>
                Активен
            </label>
        </div>
    </form>

    <!-- Модалка добавления версии -->
    <div id="versionModal" class="modal">
        <div class="modal-content">
            <h3 style="font-size:18px; font-weight:700; margin-bottom:16px;">Добавить версию</h3>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="add_version" value="1">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group"><label>Версия</label><input type="text" name="version" required class="form-control" placeholder="12.4.1"></div>
                    <div class="form-group"><label>Дата релиза</label><input type="date" name="release_date" required class="form-control" value="<?= date('Y-m-d') ?>"></div>
                    <div class="form-group"><label>Размер файла</label><input type="text" name="file_size" class="form-control" placeholder="184 МБ"></div>
                    <div class="form-group"><label>URL загрузки</label><input type="text" name="download_url" class="form-control"></div>
                </div>
                <div class="form-group"><label>Changelog</label><textarea name="changelog" rows="3" class="form-control"></textarea></div>
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
                    <input type="checkbox" name="is_current" id="is_current">
                    <label for="is_current" style="font-size:13px;">Текущая версия</label>
                </div>
                <div style="display:flex; gap:10px;">
                    <button type="submit" class="btn btn-primary">Добавить</button>
                    <button type="button" onclick="document.getElementById('versionModal').classList.remove('show')" class="btn btn-secondary">Отмена</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php include APP_ROOT . '/admin/templates/footer.php'; ?>
