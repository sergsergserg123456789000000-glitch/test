<?php
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';
requireAdmin();

$db = db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        if (isset($_POST['generate'])) {
            $count = (int)$_POST['count'];
            $productId = (int)$_POST['product_id'];
            $seats = (int)$_POST['seats'];
            $months = (int)$_POST['months'];
            
            for ($i = 0; $i < $count; $i++) {
                $key = 'PS-' . strtoupper(bin2hex(random_bytes(8)));
                $expires = date('Y-m-d H:i:s', strtotime("+$months months"));
                
                $db->prepare("INSERT INTO licenses (license_key, product_id, status, seats, expires_at) VALUES (?, ?, 'active', ?, ?)")->execute([$key, $productId, $seats, $expires]);
            }
            $message = "Сгенерировано $count ключей";
        }
        
        if (isset($_POST['block'])) {
            $db->prepare("UPDATE licenses SET status='blocked' WHERE id=?")->execute([$_POST['license_id']]);
            $message = 'Ключ заблокирован';
        }
        
        if (isset($_POST['unblock'])) {
            $db->prepare("UPDATE licenses SET status='active' WHERE id=?")->execute([$_POST['license_id']]);
            $message = 'Ключ разблокирован';
        }
    }
}

$licenses = $db->query("SELECT l.*, p.name as product_name FROM licenses l JOIN products p ON l.product_id = p.id ORDER BY l.created_at DESC LIMIT 50")->fetchAll();
$products = $db->query("SELECT id, name FROM products WHERE is_active=1")->fetchAll();

$page_title = 'Лицензии';
include APP_ROOT . '/admin/templates/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">Лицензии</h1>
    <button onclick="document.getElementById('genModal').classList.add('show')" class="btn btn-primary">+ Сгенерировать ключи</button>
</div>

<?php if ($message): ?>
    <div class="flash-message flash-success mb-4"><?= e($message) ?></div>
<?php endif; ?>

<div class="card overflow-hidden">
    <table class="table">
        <thead>
            <tr>
                <th>Ключ</th>
                <th>Продукт</th>
                <th>Мест</th>
                <th>Статус</th>
                <th>Истекает</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($licenses as $lic): ?>
            <tr>
                <td class="font-mono text-xs"><?= e($lic['license_key']) ?></td>
                <td><?= e($lic['product_name']) ?></td>
                <td><?= $lic['seats'] ?></td>
                <td>
                    <?php if ($lic['status']==='active'): ?>
                        <span class="badge badge-success">Активен</span>
                    <?php elseif ($lic['status']==='blocked'): ?>
                        <span class="badge badge-danger">Заблокирован</span>
                    <?php else: ?>
                        <span class="badge badge-warning"><?= e($lic['status']) ?></span>
                    <?php endif; ?>
                </td>
                <td class="text-slate-500"><?= $lic['expires_at'] ? formatDate($lic['expires_at'], 'd.m.Y') : '—' ?></td>
                <td>
                    <?php if ($lic['status']==='active'): ?>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="license_id" value="<?= $lic['id'] ?>">
                            <button name="block" class="text-red-600 text-sm hover:underline">Заблокировать</button>
                        </form>
                    <?php elseif ($lic['status']==='blocked'): ?>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="license_id" value="<?= $lic['id'] ?>">
                            <button name="unblock" class="text-green-600 text-sm hover:underline">Разблокировать</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Модалка генерации -->
<div id="genModal" class="modal">
    <div class="modal-content">
        <h3 class="text-lg font-bold mb-4">Генерация лицензий</h3>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="generate" value="1">
            
            <div class="form-group">
                <label>Продукт</label>
                <select name="product_id" class="form-control" required>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div class="form-group">
                    <label>Количество</label>
                    <input type="number" name="count" value="10" min="1" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Мест</label>
                    <input type="number" name="seats" value="1" min="1" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Срок (мес)</label>
                    <input type="number" name="months" value="12" min="1" class="form-control" required>
                </div>
            </div>
            
            <div class="flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary">Сгенерировать</button>
                <button type="button" onclick="document.getElementById('genModal').classList.remove('show')" class="btn btn-secondary">Отмена</button>
            </div>
        </form>
    </div>
</div>

<?php include APP_ROOT . '/admin/templates/footer.php'; ?>
