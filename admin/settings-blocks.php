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
        $blockKeys = array('hero','trusted','products','features','solutions','testimonials','map','cta','blog');
        // Сначала выключаем все
        foreach ($blockKeys as $bk) {
            saveSetting2($db, 'block_' . $bk . '_enabled', '0');
        }
        // Включаем отмеченные
        foreach ($_POST as $k => $v) {
            if (strpos($k, 'block_') === 0 && strpos($k, '_enabled') !== false) {
                saveSetting2($db, $k, '1');
            }
        }
        // Сохраняем контент
        $contentKeys = array(
            'hero_badge','hero_title1','hero_title2','hero_subtitle','hero_btn1','hero_btn2','hero_stat1','hero_stat2','hero_reviews',
            'trusted_title','trusted_logos',
            'cta_title','cta_subtitle','cta_btn1','cta_btn2',
            'products_label','products_title','products_subtitle',
            'features_label','features_title','features_subtitle',
            'solutions_label','solutions_title','solutions_subtitle',
            'testimonials_label','testimonials_title',
            'map_label','map_title','map_subtitle',
            'blog_label','blog_title',
        );
        foreach ($contentKeys as $ck) {
            if (isset($_POST[$ck])) {
                saveSetting2($db, $ck, trim($_POST[$ck]));
            }
        }
        $message = 'Блоки главной страницы обновлены';
    }
}

$s = getSettingsMap($db);

// Дефолты
function sv($s, $key, $default = '') {
    return isset($s[$key]) ? $s[$key] : $default;
}

$blocks = array(
    'hero'         => array('label' => 'Hero-блок (главный заголовок)', 'icon' => '🏠', 'desc' => 'Заголовок, подзаголовок, кнопки и статистика'),
    'trusted'      => array('label' => 'Нам доверяют',                  'icon' => '🤝', 'desc' => 'Логотипы компаний-клиентов'),
    'products'     => array('label' => 'Наши продукты',                 'icon' => '📦', 'desc' => 'Каталог из 6 продуктов'),
    'features'     => array('label' => 'Почему мы / Преимущества',      'icon' => '⭐', 'desc' => 'AI-защита, Облако, Скорость, Приватность'),
    'solutions'    => array('label' => 'Отраслевые решения',            'icon' => '🏢', 'desc' => 'Банки, Медицина, Образование и др.'),
    'testimonials' => array('label' => 'Отзывы клиентов',              'icon' => '💬', 'desc' => '3 отзыва от реальных клиентов'),
    'map'          => array('label' => 'Карта офиса',                   'icon' => '📍', 'desc' => 'Контакты и карта с меткой офиса'),
    'cta'          => array('label' => 'CTA (призыв к действию)',        'icon' => '🎯', 'desc' => 'Блок «Попробуйте бесплатно 30 дней»'),
    'blog'         => array('label' => 'Последние статьи блога',        'icon' => '📰', 'desc' => '3 последних статьи из блога'),
);

$page_title = 'Блоки главной страницы';
include APP_ROOT . '/admin/templates/header.php';
?>

