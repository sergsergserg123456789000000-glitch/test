<?php
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
requireAdmin();

$db = db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_ticket'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $db->prepare("UPDATE support_tickets SET status=?, priority=? WHERE id=?")->execute([$_POST['status'], $_POST['priority'], $_POST['ticket_id']]);
        $message = 'Тикет обновлён';
    }
}

$tickets = $db->query("SELECT * FROM support_tickets ORDER BY created_at DESC")->fetchAll();

$page_title = 'Тикеты';
include APP_ROOT . '/admin/templates/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">Тикеты поддержки</h1>
</div>

<?php if ($message): ?>
    <div class="flash-message flash-success mb-4"><?= e($message) ?></div>
<?php endif; ?>

<div class="card overflow-hidden">
    <table class="table">
        <thead>
            <tr>
                <th>№</th>
                <th>Тема</th>
                <th>Приоритет</th>
                <th>Статус</th>
                <th>Дата</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $t): ?>
            <tr>
                <td class="font-mono text-xs"><?= e($t['ticket_number']) ?></td>
                <td><?= e($t['subject']) ?></td>
                <td>
                    <form method="POST" class="inline">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
                        <input type="hidden" name="update_ticket" value="1">
                        <select name="priority" onchange="this.form.submit()" class="text-xs border rounded px-2 py-1">
                            <option value="low" <?= $t['priority']==='low'?'selected':'' ?>>Низкий</option>
                            <option value="medium" <?= $t['priority']==='medium'?'selected':'' ?>>Средний</option>
                            <option value="high" <?= $t['priority']==='high'?'selected':'' ?>>Высокий</option>
                            <option value="critical" <?= $t['priority']==='critical'?'selected':'' ?>>Критический</option>
                        </select>
                        <select name="status" onchange="this.form.submit()" class="text-xs border rounded px-2 py-1 ml-1">
                            <option value="open" <?= $t['status']==='open'?'selected':'' ?>>Открыт</option>
                            <option value="in_progress" <?= $t['status']==='in_progress'?'selected':'' ?>>В работе</option>
                            <option value="pending" <?= $t['status']==='pending'?'selected':'' ?>>Ожидает</option>
                            <option value="closed" <?= $t['status']==='closed'?'selected':'' ?>>Закрыт</option>
                        </select>
                    </form>
                </td>
                <td class="text-slate-500"><?= formatDate($t['created_at'], 'd.m.Y') ?></td>
                <td><a href="#" class="text-blue-600 text-sm">Открыть</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include APP_ROOT . '/admin/templates/footer.php'; ?>
