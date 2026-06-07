<?php
/**
 * Тест: проверяет работу API, подключение к БД и наличие данных.
 * Откройте: https://demo.net.ru/master/api/test.php
 * Когда всё заработает — удалите этот файл.
 */
header('Content-Type: text/html; charset=utf-8');
echo "<h2>API Test</h2>";

// 1. PHP работает
echo "<p>✅ PHP работает. Версия: " . phpversion() . "</p>";

// 2. Config
try {
    require_once __DIR__ . '/../config.php';
    echo "<p>✅ config.php подключён. SITE_NAME = " . SITE_NAME . "</p>";
    echo "<p>📁 APP_ROOT = " . APP_ROOT . "</p>";
    echo "<p>📁 INCLUDES_DIR = " . INCLUDES_DIR . "</p>";
} catch (Exception $e) {
    echo "<p>❌ config.php: " . $e->getMessage() . "</p>";
    exit;
}

// 3. DB
try {
    require_once INCLUDES_DIR . '/db.php';
    require_once INCLUDES_DIR . '/functions.php';
    $db = db();
    echo "<p>✅ БД подключена</p>";
} catch (Exception $e) {
    echo "<p>❌ БД: " . $e->getMessage() . "</p>";
    exit;
}

// 4. Products
try {
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM products");
    $count = $stmt->fetch();
    echo "<p>📦 Всего продуктов в БД: <strong>" . $count['cnt'] . "</strong></p>";

    $stmt = $db->query("SELECT COUNT(*) as cnt FROM products WHERE is_active = 1");
    $count = $stmt->fetch();
    echo "<p>📦 Активных (is_active=1): <strong>" . $count['cnt'] . "</strong></p>";

    $stmt = $db->query("SELECT id, slug, name, is_active FROM products ORDER BY id");
    $rows = $stmt->fetchAll();
    echo "<table border='1' cellpadding='4'><tr><th>ID</th><th>Slug</th><th>Name</th><th>Active</th></tr>";
    foreach ($rows as $r) {
        echo "<tr><td>{$r['id']}</td><td>{$r['slug']}</td><td>{$r['name']}</td><td>{$r['is_active']}</td></tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p>❌ Products: " . $e->getMessage() . "</p>";
}

// 5. Blog
try {
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM blog_posts WHERE is_published = 1");
    $count = $stmt->fetch();
    echo "<p>📝 Опубликованных статей: <strong>" . $count['cnt'] . "</strong></p>";
} catch (Exception $e) {
    echo "<p>❌ Blog: " . $e->getMessage() . "</p>";
}

// 6. API URL
echo "<h3>Проверьте эти URL:</h3>";
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$base = str_replace('/api', '', $base);
echo "<p><a href='{$base}/api/products.php' target='_blank'>{$base}/api/products.php</a> — должен вернуть JSON с продуктами</p>";
echo "<p><a href='{$base}/api/blog.php' target='_blank'>{$base}/api/blog.php</a> — должен вернуть JSON со статьями</p>";

echo "<p style='margin-top:20px; color:#999;'>Удалите этот файл после тестирования!</p>";
