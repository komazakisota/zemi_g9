<?php
/**
 * 課題完了API
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

if (empty($assignment_id)) {
    json_error('assignment_idが必要です');
}

try {
    $stmt = $pdo->prepare('
        INSERT INTO assignment_completion (assignment_id, user_id, is_completed, completed_at)
        VALUES (?, ?, TRUE, CURRENT_TIMESTAMP)
        ON CONFLICT (assignment_id, user_id)
        DO UPDATE SET is_completed = TRUE, completed_at = CURRENT_TIMESTAMP
    ');
    $stmt->execute([$assignment_id, $user_id]);
    
    json_success(['message' => '課題を完了にしました']);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>