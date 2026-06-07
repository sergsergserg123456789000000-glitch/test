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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
        $message = 'Ошибка безопасности';
    } else {
        $colorKeys = array('color_primary', 'color_cta', 'color_text', 'color_bg');
        foreach ($colorKeys as $k) {
            if (isset($_POST[$k]) && preg_match('/^#[0-9a-fA-F]{6}$/', trim($_POST[$k]))) {
                saveSetting2($db, $k, trim($_POST[$k]));
            }
        }
        // Тип фона: цвет или изображение
        $bgMode = isset($_POST['site_bg_mode']) && $_POST['site_bg_mode'] === 'image' ? 'image' : 'color';
        saveSetting2($db, 'site_bg_mode', $bgMode);

        // Фоновое изображение
        if (!empty($_FILES['bg_image']['name']) && $_FILES['bg_image']['error'] == UPLOAD_ERR_OK) {
            $allowed = array('jpg','jpeg','png','webp','svg');
            $ext = strtolower(pathinfo($_FILES['bg_image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                if (!is_dir(IMAGES_DIR)) mkdir(IMAGES_DIR, 0755, true);
                $fn = 'site-bg.' . $ext;
                if (move_uploaded_file($_FILES['bg_image']['tmp_name'], IMAGES_DIR . DS . $fn)) {
                    saveSetting2($db, 'site_bg_image', 'images/' . $fn);
                }
            }
        }
        // Прозрачность
        if (isset($_POST['bg_opacity']) && is_numeric($_POST['bg_opacity'])) {
            saveSetting2($db, 'site_bg_opacity', (int)max(0, min(100, (int)$_POST['bg_opacity'])));
        }
        // Удалить фон
        if (isset($_POST['remove_bg'])) {
            saveSetting2($db, 'site_bg_image', '');
        }

        $message = 'Цветовая схема сохранена';
    }
}

$s = getSettingsMap($db);
$primary   = isset($s['color_primary']) ? $s['color_primary'] : '#0056D2';
$cta       = isset($s['color_cta'])     ? $s['color_cta']     : '#FF6B00';
$text      = isset($s['color_text'])    ? $s['color_text']    : '#1A1A1A';
$bg        = isset($s['color_bg'])      ? $s['color_bg']      : '#FFFFFF';
$bgMode    = isset($s['site_bg_mode'])  ? $s['site_bg_mode']  : 'color';
$bgImg     = isset($s['site_bg_image']) ? $s['site_bg_image'] : '';
$bgOp      = isset($s['site_bg_opacity']) ? (int)$s['site_bg_opacity'] : 0;

$page_title = 'Цветовая гамма';
include APP_ROOT . '/admin/templates/header.php';
?>

<style>
.color-row { display:flex; align-items:center; gap:16px; padding:16px; border:1px solid #e2e8f0; border-radius:12px; margin-bottom:12px; }
.color-preview { width:56px; height:56px; border-radius:10px; border:3px solid #fff; box-shadow:0 0 0 1px #e2e8f0; flex-shrink:0; cursor:pointer; }
.color-name { font-weight:700; color:#0f172a; font-size:15px; }
.color-desc { font-size:12px; color:#64748b; margin-top:2px; }
.color-hex { font-family:monospace; width:120px; height:40px; padding:0 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:14px; }
.palette-grid { display:grid; grid-template-columns:repeat(6, 1fr); gap:8px; margin-top:10px; }
.palette-btn { height:36px; border-radius:8px; border:2px solid transparent; cursor:pointer; transition:transform .15s; }
.palette-btn:hover { transform:scale(1.1); border-color:#1e293b; }
.site-preview { border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; }
.bg-section { border:2px solid #e2e8f0; border-radius:14px; padding:20px; margin-bottom:20px; background:#f8fafc; }
.bg-section h3 { font-size:16px; font-weight:700; margin-bottom:16px; color:#0f172a; }
.range-slider { width:100%; accent-color:#2563eb; }
</style>

<div style="margin-bottom:24px;">
    <h1 style="font-size:26px; font-weight:800; color:#0f172a;">Цветовая гамма</h1>
    <div style="color:#64748b; font-size:14px; margin-top:4px;">Выберите цвета или введите HEX-код. Фон сайта можно задать цветом или картинкой.</div>
</div>

<?php if ($message): ?><div class="flash-message flash-success"><?= e($message) ?></div><?php endif; ?>

<div style="display:grid; grid-template-columns:1fr 380px; gap:20px;">
    <div>
        <form method="POST" id="colorForm" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

            <div class="card" style="padding:24px;">
                <?php
                $colorRows = array(
                    array('key'=>'color_primary', 'val'=>$primary, 'name'=>'Основной цвет', 'desc'=>'Кнопки, ссылки, акценты', 'palettes'=>array('#0056D2','#1A57E6','#0369a1','#1d4ed8','#7c3aed','#059669','#dc2626','#0f172a')),
                    array('key'=>'color_cta',     'val'=>$cta,     'name'=>'CTA-кнопки',   'desc'=>'«Купить», «Скачать», призыв к действию', 'palettes'=>array('#FF6B00','#E63946','#f97316','#dc2626','#16a34a','#0891b2','#7c3aed','#d97706')),
                    array('key'=>'color_text',    'val'=>$text,    'name'=>'Цвет текста',   'desc'=>'Основной текст сайта', 'palettes'=>array('#1A1A1A','#0f172a','#1e293b','#374151','#4b5563','#6b7280','#374151','#111827')),
                    array('key'=>'color_bg',      'val'=>$bg,      'name'=>'Цвет фона сайта','desc'=>'Используется, если выбран режим «Фон цветом»', 'palettes'=>array('#FFFFFF','#F8F9FA','#f1f5f9','#fafafa','#f9fafb','#fffbeb','#f0f9ff','#fdf4ff','#f0fdf4','#fef2f2','#f5f3ff','#ecfeff')),
                );
                foreach ($colorRows as $cr):
                ?>
                <div class="color-row">
                    <input type="color" id="picker_<?= $cr['key'] ?>" value="<?= e($cr['val']) ?>"
                        class="color-preview"
                        oninput="document.getElementById('hex_<?= $cr['key'] ?>').value=this.value; updatePreview();"
                        title="Кликните для выбора цвета">
                    <div style="flex:1;">
                        <div class="color-name"><?= e($cr['name']) ?></div>
                        <div class="color-desc"><?= e($cr['desc']) ?></div>
                        <div class="palette-grid">
                            <?php foreach ($cr['palettes'] as $c): ?>
                                <div class="palette-btn" style="background:<?= e($c) ?>;" title="<?= e($c) ?>" onclick="setColor('<?= $cr['key'] ?>','<?= $c ?>')"></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div>
                        <label style="font-size:12px; color:#64748b;">HEX</label>
                        <input type="text" id="hex_<?= $cr['key'] ?>" name="<?= $cr['key'] ?>" value="<?= e($cr['val']) ?>"
                            class="color-hex"
                            maxlength="7"
                            oninput="if(/^#[0-9a-fA-F]{6}$/.test(this.value)){document.getElementById('picker_<?= $cr['key'] ?>').value=this.value; updatePreview();}">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Фон сайта -->
            <div class="bg-section" style="margin-top:16px;">
                <h3>🖼️ Фон сайта</h3>
                <p style="font-size:13px; color:#64748b; margin-bottom:16px;">Выберите режим: сплошной цвет или фоновая картинка с прозрачностью.</p>

                <div style="display:flex; gap:12px; margin-bottom:16px; flex-wrap:wrap;">
                    <label style="display:flex; align-items:center; gap:8px; padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; background:#fff; cursor:pointer;">
                        <input type="radio" name="site_bg_mode" value="color" <?= $bgMode === 'color' ? 'checked' : '' ?>>
                        <span style="font-size:13px; font-weight:700; color:#0f172a;">Фон цветом</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; background:#fff; cursor:pointer;">
                        <input type="radio" name="site_bg_mode" value="image" <?= $bgMode === 'image' ? 'checked' : '' ?>>
                        <span style="font-size:13px; font-weight:700; color:#0f172a;">Фон картинкой</span>
                    </label>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <div class="form-group">
                            <label>Фоновое изображение</label>
                            <?php if ($bgImg): ?>
                                <div style="margin-bottom:10px; display:flex; align-items:center; gap:12px;">
                                    <img src="<?= url('/' . $bgImg) ?>?v=<?= time() ?>" alt="Текущий фон" style="width:120px; height:68px; object-fit:cover; border-radius:8px; border:1px solid #e2e8f0;">
                                    <div style="font-size:12px; color:#64748b;"><?= e($bgImg) ?></div>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="bg_image" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg,image/*">
                            <div style="font-size:12px; color:#64748b; margin-top:6px;">Форматы: jpg, png, webp, svg</div>
                        </div>
                        <?php if ($bgImg): ?>
                        <div class="form-group" style="margin-top:10px;">
                            <button type="submit" name="remove_bg" value="1" class="btn btn-secondary btn-sm">Удалить фоновое изображение</button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="form-group">
                            <label>Прозрачность изображения: <span id="opVal"><?= $bgOp ?>%</span></label>
                            <input type="range" name="bg_opacity" min="0" max="100" value="<?= $bgOp ?>"
                                class="range-slider"
                                oninput="document.getElementById('opVal').textContent=this.value+'%'; updateBgPreview();">
                            <div style="display:flex; justify-content:space-between; font-size:11px; color:#94a3b8;">
                                <span>0 (не видно)</span>
                                <span>100 (полная видимость)</span>
                            </div>
                        </div>
                        <div style="border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; height:80px; background:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; color:#94a3b8;">
                            <span id="bgSample" style="display:flex; align-items:center; justify-content:center; width:100%; height:100%;">
                                Белый фон + изображение
                            </span>
                        </div>
                        <div style="font-size:12px; color:#64748b; margin-top:6px;">
                            Если выбран режим «Фон картинкой», картинка накладывается поверх белого/выбранного цвета с заданной прозрачностью.
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top:20px;">
                <button type="submit" class="btn btn-primary">Сохранить цвета</button>
                <button type="button" onclick="resetDefaults()" class="btn btn-secondary">Сбросить по умолчанию</button>
            </div>
        </form>
    </div>

    <div>
        <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:10px;">Предпросмотр</div>
        <div class="site-preview" id="preview" style="background:#FFFFFF; padding:20px; position:relative;">
            <div id="previewBg" style="position:absolute; inset:0; pointer-events:none;"></div>
            <div style="position:relative; z-index:1;">
                <div style="background:<?= e($primary) ?>; padding:12px 16px; border-radius:8px; color:#fff; font-size:14px; font-weight:700; margin-bottom:12px;">Шапка сайта</div>
                <div style="font-size:20px; font-weight:800; color:<?= e($text) ?>; margin-bottom:6px;">Заголовок страницы</div>
                <div style="font-size:13px; color:<?= e($text) ?>; opacity:0.7; margin-bottom:14px;">Основной текст сайта.</div>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button style="background:<?= e($primary) ?>; color:#fff; border:none; padding:10px 18px; border-radius:8px; font-weight:700; cursor:pointer; font-size:13px;">Основная кнопка</button>
                    <button style="background:<?= e($cta) ?>; color:#fff; border:none; padding:10px 18px; border-radius:8px; font-weight:700; cursor:pointer; font-size:13px;">CTA Купить</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setColor(key, hex) {
    document.getElementById('picker_' + key).value = hex;
    document.getElementById('hex_' + key).value = hex;
    updatePreview();
}
function updatePreview() {
    var p = document.getElementById('hex_color_primary').value;
    var c = document.getElementById('hex_color_cta').value;
    var t = document.getElementById('hex_color_text').value;
    var el = document.getElementById('preview');
    if (!el) return;
    el.innerHTML = '<div id="previewBg" style="position:absolute;inset:0;pointer-events:none;"></div><div style="position:relative;z-index:1;">' +
        '<div style="background:'+p+';padding:12px 16px;border-radius:8px;color:#fff;font-size:14px;font-weight:700;margin-bottom:12px;">Шапка сайта</div>' +
        '<div style="font-size:20px;font-weight:800;color:'+t+';margin-bottom:6px;">Заголовок страницы</div>' +
        '<div style="font-size:13px;color:'+t+';opacity:0.7;margin-bottom:14px;">Основной текст сайта.</div>' +
        '<div style="display:flex;gap:8px;flex-wrap:wrap;"><button style="background:'+p+';color:#fff;border:none;padding:10px 18px;border-radius:8px;font-weight:700;font-size:13px;">Основная кнопка</button>' +
        '<button style="background:'+c+';color:#fff;border:none;padding:10px 18px;border-radius:8px;font-weight:700;font-size:13px;">CTA Купить</button></div></div>';
}
function resetDefaults() {
    setColor('color_primary', '#0056D2');
    setColor('color_cta',     '#FF6B00');
    setColor('color_text',    '#1A1A1A');
}
</script>

<?php include APP_ROOT . '/admin/templates/footer.php'; ?>
