<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title ?? 'Админ-панель') ?> | <?= SITE_NAME ?></title>
    <link rel="icon" href="<?= url('/site-assets.php?type=favicon') ?>">
    <link rel="shortcut icon" href="<?= url('/site-assets.php?type=favicon') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --color-brand: <?= e(getSetting('color_primary', '#0056D2')) ?>; --color-cta: <?= e(getSetting('color_cta', '#FF6B00')) ?>; }
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }
        .admin-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #0f172a; color: #94a3b8; flex-shrink: 0; position: fixed; height: 100vh; overflow-y: auto; }
        .sidebar-header { height: 64px; display: flex; align-items: center; gap: 10px; padding: 0 20px; border-bottom: 1px solid #1e293b; }
        .sidebar-logo { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; overflow:hidden; }
        .sidebar-logo img { width:100%; height:100%; object-fit:contain; background:#fff; border-radius:10px; }
        .sidebar-title { color: white; font-size: 14px; font-weight: 800; }
        .sidebar-subtitle { color: #64748b; font-size: 9px; letter-spacing: 2px; }
        .sidebar-nav { padding: 16px; }
        .nav-section { margin-bottom: 20px; }
        .nav-section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #475569; padding: 0 12px; margin-bottom: 8px; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; color: #94a3b8; text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.2s; }
        .nav-item:hover { background: #1e293b; color: white; }
        .nav-item.active { background: #2563eb; color: white; }
        .nav-sub-item { font-size: 12px; padding: 8px 12px; }
        .nav-sub-item.active { background: #1e40af; color: white; }
        .nav-icon { width: 18px; height: 18px; }
        .sidebar-footer { padding: 12px; border-top: 1px solid #1e293b; position: sticky; bottom: 0; background: #0f172a; }
        .user-info { display: flex; align-items: center; gap: 10px; padding: 8px; background: #1e293b; border-radius: 8px; }
        .user-avatar { width: 32px; height: 32px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 12px; }
        .user-details { flex: 1; min-width: 0; }
        .user-name { color: white; font-size: 12px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { color: #64748b; font-size: 10px; }
        .logout-btn { color: #64748b; font-size: 12px; text-decoration: none; }
        .logout-btn:hover { color: #ef4444; }
        .main-content { flex: 1; min-width: 0; margin-left: 260px; }
        .topbar { height: 64px; background: white; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 16px; padding: 0 24px; position: sticky; top: 0; z-index: 100; }
        .search-box { flex: 1; max-width: 400px; position: relative; }
        .search-input { width: 100%; height: 36px; padding: 0 12px 0 36px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f1f5f9; font-size: 13px; }
        .search-input:focus { outline: none; border-color: #2563eb; background: white; }
        .search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #94a3b8; }
        .topbar-actions { display: flex; align-items: center; gap: 8px; }
        .topbar-btn { width: 36px; height: 36px; border-radius: 8px; border: none; background: transparent; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .topbar-btn:hover { background: #f1f5f9; }
        .notification-dot { position: absolute; top: 6px; right: 6px; width: 8px; height: 8px; background: #f97316; border-radius: 50%; }
        .content-area { padding: 24px; }
        .flash-message { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; font-weight: 500; }
        .flash-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .flash-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
        .card { background: white; border-radius: 16px; border: 1px solid #e2e8f0; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
        .table th { background: #f8fafc; font-weight: 600; color: #475569; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-sm { padding: 6px 10px; font-size: 12px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; }
        .form-control:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.15); }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #854d0e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal.show { display: flex; }
        .modal-content { background: white; border-radius: 16px; padding: 24px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="<?= url('/site-assets.php?type=logo') ?>" alt="<?= SITE_NAME ?>">
                </div>
                <div>
                    <div class="sidebar-title">PROFESSIONAL</div>
                    <div class="sidebar-subtitle">ADMIN</div>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Основное</div>
                    <a href="<?= url('/admin/index.php') ?>" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
                        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Дашборд
                    </a>
                    <?php
                    $prodPages = array('products.php','categories.php');
                    $isProdActive = in_array(basename($_SERVER['PHP_SELF']), $prodPages);
                    ?>
                    <div class="nav-group">
                        <button onclick="toggleProdMenu()" class="nav-item nav-item-toggle <?= $isProdActive ? 'active' : '' ?>" style="width:100%; cursor:pointer; background:none; border:none; text-align:left;">
                            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Продукты
                            <svg id="prod-chevron" style="margin-left:auto; width:14px; height:14px; transition:transform .2s; transform:<?= $isProdActive ? 'rotate(90deg)' : 'rotate(0deg)' ?>;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <div id="prod-submenu" style="padding-left:14px; <?= $isProdActive ? '' : 'display:none;' ?>">
                            <a href="<?= url('/admin/products.php') ?>" class="nav-item nav-sub-item <?= basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : '' ?>">Продукты</a>
                            <a href="<?= url('/admin/categories.php') ?>" class="nav-item nav-sub-item <?= basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : '' ?>">Категории</a>
                        </div>
                    </div>
                    <script>
                    function toggleProdMenu(){
                        var m=document.getElementById('prod-submenu');
                        var c=document.getElementById('prod-chevron');
                        if(m.style.display==='none'){m.style.display='block';c.style.transform='rotate(90deg)';}
                        else{m.style.display='none';c.style.transform='rotate(0deg)';}
                    }
                    </script>
                    <a href="<?= url('/admin/orders.php') ?>" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : '' ?>">
                        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Заказы
                    </a>
                    <a href="<?= url('/admin/licenses.php') ?>" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'licenses.php' ? 'active' : '' ?>">
                        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        Лицензии
                    </a>
                    <a href="<?= url('/admin/blog.php') ?>" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'blog.php' ? 'active' : '' ?>">
                        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        Блог
                    </a>
                    <a href="<?= url('/admin/testimonials.php') ?>" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'testimonials.php' ? 'active' : '' ?>">
                        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m-6 8l-4-4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12z"/></svg>
                        Отзывы
                    </a>
                    <a href="<?= url('/admin/tickets.php') ?>" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'tickets.php' ? 'active' : '' ?>">
                        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Тикеты
                    </a>
                    <a href="<?= url('/admin/users.php') ?>" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '' ?>">
                        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Пользователи
                    </a>
                    <a href="<?= url('/admin/visitors.php') ?>" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'visitors.php' ? 'active' : '' ?>">
                        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Посетители
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Настройки</div>

                    <?php
                    $settingsPages = array('settings.php','settings-security.php','settings-oauth.php','settings-colors.php','settings-blocks.php');
                    $isSettingsActive = in_array(basename($_SERVER['PHP_SELF']), $settingsPages);
                    ?>
                    <div class="nav-group">
                        <button onclick="toggleSettingsMenu()" class="nav-item nav-item-toggle <?= $isSettingsActive ? 'active' : '' ?>" style="width:100%; cursor:pointer; background:none; border:none; text-align:left;">
                            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Настройки сайта
                            <svg id="settings-chevron" style="margin-left:auto; width:14px; height:14px; transition:transform .2s; transform:<?= $isSettingsActive ? 'rotate(90deg)' : 'rotate(0deg)' ?>;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>

                        <div id="settings-submenu" style="padding-left:14px; <?= $isSettingsActive ? '' : 'display:none;' ?>">
                            <a href="<?= url('/admin/settings.php') ?>" class="nav-item nav-sub-item <?= basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : '' ?>">
                                Основные
                            </a>
                            <a href="<?= url('/admin/settings-security.php') ?>" class="nav-item nav-sub-item <?= basename($_SERVER['PHP_SELF']) === 'settings-security.php' ? 'active' : '' ?>">
                                Безопасность
                            </a>
                            <a href="<?= url('/admin/settings-oauth.php') ?>" class="nav-item nav-sub-item <?= basename($_SERVER['PHP_SELF']) === 'settings-oauth.php' ? 'active' : '' ?>">
                                Соцсети (OAuth)
                            </a>
                            <a href="<?= url('/admin/settings-colors.php') ?>" class="nav-item nav-sub-item <?= basename($_SERVER['PHP_SELF']) === 'settings-colors.php' ? 'active' : '' ?>">
                                Цветовая гамма
                            </a>
                            <a href="<?= url('/admin/settings-blocks.php') ?>" class="nav-item nav-sub-item <?= basename($_SERVER['PHP_SELF']) === 'settings-blocks.php' ? 'active' : '' ?>">
                                Блоки главной
                            </a>
                        </div>
                    </div>
                </div>
                <script>
                function toggleSettingsMenu(){
                    var m=document.getElementById('settings-submenu');
                    var c=document.getElementById('settings-chevron');
                    if(m.style.display==='none'){m.style.display='block';c.style.transform='rotate(90deg)';}
                    else{m.style.display='none';c.style.transform='rotate(0deg)';}
                }
                </script>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar"><?= mb_substr($admin_name ?? 'A', 0, 1) ?></div>
                    <div class="user-details">
                        <div class="user-name"><?= e($admin_name ?? 'Админ') ?></div>
                        <div class="user-role"><?= e($admin_role ?? 'admin') ?></div>
                    </div>
                    <a href="<?= url('/admin/logout.php') ?>" class="logout-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                        </svg>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <header class="topbar">
                <div class="search-box">
                    <svg class="search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" class="search-input" placeholder="Поиск...">
                </div>
                <div class="topbar-actions">
                    <button class="topbar-btn" style="position: relative;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
                        <span class="notification-dot"></span>
                    </button>
                    <a href="<?= url('/') ?>" target="_blank" class="topbar-btn" title="На сайт">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3"/></svg>
                    </a>
                </div>
            </header>

            <main class="content-area">
                <?php
                $flash = getFlashMessage();
                if ($flash['message']):
                ?>
                    <div class="flash-message flash-<?= $flash['type'] ?>">
                        <?= e($flash['message']) ?>
                    </div>
                <?php endif; ?>
