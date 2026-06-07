<?php
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
requireAdmin();

$db = db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $db->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$_POST['status'], $_POST['order_id']]);
        $message = 'Статус обновлён';
    }
}

$orders = $db->query("SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetchAll();

$page_title = 'Заказы';
include APP_ROOT . '/admin/templates/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">Заказы</h1>
</div>

<?php if ($message): ?>
    <div class="flash-message flash-success mb-4"><?= e($message) ?></div>
<?php endif; ?>

<div class="card overflow-hidden">
    <table class="table">
        <thead>
            <tr>
                <th>№ заказа</th>
                <th>Клиент</th>
                <th>Email</th>
                <th>Сумма</th>
                <th>Статус</th>
                <th>Дата</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td class="font-mono text-xs"><?= e($order['order_number']) ?></td>
                <td class="font-semibold"><?= e($order['customer_name']) ?></td>
                <td class="text-slate-600"><?= e($order['customer_email']) ?></td>
                <td class="font-bold"><?= formatPrice($order['total_amount']) ?></td>
                <td>
                    <form method="POST" class="inline">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <input type="hidden" name="update_status" value="1">
                        <select name="status" onchange="this.form.submit()" class="text-xs border rounded px-2 py-1">
                            <option value="pending" <?= $order['status']==='pending'?'selected':'' ?>>Ожидает</option>
                            <option value="paid" <?= $order['status']==='paid'?'selected':'' ?>>Оплачен</option>
                            <option value="cancelled" <?= $order['status']==='cancelled'?'selected':'' ?>>Отменён</option>
                            <option value="refunded" <?= $order['status']==='refunded'?'selected':'' ?>>Возврат</option>
                        </select>
                    </form>
                </td>
                <td class="text-slate-500"><?= formatDate($order['created_at'], 'd.m.Y H:i') ?></td>
                <td>
                    <a href="#" class="text-blue-600 hover:underline text-sm">Детали</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include APP_ROOT . '/admin/templates/footer.php'; ?>
