<?php
/**
 * 課題並び順リセットAPI
 */

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/functions.php';

if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

$user_id = get_current_user_id();
$input = json_decode(file_get_contents('php://input'), true);
$course_year_id = $input['course_year_id'] ?? null;

try {
    $stmt = $pdo->prepare('
        DELETE FROM assignment_order
        WHERE user_id = ? AND course_year_id = ?
    ');
    $stmt->execute([$user_id, $course_year_id]);
    
    json_success(['message' => '並び順をリセットしました']);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
