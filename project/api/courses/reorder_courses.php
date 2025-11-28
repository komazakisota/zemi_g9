<?php
/**
 * 授業並び替えAPI
 * 
 * POST /api/courses/reorder_courses.php
 * パラメータ: course_order (JSON配列)
 */

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/functions.php';

// ログインチェック
if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

$user_id = get_current_user_id();
$input = json_decode(file_get_contents('php://input'), true);
$course_order = $input['course_order'] ?? [];

try {
    $pdo->beginTransaction();
    
    foreach ($course_order as $item) {
        $course_id = $item['course_id'];
        $display_order = $item['display_order'];
        
        // UPSERT: 既存の場合は更新、新規の場合は挿入
        $stmt = $pdo->prepare('
            INSERT INTO user_course_order (user_id, course_id, display_order)
            VALUES (?, ?, ?)
            ON CONFLICT (user_id, course_id)
            DO UPDATE SET display_order = EXCLUDED.display_order
        ');
        $stmt->execute([$user_id, $course_id, $display_order]);
    }
    
    $pdo->commit();
    
    json_success(['message' => '並び順を保存しました']);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
