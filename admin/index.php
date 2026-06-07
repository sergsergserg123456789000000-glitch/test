<?php
/**
 * Admin Dashboard
 */
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
requireAdmin();

$db = db();
$admin_name = $_SESSION['admin_name'];
$admin_role = $_SESSION['admin_role'];

$stats = [];
$stmt = $db->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE status = 'paid' AND date(paid_at) = date('now', 'localtime')");
$stats['sales_today'] = $stmt->fetch()['total'] ?? 0;
$stmt = $db->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE status = 'paid' AND strftime('%Y-%m', paid_at) = strftime('%Y-%m', 'now', 'localtime')");
$stats['sales_month'] = $stmt->fetch()['total'] ?? 0;
$stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE date(created_at) = date('now', 'localtime')");
$stats['users_today'] = $stmt->fetch()['count'] ?? 0;
$stmt = $db->query("SELECT COUNT(*) as count FROM licenses WHERE status = 'active'");
$stats['active_licenses'] = $stmt->fetch()['count'] ?? 0;
$stmt = $db->query("SELECT COUNT(*) as count FROM support_tickets WHERE status IN ('open', 'in_progress')");
$stats['open_tickets'] = $stmt->fetch()['count'] ?? 0;
$stmt = $db->query("SELECT COUNT(*) as count FROM blog_posts WHERE is_published = 1");
$stats['published_posts'] = $stmt->fetch()['count'] ?? 0;

