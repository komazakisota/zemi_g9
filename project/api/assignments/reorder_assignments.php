<?php
/**
 * 課題並び替えAPI
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
$assignment_order = $input['assignment_order'] ?? [];

try {
    $pdo->beginTransaction();
    
    foreach ($assignment_order as $item) {
        $assignment_id = $item['assignment_id'];
        $display_order = $item['display_order'];
        
        $stmt = $pdo->prepare('
            INSERT INTO assignment_order (user_id, course_year_id, assignment_id, display_order)
            VALUES (?, ?, ?, ?)
            ON CONFLICT (user_id, course_year_id, assignment_id)
            DO UPDATE SET display_order = EXCLUDED.display_order
        ');
        $stmt->execute([$user_id, $course_year_id, $assignment_id, $display_order]);
    }
    
    $pdo->commit();
    
    json_success(['message' => '並び順を保存しました']);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
