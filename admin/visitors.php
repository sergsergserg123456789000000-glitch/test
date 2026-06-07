<?php
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
requireAdmin();

$db = db();
$admin_name = $_SESSION['admin_name'] ?? 'Администратор';
$admin_role = $_SESSION['admin_role'] ?? 'admin';

// Фильтр по периоду
$period = $_GET['period'] ?? 'today';
$periods = [
    'today' => "date(created_at) = date('now', 'localtime')",
    'yesterday' => "date(created_at) = date('now', '-1 day', 'localtime')",
    'week' => "date(created_at) >= date('now', '-7 days', 'localtime')",
    'month' => "date(created_at) >= date('now', '-30 days', 'localtime')",
    'all' => "1=1"
];
$periodLabels = [
    'today' => 'Сегодня',
    'yesterday' => 'Вчера',
    'week' => 'За 7 дней',
    'month' => 'За 30 дней',
    'all' => 'За всё время'
];
$whereClause = $periods[$period] ?? $periods['today'];

// Статистика
$stats = [];
$stats['visitors'] = (int)$db->query("SELECT COUNT(*) FROM visitors WHERE $whereClause")->fetchColumn();
$stats['unique'] = (int)$db->query("SELECT COUNT(DISTINCT visitor_id) FROM visitors WHERE $whereClause")->fetchColumn();
$stats['pageviews'] = (int)$db->query("SELECT COALESCE(SUM(pages_count), 0) FROM visitors WHERE $whereClause")->fetchColumn();
$stats['avg_duration'] = (int)$db->query("SELECT COALESCE(AVG(duration_seconds), 0) FROM visitors WHERE $whereClause")->fetchColumn();
$stats['countries'] = (int)$db->query("SELECT COUNT(DISTINCT country) FROM visitors WHERE $whereClause AND country IS NOT NULL AND country != ''")->fetchColumn();
$stats['online'] = (int)$db->query("SELECT COUNT(*) FROM visitors WHERE datetime(last_activity) > datetime('now', '-5 minutes', 'localtime')")->fetchColumn();

// Топ стран
$topCountries = $db->query("SELECT country, COUNT(*) as cnt FROM visitors WHERE $whereClause AND country IS NOT NULL AND country != '' GROUP BY country ORDER BY cnt DESC LIMIT 5")->fetchAll();

// Топ источников
$topSources = $db->query("SELECT source, COUNT(*) as cnt FROM visitors WHERE $whereClause AND source IS NOT NULL AND source != '' GROUP BY source ORDER BY cnt DESC LIMIT 5")->fetchAll();

// Топ устройств
$topDevices = $db->query("SELECT device_type, COUNT(*) as cnt FROM visitors WHERE $whereClause GROUP BY device_type ORDER BY cnt DESC")->fetchAll();

// Список посетителей
$page = max(1, (int)($_GET['p'] ?? 1));
$perPage = 50;
$offset = ($page - 1) * $perPage;
$totalRows = (int)$db->query("SELECT COUNT(*) FROM visitors WHERE $whereClause")->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

$visitors = $db->query("SELECT * FROM visitors WHERE $whereClause ORDER BY created_at DESC LIMIT $perPage OFFSET $offset")->fetchAll();

function formatDuration($seconds) {
    if ($seconds < 60) return $seconds . ' сек';
    if ($seconds < 3600) return floor($seconds / 60) . ' мин ' . ($seconds % 60) . ' сек';
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    return $h . ' ч ' . $m . ' мин';
}

function flagEmoji($code) {
    if (!$code || strlen($code) !== 2) return '🌐';
    $code = strtoupper($code);
    return mb_chr(0x1F1E6 + ord($code[0]) - ord('A'), 'UTF-8') . mb_chr(0x1F1E6 + ord($code[1]) - ord('A'), 'UTF-8');
}

function isOnline($lastActivity) {
    return strtotime($lastActivity) > time() - 300; // 5 минут
}

$page_title = 'Посетители';
include APP_ROOT . '/admin/templates/header.php';
?>

