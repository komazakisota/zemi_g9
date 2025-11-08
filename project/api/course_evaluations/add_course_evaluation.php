<?php
/**
 * 授業評価追加API
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

if (!is_post()) {
    json_error('Invalid request method', 405);
}

$user_id = get_current_user_id();
$course_id = post('course_id');
$rating = post('rating');
$comment = post('comment');

if (empty($course_id) || empty($rating)) {
    json_error('必須項目を入力してください');
}

if ($rating < 1 || $rating > 5) {
    json_error('評価は1〜5の範囲で入力してください');
}

try {
    $stmt = $pdo->prepare('
        SELECT course_evaluation_id FROM course_evaluations
        WHERE course_id = ? AND user_id = ?
    ');
    $stmt->execute([$course_id, $user_id]);
    
    if ($stmt->fetch()) {
        json_error('この授業は既に評価済みです');
    }
    
    $stmt = $pdo->prepare('
        INSERT INTO course_evaluations (course_id, user_id, rating, comment)
        VALUES (?, ?, ?, ?)
    ');
    $stmt->execute([$course_id, $user_id, $rating, $comment]);
    
    json_success(['message' => '評価を投稿しました']);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
