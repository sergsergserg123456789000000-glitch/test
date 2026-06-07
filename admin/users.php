<?php
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
requireAdmin();

$db = db();
$users = $db->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 100")->fetchAll();

$page_title = 'Пользователи';
include APP_ROOT . '/admin/templates/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">Пользователи</h1>
</div>

<div class="card overflow-hidden">
    <table class="table">
        <thead>
            <tr>
                <th>Email</th>
                <th>Имя</th>
                <th>Компания</th>
                <th>Источник</th>
                <th>Email</th>
                <th>Регистрация</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= e($u['email']) ?></td>
                <td class="font-semibold"><?= e($u['name']) ?></td>
                <td class="text-slate-600"><?= e($u['company'] ?? '—') ?></td>
                <td><span class="badge badge-info"><?= e($u['social_provider']) ?></span></td>
                <td>
                    <?php if ($u['email_verified']): ?>
                        <span class="badge badge-success">✓</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Не подтверждён</span>
                    <?php endif; ?>
                </td>
                <td class="text-slate-500"><?= formatDate($u['created_at'], 'd.m.Y') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include APP_ROOT . '/admin/templates/footer.php'; ?>
