<?php
/**
 * API: Продукты из БД
 * Если что-то сломалось — покажет ошибку в JSON, а не белый экран.
 */
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');
header('Access-Control-Allow-Origin: *');

// Оборачиваем ВСЁ в try-catch
try {
    require_once __DIR__ . '/../config.php';
    require_once INCLUDES_DIR . '/db.php';
    require_once INCLUDES_DIR . '/functions.php';

    $db = db();
    $slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

    if ($slug !== '') {
        // Один продукт по slug
        $stmt = $db->prepare("SELECT * FROM products WHERE slug = ? AND is_active = 1");
        $stmt->execute(array($slug));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            echo json_encode(array('error' => 'Product not found'), JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Версии
        $versions = array();
        try {
            $st = $db->prepare("SELECT * FROM product_versions WHERE product_id = ? ORDER BY is_current DESC, created_at DESC");
            $st->execute(array($row['id']));
            $versions = $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ignore) {}

        // Галерея
        $gallery = array();
        try {
            $st = $db->prepare("SELECT image_path FROM product_images WHERE product_id = ? ORDER BY sort_order ASC");
            $st->execute(array($row['id']));
            $gallery = $st->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $ignore) {}

        $vOut = array();
        foreach ($versions as $v) {
            $cl = null;
            if (!empty($v['changelog'])) {
                $cl = array('version' => $v['version'], 'date' => $v['release_date'], 'changes' => explode("\n", trim($v['changelog'])));
            }
            $vOut[] = array(
                'version' => $v['version'],
                'date' => $v['release_date'],
                'size' => $v['file_size'],
                'isCurrent' => (bool)$v['is_current'],
                'changelog' => $cl,
            );
        }

        echo json_encode(array(
            'id' => (string)$row['id'],
            'slug' => $row['slug'],
            'name' => $row['name'],
            'tagline' => isset($row['tagline']) ? $row['tagline'] : '',
            'category' => $row['category'],
            'price' => (float)$row['price'],
            'oldPrice' => !empty($row['old_price']) ? (float)$row['old_price'] : null,
            'rating' => !empty($row['rating']) ? (float)$row['rating'] : 5,
            'reviews' => !empty($row['reviews_count']) ? (int)$row['reviews_count'] : 0,
            'badge' => !empty($row['badge']) ? $row['badge'] : null,
            'description' => isset($row['description']) ? $row['description'] : '',
            'features' => json_decode(isset($row['features']) ? $row['features'] : '[]', true),
            'os' => json_decode(isset($row['os_support']) ? $row['os_support'] : '[]', true),
            'requirements' => json_decode(isset($row['requirements']) ? $row['requirements'] : '{}', true),
            'coverImage' => !empty($row['cover_image']) ? $row['cover_image'] : null,
            'gallery' => $gallery,
            'versions' => $vOut,
        ), JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Все активные продукты
    $stmt = $db->query("SELECT * FROM products WHERE is_active = 1 ORDER BY created_at DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $out = array();
    foreach ($rows as $r) {
        $out[] = array(
            'id' => (string)$r['id'],
            'slug' => $r['slug'],
            'name' => $r['name'],
            'tagline' => isset($r['tagline']) ? $r['tagline'] : '',
            'category' => $r['category'],
            'price' => (float)$r['price'],
            'oldPrice' => !empty($r['old_price']) ? (float)$r['old_price'] : null,
            'rating' => !empty($r['rating']) ? (float)$r['rating'] : 5,
            'reviews' => !empty($r['reviews_count']) ? (int)$r['reviews_count'] : 0,
            'badge' => !empty($r['badge']) ? $r['badge'] : null,
            'description' => isset($r['description']) ? $r['description'] : '',
            'features' => json_decode(isset($r['features']) ? $r['features'] : '[]', true),
            'os' => json_decode(isset($r['os_support']) ? $r['os_support'] : '[]', true),
            'requirements' => json_decode(isset($r['requirements']) ? $r['requirements'] : '{}', true),
            'coverImage' => !empty($r['cover_image']) ? $r['cover_image'] : null,
        );
    }

    echo json_encode(array('products' => $out), JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode(array(
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'products' => array(),
    ), JSON_UNESCAPED_UNICODE);
}
