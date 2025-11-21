<?php
/**
 * 課題一覧取得API
 * 
 * GET /api/assignments/get_assignments.php?course_year_id=1
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

// ログインチェック
if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

$user_id = get_current_user_id();
$course_year_id = get('course_year_id');

if (empty($course_year_id)) {
    json_error('course_year_idが必要です');
}

try {
    // 課題一覧を取得（カスタム順 or 期限順）
    $stmt = $pdo->prepare('
        SELECT 
            a.assignment_id,
            a.assignment_name,
            a.deadline,
            a.has_time,
            a.created_at,
            ac.is_completed,
            ao.display_order,
            AVG(ae.rating) as avg_rating
        FROM assignments a
        LEFT JOIN assignment_completion ac ON a.assignment_id = ac.assignment_id AND ac.user_id = ?
        LEFT JOIN assignment_order ao ON a.assignment_id = ao.assignment_id AND ao.user_id = ?
        LEFT JOIN assignment_evaluations ae ON a.assignment_id = ae.assignment_id
        WHERE a.course_year_id = ?
        GROUP BY a.assignment_id, a.assignment_name, a.deadline, a.has_time, a.created_at, 
                 ac.is_completed, ao.display_order
        ORDER BY 
            CASE WHEN ao.display_order IS NOT NULL THEN ao.display_order ELSE 999999 END,
            CASE WHEN a.deadline IS NULL THEN 1 ELSE 0 END,
            a.deadline ASC NULLS LAST,
            a.created_at ASC
    ');
    $stmt->execute([$user_id, $user_id, $course_year_id]);
    $assignments = $stmt->fetchAll();
    
    // データ型を変換
    foreach ($assignments as &$assignment) {
        // is_completedをbooleanに変換（PostgreSQLは't'/'f'または true/false）
        $assignment['is_completed'] = $assignment['is_completed'] === 't' || $assignment['is_completed'] === true;
        
        // avg_ratingを数値に変換
        $assignment['avg_rating'] = $assignment['avg_rating'] ? (float)$assignment['avg_rating'] : null;
    }
    
    json_success([
        'assignments' => $assignments
    ]);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>