<style>
.visitors-stats { display:grid; grid-template-columns: repeat(6, 1fr); gap:16px; margin-bottom:24px; }
@media (max-width: 1200px){ .visitors-stats { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 640px){ .visitors-stats { grid-template-columns: repeat(2, 1fr); } }
.v-stat { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:16px; }
.v-stat-value { font-size:24px; font-weight:800; color:#0f172a; line-height:1; margin-bottom:6px; }
.v-stat-label { font-size:12px; color:#64748b; }
.v-stat.online .v-stat-value { color:#16a34a; }
.v-stat.online .v-stat-value::before { content:'●'; color:#16a34a; margin-right:6px; animation: pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:0.4;} }

.tabs { display:flex; gap:6px; margin-bottom:24px; flex-wrap:wrap; }
.tab { padding:8px 16px; border-radius:8px; border:1px solid #e2e8f0; background:#fff; color:#475569; text-decoration:none; font-size:13px; font-weight:600; }
.tab.active { background:#2563eb; color:#fff; border-color:#2563eb; }
.tab:hover:not(.active) { background:#f1f5f9; }

.top-grid { display:grid; grid-template-columns: repeat(3, 1fr); gap:16px; margin-bottom:24px; }
@media (max-width: 1024px){ .top-grid { grid-template-columns: 1fr; } }

.top-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; }
.top-card h3 { font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; }
.top-item { display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f1f5f9; font-size:13px; }
.top-item:last-child { border-bottom:none; }
.top-name { color:#334155; }
.top-count { color:#2563eb; font-weight:700; }

.visitor-row { display:flex; align-items:center; gap:12px; padding:14px 20px; border-bottom:1px solid #eef2f7; font-size:13px; }
.visitor-row:hover { background:#fafcff; }
.visitor-status { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.visitor-status.online { background:#16a34a; box-shadow:0 0 0 3px rgba(22,163,74,0.2); }
.visitor-status.offline { background:#cbd5e1; }
.visitor-geo { min-width:180px; flex-shrink:0; }
.visitor-geo-flag { font-size:18px; margin-right:6px; }
.visitor-geo-country { font-weight:700; color:#0f172a; }
.visitor-geo-city { color:#64748b; font-size:12px; }
.visitor-tech { min-width:140px; flex-shrink:0; font-size:12px; color:#475569; }
.visitor-tech strong { color:#0f172a; }
.visitor-source { min-width:140px; flex-shrink:0; color:#475569; font-size:12px; }
.visitor-pages { min-width:80px; text-align:center; flex-shrink:0; }
.visitor-pages-count { font-size:18px; font-weight:800; color:#2563eb; }
.visitor-pages-label { font-size:10px; color:#64748b; text-transform:uppercase; }
.visitor-duration { min-width:120px; flex-shrink:0; color:#0f172a; font-weight:600; font-size:12px; }
.visitor-time { min-width:120px; flex-shrink:0; color:#64748b; font-size:12px; }
.visitor-ip { color:#64748b; font-family:monospace; font-size:11px; }

.empty-state { padding:40px; text-align:center; color:#64748b; }

.pagination { display:flex; justify-content:center; gap:6px; padding:20px; }
.pagination a, .pagination span { padding:6px 12px; border:1px solid #e2e8f0; border-radius:6px; text-decoration:none; color:#475569; font-size:13px; }
.pagination .active { background:#2563eb; color:#fff; border-color:#2563eb; }
</style>

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
    <div>
        <h1 style="font-size:28px; font-weight:800; color:#0f172a;">Посетители</h1>
        <div style="margin-top:4px; color:#64748b; font-size:14px;">Аналитика трафика, география, источники переходов</div>
    </div>
    <div style="display:flex; gap:8px;">
        <a href="?period=<?= $period ?>" class="btn btn-secondary" onclick="location.reload(); return false;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Обновить
        </a>
    </div>
</div>

<div class="tabs">
    <?php foreach ($periodLabels as $key => $label): ?>
        <a href="?period=<?= $key ?>" class="tab <?= $period === $key ? 'active' : '' ?>"><?= $label ?></a>
    <?php endforeach; ?>
</div>

<div class="visitors-stats">
    <div class="v-stat online">
        <div class="v-stat-value"><?= $stats['online'] ?></div>
        <div class="v-stat-label">Онлайн сейчас</div>
    </div>
    <div class="v-stat">
        <div class="v-stat-value"><?= number_format($stats['visitors'], 0, '', ' ') ?></div>
        <div class="v-stat-label">Визитов</div>
    </div>
    <div class="v-stat">
        <div class="v-stat-value"><?= number_format($stats['unique'], 0, '', ' ') ?></div>
        <div class="v-stat-label">Уникальных</div>
    </div>
    <div class="v-stat">
        <div class="v-stat-value"><?= number_format($stats['pageviews'], 0, '', ' ') ?></div>
        <div class="v-stat-label">Просмотров</div>
    </div>
    <div class="v-stat">
        <div class="v-stat-value"><?= formatDuration($stats['avg_duration']) ?></div>
        <div class="v-stat-label">Среднее время</div>
    </div>
    <div class="v-stat">
        <div class="v-stat-value"><?= $stats['countries'] ?></div>
        <div class="v-stat-label">Стран</div>
    </div>
</div>

<div class="top-grid">
    <div class="top-card">
        <h3>🌍 Топ стран</h3>
        <?php if ($topCountries): ?>
            <?php foreach ($topCountries as $c): ?>
                <div class="top-item">
                    <span class="top-name"><?= e($c['country']) ?></span>
                    <span class="top-count"><?= $c['cnt'] ?></span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="color:#94a3b8; font-size:13px;">Нет данных</div>
        <?php endif; ?>
    </div>
    
    <div class="top-card">
        <h3>🔗 Источники переходов</h3>
        <?php if ($topSources): ?>
            <?php foreach ($topSources as $s): ?>
                <div class="top-item">
                    <span class="top-name"><?= e($s['source']) ?></span>
                    <span class="top-count"><?= $s['cnt'] ?></span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="color:#94a3b8; font-size:13px;">Нет данных</div>
        <?php endif; ?>
    </div>
    
    <div class="top-card">
        <h3>📱 Устройства</h3>
        <?php if ($topDevices): ?>
            <?php 
            $deviceLabels = ['mobile' => '📱 Мобильные', 'desktop' => '🖥 Десктоп', 'tablet' => '📲 Планшеты'];
            foreach ($topDevices as $d): 
            ?>
                <div class="top-item">
                    <span class="top-name"><?= $deviceLabels[$d['device_type']] ?? e($d['device_type']) ?></span>
                    <span class="top-count"><?= $d['cnt'] ?></span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="color:#94a3b8; font-size:13px;">Нет данных</div>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="overflow:hidden;">
    <div style="padding:18px 20px; border-bottom:1px solid #eef2f7; display:flex; justify-content:space-between; align-items:center;">
        <div style="font-size:16px; font-weight:800; color:#0f172a;">Список посетителей</div>
        <div style="font-size:12px; color:#64748b;">Показано <?= count($visitors) ?> из <?= $totalRows ?></div>
    </div>
    
    <?php if ($visitors): ?>
        <?php foreach ($visitors as $v): 
            $online = isOnline($v['last_activity']);
        ?>
            <div class="visitor-row">
                <div class="visitor-status <?= $online ? 'online' : 'offline' ?>" title="<?= $online ? 'Онлайн' : 'Офлайн' ?>"></div>
                
                <div class="visitor-geo">
                    <div>
                        <span class="visitor-geo-flag"><?= flagEmoji($v['country_code']) ?></span>
                        <span class="visitor-geo-country"><?= e($v['country'] ?: 'Unknown') ?></span>
                    </div>
                    <div class="visitor-geo-city">
                        <?= e($v['city'] ?: '—') ?><?= $v['region'] && $v['region'] !== $v['city'] ? ', ' . e($v['region']) : '' ?>
                    </div>
                </div>
                
                <div class="visitor-tech">
                    <strong><?= e($v['browser'] ?: '—') ?></strong><br>
                    <?= e($v['os'] ?: '—') ?>
                </div>
                
                <div class="visitor-source">
                    <strong style="color:#0f172a;"><?= e($v['source'] ?: '—') ?></strong>
                    <?php if ($v['isp']): ?>
                        <div style="color:#94a3b8; font-size:11px;">ISP: <?= e($v['isp']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="visitor-pages">
                    <div class="visitor-pages-count"><?= (int)$v['pages_count'] ?></div>
                    <div class="visitor-pages-label">страниц</div>
                </div>
                
                <div class="visitor-duration">
                    <?= formatDuration((int)$v['duration_seconds']) ?>
                </div>
                
                <div class="visitor-time">
                    <?= formatDate($v['created_at'], 'd.m.Y') ?><br>
                    <?= formatDate($v['created_at'], 'H:i:s') ?>
                </div>
                
                <div style="margin-left:auto; text-align:right;">
                    <div class="visitor-ip"><?= e($v['ip_address']) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= min($totalPages, 10); $i++): ?>
                <?php if ($i === $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?period=<?= $period ?>&p=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="empty-state">
            <div style="font-size:48px; margin-bottom:12px;">📊</div>
            <div style="font-weight:700; color:#0f172a; margin-bottom:4px;">Посетителей пока нет</div>
            <div>Данные появятся, когда пользователи начнут заходить на сайт</div>
            <div style="margin-top:16px; font-size:12px;">Убедитесь, что в <code style="background:#f1f5f9; padding:2px 6px; border-radius:4px;">dist/index.html</code> подключён <code style="background:#f1f5f9; padding:2px 6px; border-radius:4px;">tracker.js</code></div>
        </div>
    <?php endif; ?>
</div>

<!-- Автообновление каждые 30 секунд для отслеживания онлайн -->
<script>
    setTimeout(function() { location.reload(); }, 30000);
</script>

<?php include APP_ROOT . '/admin/templates/footer.php'; ?>