<style>
.block-card { border:2px solid #e2e8f0; border-radius:14px; margin-bottom:12px; overflow:hidden; }
.block-card.on { border-color:#86efac; }
.block-header { display:flex; align-items:center; gap:12px; padding:16px 20px; cursor:pointer; }
.block-body { padding:16px 20px; border-top:1px solid #e2e8f0; background:#f8fafc; display:none; }
.block-body.open { display:block; }
.toggle-switch { position:relative; width:48px; height:26px; flex-shrink:0; }
.toggle-switch input { opacity:0; width:0; height:0; }
.toggle-slider { position:absolute; inset:0; border-radius:26px; background:#cbd5e1; cursor:pointer; transition:.3s; }
.toggle-slider:before { content:''; position:absolute; width:20px; height:20px; left:3px; top:3px; border-radius:50%; background:#fff; transition:.3s; }
.toggle-switch input:checked + .toggle-slider { background:#16a34a; }
.toggle-switch input:checked + .toggle-slider:before { transform:translateX(22px); }
</style>

<div style="margin-bottom:24px;">
    <h1 style="font-size:26px; font-weight:800; color:#0f172a;">Блоки главной страницы</h1>
    <div style="color:#64748b; font-size:14px; margin-top:4px;">Включайте/выключайте и редактируйте каждый блок. Нажмите на блок для редактирования содержимого.</div>
</div>

<?php if ($message): ?><div class="flash-message flash-success"><?= e($message) ?></div><?php endif; ?>

<form method="POST" id="blocksForm">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

    <?php foreach ($blocks as $bk => $bi):
        $enabledKey = 'block_' . $bk . '_enabled';
        $isOn = sv($s, $enabledKey, '1') === '1';
        $hasContent = true;
    ?>
    <div class="block-card <?= $isOn ? 'on' : '' ?>" id="bc_<?= $bk ?>">
        <div class="block-header" onclick="toggleBlockBody('<?= $bk ?>')">
            <span style="font-size:24px;"><?= $bi['icon'] ?></span>
            <div style="flex:1; min-width:0;">
                <div style="font-weight:700; color:#0f172a; font-size:15px;"><?= e($bi['label']) ?></div>
                <div style="font-size:12px; color:#64748b; margin-top:2px;"><?= e($bi['desc']) ?></div>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <?php if ($isOn): ?>
                    <span class="badge badge-success" id="badge_<?= $bk ?>">Показывается</span>
                <?php else: ?>
                    <span class="badge" style="background:#fee2e2; color:#991b1b; border-radius:9999px; padding:2px 8px; font-size:11px; font-weight:600;" id="badge_<?= $bk ?>">Скрыт</span>
                <?php endif; ?>
                <label class="toggle-switch" onclick="event.stopPropagation();">
                    <input type="checkbox" name="block_<?= $bk ?>_enabled" value="1" <?= $isOn ? 'checked' : '' ?> onchange="toggleBlockEnabled('<?= $bk ?>', this.checked)">
                    <span class="toggle-slider"></span>
                </label>
                <?php if ($hasContent): ?>
                    <svg style="width:16px; height:16px; color:#94a3b8;" id="arr_<?= $bk ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($bk === 'hero'): ?>
        <div class="block-body" id="body_<?= $bk ?>">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div class="form-group">
                    <label>Бейдж (маленькая метка)</label>
                    <input type="text" name="hero_badge" value="<?= e(sv($s, 'hero_badge', 'Версия 12.4 с обновлённым AI-движком')) ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label>Кнопка 1 (синяя)</label>
                    <input type="text" name="hero_btn1" value="<?= e(sv($s, 'hero_btn1', 'Смотреть продукты')) ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label>Заголовок строка 1</label>
                    <input type="text" name="hero_title1" value="<?= e(sv($s, 'hero_title1', 'Профессиональное ПО')) ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label>Кнопка 2 (белая)</label>
                    <input type="text" name="hero_btn2" value="<?= e(sv($s, 'hero_btn2', 'Сравнить версии')) ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label>Заголовок строка 2 (цветная)</label>
                    <input type="text" name="hero_title2" value="<?= e(sv($s, 'hero_title2', 'для вашего бизнеса')) ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label>Статистика 1 (крупная)</label>
                    <input type="text" name="hero_stat1" value="<?= e(sv($s, 'hero_stat1', '2.5M+')) ?>" class="form-control">
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Подзаголовок</label>
                    <textarea name="hero_subtitle" rows="2" class="form-control"><?= e(sv($s, 'hero_subtitle', 'Российский разработчик антивирусов...')) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Статистика 2</label>
                    <input type="text" name="hero_stat2" value="<?= e(sv($s, 'hero_stat2', '15 лет')) ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label>Количество отзывов</label>
                    <input type="text" name="hero_reviews" value="<?= e(sv($s, 'hero_reviews', '2 847 отзывов')) ?>" class="form-control">
                </div>
            </div>
        </div>
        <?php elseif ($bk === 'trusted'): ?>
        <div class="block-body" id="body_<?= $bk ?>">
            <div class="form-group">
                <label>Заголовок блока</label>
                <input type="text" name="trusted_title" value="<?= e(sv($s, 'trusted_title', 'Нам доверяют компании из разных отраслей')) ?>" class="form-control">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Логотипы / названия компаний (через запятую)</label>
                <input type="text" name="trusted_logos" value="<?= e(sv($s, 'trusted_logos', 'Альфа-Банк, СберМаркет, Тинькофф, М.Видео, DNS, Ситилинк, ВкусВилл, Ozon')) ?>" class="form-control">
                <div style="font-size:12px; color:#64748b; margin-top:6px;">Введите через запятую. Отображается до 8 элементов.</div>
            </div>
        </div>
        <?php elseif ($bk === 'cta'): ?>
        <div class="block-body" id="body_<?= $bk ?>">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div class="form-group">
                    <label>Заголовок</label>
                    <input type="text" name="cta_title" value="<?= e(sv($s, 'cta_title', 'Попробуйте бесплатно 30 дней')) ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label>Кнопка 1 (оранжевая)</label>
                    <input type="text" name="cta_btn1" value="<?= e(sv($s, 'cta_btn1', 'Скачать бесплатно')) ?>" class="form-control">
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Подзаголовок</label>
                    <textarea name="cta_subtitle" rows="2" class="form-control"><?= e(sv($s, 'cta_subtitle', 'Полный функционал всех продуктов, без ограничений.')) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Кнопка 2 (белая)</label>
                    <input type="text" name="cta_btn2" value="<?= e(sv($s, 'cta_btn2', 'Связаться с нами')) ?>" class="form-control">
                </div>
            </div>
        </div>
        <?php elseif ($bk === 'products'): ?>
        <div class="block-body" id="body_<?= $bk ?>">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div class="form-group"><label>Метка (мелкий текст сверху)</label><input type="text" name="products_label" value="<?= e(sv($s,'products_label','Наши продукты')) ?>" class="form-control"></div>
                <div class="form-group"><label>Заголовок</label><input type="text" name="products_title" value="<?= e(sv($s,'products_title','Решения для любых задач')) ?>" class="form-control"></div>
                <div class="form-group" style="grid-column:1/-1;"><label>Подзаголовок</label><textarea name="products_subtitle" rows="2" class="form-control"><?= e(sv($s,'products_subtitle','')) ?></textarea></div>
            </div>
            <div style="font-size:12px; color:#64748b; margin-top:6px;">Сами карточки продуктов редактируются в разделе <a href="<?= url('/admin/products.php') ?>" style="color:#2563eb;">Продукты</a>.</div>
        </div>
        <?php elseif ($bk === 'features'): ?>
        <div class="block-body" id="body_<?= $bk ?>">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div class="form-group"><label>Метка</label><input type="text" name="features_label" value="<?= e(sv($s,'features_label','Почему мы')) ?>" class="form-control"></div>
                <div class="form-group"><label>Заголовок</label><input type="text" name="features_title" value="<?= e(sv($s,'features_title','Технологии, которым доверяют')) ?>" class="form-control"></div>
                <div class="form-group" style="grid-column:1/-1;"><label>Подзаголовок</label><textarea name="features_subtitle" rows="2" class="form-control"><?= e(sv($s,'features_subtitle','')) ?></textarea></div>
            </div>
        </div>
        <?php elseif ($bk === 'solutions'): ?>
        <div class="block-body" id="body_<?= $bk ?>">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div class="form-group"><label>Метка</label><input type="text" name="solutions_label" value="<?= e(sv($s,'solutions_label','Отраслевые решения')) ?>" class="form-control"></div>
                <div class="form-group"><label>Заголовок</label><input type="text" name="solutions_title" value="<?= e(sv($s,'solutions_title','Подходим под вашу индустрию')) ?>" class="form-control"></div>
                <div class="form-group" style="grid-column:1/-1;"><label>Подзаголовок</label><textarea name="solutions_subtitle" rows="2" class="form-control"><?= e(sv($s,'solutions_subtitle','')) ?></textarea></div>
            </div>
        </div>
        <?php elseif ($bk === 'testimonials'): ?>
        <div class="block-body" id="body_<?= $bk ?>">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div class="form-group"><label>Метка</label><input type="text" name="testimonials_label" value="<?= e(sv($s,'testimonials_label','Отзывы')) ?>" class="form-control"></div>
                <div class="form-group"><label>Заголовок</label><input type="text" name="testimonials_title" value="<?= e(sv($s,'testimonials_title','Нам доверяют тысячи')) ?>" class="form-control"></div>
            </div>
        </div>
        <?php elseif ($bk === 'map'): ?>
        <div class="block-body" id="body_<?= $bk ?>">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div class="form-group"><label>Метка</label><input type="text" name="map_label" value="<?= e(sv($s,'map_label','Офис компании')) ?>" class="form-control"></div>
                <div class="form-group"><label>Заголовок</label><input type="text" name="map_title" value="<?= e(sv($s,'map_title','Приезжайте в гости')) ?>" class="form-control"></div>
                <div class="form-group" style="grid-column:1/-1;"><label>Подзаголовок</label><textarea name="map_subtitle" rows="2" class="form-control"><?= e(sv($s,'map_subtitle','')) ?></textarea></div>
            </div>
            <div style="font-size:12px; color:#64748b; margin-top:6px;">Адрес и контакты редактируются в разделе <a href="<?= url('/admin/settings.php') ?>" style="color:#2563eb;">Основные настройки</a>.</div>
        </div>
        <?php elseif ($bk === 'blog'): ?>
        <div class="block-body" id="body_<?= $bk ?>">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div class="form-group"><label>Метка</label><input type="text" name="blog_label" value="<?= e(sv($s,'blog_label','Блог')) ?>" class="form-control"></div>
                <div class="form-group"><label>Заголовок</label><input type="text" name="blog_title" value="<?= e(sv($s,'blog_title','Последние статьи')) ?>" class="form-control"></div>
            </div>
            <div style="font-size:12px; color:#64748b; margin-top:6px;">Статьи редактируются в разделе <a href="<?= url('/admin/blog.php') ?>" style="color:#2563eb;">Блог</a>.</div>
        </div>
        <?php else: ?>
        <div class="block-body" id="body_<?= $bk ?>">
            <div style="color:#64748b; font-size:13px; padding:8px 0;">Этот блок генерируется автоматически.</div>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <div style="margin-top:16px; display:flex; gap:10px; align-items:center;">
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <div style="font-size:13px; color:#64748b;">Изменения применятся после сохранения и обновления страницы сайта</div>
    </div>
</form>

<script>
function toggleBlockEnabled(key, isOn) {
    var card = document.getElementById('bc_' + key);
    var badge = document.getElementById('badge_' + key);
    if (isOn) {
        card.className = card.className.replace(' on','') + ' on';
        badge.style.background = '#dcfce7';
        badge.style.color = '#166534';
        badge.textContent = 'Показывается';
    } else {
        card.className = card.className.replace(' on','');
        badge.style.background = '#fee2e2';
        badge.style.color = '#991b1b';
        badge.textContent = 'Скрыт';
    }
}
function toggleBlockBody(key) {
    var body = document.getElementById('body_' + key);
    var arr  = document.getElementById('arr_' + key);
    if (!body) return;
    var isOpen = body.classList.contains('open');
    if (isOpen) {
        body.classList.remove('open');
        if (arr) arr.style.transform = 'rotate(0deg)';
    } else {
        body.classList.add('open');
        if (arr) arr.style.transform = 'rotate(180deg)';
    }
}
</script>

<?php include APP_ROOT . '/admin/templates/footer.php'; ?>
