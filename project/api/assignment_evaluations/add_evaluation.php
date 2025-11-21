<?php
/**
 * 課題評価追加API
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
$assignment_id = post('assignment_id');
$rating = post('rating');
$comment = post('comment');

if (empty($assignment_id) || empty($rating)) {
    json_error('必須項目を入力してください');
}

if ($rating < 1 || $rating > 5) {
    json_error('評価は1〜5の範囲で入力してください');
}

try {
    // 重複チェック
    $stmt = $pdo->prepare('
        SELECT evaluation_id FROM assignment_evaluations
        WHERE assignment_id = ? AND user_id = ?
    ');
    $stmt->execute([$assignment_id, $user_id]);
    
    if ($stmt->fetch()) {
        json_error('この課題は既に評価済みです');
    }
    
    // 評価を追加
    $stmt = $pdo->prepare('
        INSERT INTO assignment_evaluations (assignment_id, user_id, rating, comment)
        VALUES (?, ?, ?, ?)
    ');
    $stmt->execute([$assignment_id, $user_id, $rating, $comment]);
    
    json_success(['message' => '評価を投稿しました']);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
