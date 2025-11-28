<?php
/**
 * 授業評価削除API
 */

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/functions.php';

if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

$user_id = get_current_user_id();
$input = json_decode(file_get_contents('php://input'), true);
$course_id = $input['course_id'] ?? null;

if (empty($course_id)) {
    json_error('course_idが必要です');
}

try {
    $stmt = $pdo->prepare('
        DELETE FROM course_evaluations
        WHERE course_id = ? AND user_id = ?
    ');
    $stmt->execute([$course_id, $user_id]);
    
    json_success(['message' => '評価を削除しました']);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