$stmt = $db->query("SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
$recent_orders = $stmt->fetchAll();
$stmt = $db->query("SELECT * FROM support_tickets ORDER BY created_at DESC LIMIT 5");
$recent_tickets = $stmt->fetchAll();
$stmt = $db->query("SELECT title, category, views_count, created_at FROM blog_posts ORDER BY created_at DESC LIMIT 4");
$recent_posts = $stmt->fetchAll();

$page_title = 'Дашборд';
include APP_ROOT . '/admin/templates/header.php';
?>

<style>
.dashboard-grid { display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap:16px; margin-bottom:24px; }
.stat-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:20px; }
.stat-top { display:flex; align-items:flex-start; justify-content:space-between; }
.stat-icon { width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; flex-shrink:0; }
.stat-icon svg { width:20px; height:20px; }
.stat-value { margin-top:16px; font-size:30px; font-weight:800; color:#0f172a; line-height:1; }
.stat-label { margin-top:6px; font-size:12px; color:#64748b; }
.dashboard-row { display:grid; grid-template-columns: 2fr 1fr; gap:16px; margin-bottom:16px; }
.panel { background:#fff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden; }
.panel-head { display:flex; align-items:center; justify-content:space-between; padding:18px 20px; border-bottom:1px solid #eef2f7; }
.panel-title { font-size:16px; font-weight:800; color:#0f172a; }
.panel-link { font-size:13px; color:#2563eb; text-decoration:none; font-weight:600; }
.panel-link:hover { text-decoration:underline; }
.panel-body { padding:20px; }
.activity-list { display:flex; flex-direction:column; gap:14px; }
.activity-item { display:flex; gap:10px; align-items:flex-start; }
.activity-dot { width:8px; height:8px; border-radius:50%; margin-top:6px; flex-shrink:0; }
.mini-table { width:100%; border-collapse:collapse; }
.mini-table th, .mini-table td { padding:12px 16px; font-size:13px; border-bottom:1px solid #eef2f7; }
.mini-table th { background:#f8fafc; color:#64748b; text-transform:uppercase; letter-spacing:.04em; font-size:11px; }
.mini-table tr:hover td { background:#fafcff; }
.ticket-row { display:flex; align-items:center; gap:12px; padding:14px 20px; border-bottom:1px solid #eef2f7; }
.ticket-icon { width:36px; height:36px; border-radius:10px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.ticket-icon svg { width:16px; height:16px; color:#64748b; }
.ticket-main { flex:1; min-width:0; }
.ticket-title { font-size:13px; font-weight:700; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ticket-sub { font-size:12px; color:#64748b; margin-top:2px; }
.section-title { font-size:28px; font-weight:800; color:#0f172a; }
.section-subtitle { margin-top:4px; font-size:14px; color:#64748b; }
@media (max-width: 1200px){ .dashboard-grid{grid-template-columns:repeat(2,minmax(0,1fr));} .dashboard-row{grid-template-columns:1fr;} }
@media (max-width: 640px){ .dashboard-grid{grid-template-columns:1fr;} }
</style>

<div>
    <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:24px;">
        <div>
            <div class="section-title">Дашборд</div>
            <div class="section-subtitle">Сводка по продажам, пользователям, лицензиям и контенту</div>
        </div>
        <a href="<?= url('/') ?>" target="_blank" class="btn btn-secondary">На сайт →</a>
    </div>

    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:linear-gradient(135deg,#2563eb,#1d4ed8)">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="badge badge-success">сегодня</span>
            </div>
            <div class="stat-value"><?= formatPrice($stats['sales_today']) ?></div>
            <div class="stat-label">Продажи за день</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:linear-gradient(135deg,#16a34a,#15803d)">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <span class="badge badge-info">новые</span>
            </div>
            <div class="stat-value"><?= (int)$stats['users_today'] ?></div>
            <div class="stat-label">Пользователи за день</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:linear-gradient(135deg,#7c3aed,#6d28d9)">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                </div>
                <span class="badge badge-success">активны</span>
            </div>
            <div class="stat-value"><?= (int)$stats['active_licenses'] ?></div>
            <div class="stat-label">Активные лицензии</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background:linear-gradient(135deg,#f97316,#ea580c)">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                </div>
                <span class="badge badge-warning">в работе</span>
            </div>
            <div class="stat-value"><?= (int)$stats['open_tickets'] ?></div>
            <div class="stat-label">Открытые тикеты</div>
        </div>
    </div>

    <div class="dashboard-row">
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">Последние заказы</div>
                <a href="<?= url('/admin/orders.php') ?>" class="panel-link">Все заказы →</a>
            </div>
            <div style="overflow:auto;">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>№</th>
                            <th>Клиент</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th>Дата</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_orders): ?>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td style="font-family:monospace; font-size:12px;"><?= e($order['order_number']) ?></td>
                                    <td style="font-weight:700;"><?= e($order['customer_name']) ?></td>
                                    <td style="font-weight:700;"><?= formatPrice($order['total_amount']) ?></td>
                                    <td>
                                        <?php if ($order['status'] === 'paid'): ?>
                                            <span class="badge badge-success">Оплачен</span>
                                        <?php elseif ($order['status'] === 'pending'): ?>
                                            <span class="badge badge-warning">Ожидает</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger"><?= e($order['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color:#64748b;"><?= formatDate($order['created_at'], 'd.m.Y H:i') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="color:#64748b;">Пока нет заказов</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">Быстрая сводка</div>
                <a href="<?= url('/admin/blog.php') ?>" class="panel-link">Контент →</a>
            </div>
            <div class="panel-body">
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-dot" style="background:#2563eb"></div>
                        <div>
                            <div style="font-size:13px; font-weight:700; color:#0f172a;">Продажи за месяц</div>
                            <div style="font-size:12px; color:#64748b;"><?= formatPrice($stats['sales_month']) ?></div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot" style="background:#16a34a"></div>
                        <div>
                            <div style="font-size:13px; font-weight:700; color:#0f172a;">Опубликовано статей</div>
                            <div style="font-size:12px; color:#64748b;"><?= (int)$stats['published_posts'] ?></div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot" style="background:#f97316"></div>
                        <div>
                            <div style="font-size:13px; font-weight:700; color:#0f172a;">Тикеты в работе</div>
                            <div style="font-size:12px; color:#64748b;"><?= (int)$stats['open_tickets'] ?></div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot" style="background:#7c3aed"></div>
                        <div>
                            <div style="font-size:13px; font-weight:700; color:#0f172a;">Лицензии</div>
                            <div style="font-size:12px; color:#64748b;">Активных: <?= (int)$stats['active_licenses'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-row">
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">Последние тикеты</div>
                <a href="<?= url('/admin/tickets.php') ?>" class="panel-link">Все тикеты →</a>
            </div>
            <div>
                <?php if ($recent_tickets): ?>
                    <?php foreach ($recent_tickets as $ticket): ?>
                        <div class="ticket-row">
                            <div class="ticket-icon">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                            </div>
                            <div class="ticket-main">
                                <div class="ticket-title"><?= e($ticket['subject']) ?></div>
                                <div class="ticket-sub">#<?= e($ticket['ticket_number']) ?> · <?= e($ticket['priority']) ?> · <?= formatDate($ticket['created_at'], 'd.m.Y H:i') ?></div>
                            </div>
                            <div>
                                <?php if ($ticket['status'] === 'open'): ?>
                                    <span class="badge badge-info">Открыт</span>
                                <?php elseif ($ticket['status'] === 'in_progress'): ?>
                                    <span class="badge badge-warning">В работе</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Закрыт</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="panel-body" style="color:#64748b;">Тикетов пока нет</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">Последние статьи</div>
                <a href="<?= url('/admin/blog.php') ?>" class="panel-link">Редактировать →</a>
            </div>
            <div>
                <?php if ($recent_posts): ?>
                    <?php foreach ($recent_posts as $post): ?>
                        <div class="ticket-row">
                            <div class="ticket-icon">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                            </div>
                            <div class="ticket-main">
                                <div class="ticket-title"><?= e($post['title']) ?></div>
                                <div class="ticket-sub"><?= e($post['category'] ?: 'Без категории') ?> · Просмотры: <?= (int)$post['views_count'] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="panel-body" style="color:#64748b;">Статей пока нет</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/admin/templates/footer.php'; ?>
