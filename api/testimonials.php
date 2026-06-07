<?php
/**
 * API отзывов:
 *  GET  — список опубликованных отзывов
 *  POST — добавление отзыва пользователем (на модерацию)
 */
session_start();
require_once __DIR__ . '/../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

header('Content-Type: application/json; charset=utf-8');

$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) { $input = $_POST; }

    $name   = isset($input['name']) ? trim($input['name']) : '';
    $role   = isset($input['role']) ? trim($input['role']) : '';
    $text   = isset($input['text']) ? trim($input['text']) : '';
    $rating = isset($input['rating']) ? (int)$input['rating'] : 5;
    if ($rating < 1) { $rating = 1; }
    if ($rating > 5) { $rating = 5; }

    if ($name === '' || $text === '') {
        http_response_code(400);
        echo json_encode(array('error' => 'Заполните имя и текст отзыва'), JSON_UNESCAPED_UNICODE);
        exit;
    }

    $avatar = mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8');

    // Новые пользовательские отзывы попадают на модерацию (is_published = 0)
    $stmt = $db->prepare("INSERT INTO testimonials (author_name, author_role, text, rating, avatar, is_published, sort_order, created_at) VALUES (?, ?, ?, ?, ?, 0, 999, CURRENT_TIMESTAMP)");
    $stmt->execute(array($name, $role, $text, $rating, $avatar));

    echo json_encode(array('success' => true, 'message' => 'Спасибо! Отзыв отправлен на модерацию.'), JSON_UNESCAPED_UNICODE);
    exit;
}

// GET — только опубликованные
$rows = $db->query("SELECT id, author_name, author_role, text, rating, avatar FROM testimonials WHERE is_published = 1 ORDER BY sort_order ASC, created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

$items = array();
foreach ($rows as $r) {
    $items[] = array(
        'id'     => (int)$r['id'],
        'name'   => $r['author_name'],
        'role'   => $r['author_role'],
        'text'   => $r['text'],
        'rating' => (int)$r['rating'],
        'avatar' => $r['avatar'] ?: mb_strtoupper(mb_substr($r['author_name'], 0, 1, 'UTF-8'), 'UTF-8'),
    );
}

echo json_encode(array('testimonials' => $items), JSON_UNESCAPED_UNICODE);
