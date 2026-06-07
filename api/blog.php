<?php
/**
 * API: Статьи блога из БД
 */
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');
header('Access-Control-Allow-Origin: *');

try {
    require_once __DIR__ . '/../config.php';
    require_once INCLUDES_DIR . '/db.php';
    require_once INCLUDES_DIR . '/functions.php';

    $db = db();
    $slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

    if ($slug !== '') {
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = ? AND is_published = 1");
        $stmt->execute(array($slug));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            echo json_encode(array('error' => 'Post not found'), JSON_UNESCAPED_UNICODE);
            exit;
        }
        $db->prepare("UPDATE blog_posts SET views_count = views_count + 1 WHERE id = ?")->execute(array($row['id']));
        echo json_encode(array(
            'id' => (string)$row['id'],
            'slug' => $row['slug'],
            'title' => $row['title'],
            'excerpt' => isset($row['excerpt']) ? $row['excerpt'] : '',
            'content' => isset($row['content']) ? $row['content'] : '',
            'category' => isset($row['category']) ? $row['category'] : '',
            'author' => isset($row['author_name']) ? $row['author_name'] : '',
            'date' => $row['published_at'] ? date('d.m.Y', strtotime($row['published_at'])) : date('d.m.Y', strtotime($row['created_at'])),
            'readTime' => max(1, (int)(mb_strlen(strip_tags(isset($row['content']) ? $row['content'] : '')) / 1200)),
            'coverImage' => isset($row['cover_image']) ? $row['cover_image'] : null,
            'tags' => isset($row['tags']) ? $row['tags'] : '',
        ), JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $db->query("SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY published_at DESC, created_at DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $out = array();
    foreach ($rows as $r) {
        $out[] = array(
            'id' => (string)$r['id'],
            'slug' => $r['slug'],
            'title' => $r['title'],
            'excerpt' => isset($r['excerpt']) ? $r['excerpt'] : '',
            'content' => isset($r['content']) ? $r['content'] : '',
            'category' => isset($r['category']) ? $r['category'] : '',
            'author' => isset($r['author_name']) ? $r['author_name'] : '',
            'date' => $r['published_at'] ? date('d.m.Y', strtotime($r['published_at'])) : date('d.m.Y', strtotime($r['created_at'])),
            'readTime' => max(1, (int)(mb_strlen(strip_tags(isset($r['content']) ? $r['content'] : '')) / 1200)),
            'coverImage' => isset($r['cover_image']) ? $r['cover_image'] : null,
            'tags' => isset($r['tags']) ? $r['tags'] : '',
        );
    }

    echo json_encode(array('posts' => $out), JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode(array(
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'posts' => array(),
    ), JSON_UNESCAPED_UNICODE);
}
