<?php
/**
 * 授業評価一覧取得API
 */

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/functions.php';

if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

$user_id = get_current_user_id();

try {
    $stmt = $pdo->prepare('
        SELECT 
            c.course_id,
            c.course_name,
            AVG(ce.rating) as avg_rating,
            COUNT(ce.course_evaluation_id) as evaluation_count,
            MAX(CASE WHEN ce.user_id = ? THEN 1 ELSE 0 END) as has_my_evaluation
        FROM user_courses uc
        JOIN courses c ON uc.course_id = c.course_id
        LEFT JOIN course_evaluations ce ON c.course_id = ce.course_id
        WHERE uc.user_id = ?
        GROUP BY c.course_id, c.course_name
        ORDER BY c.course_name
    ');
    $stmt->execute([$user_id, $user_id]);
    $courses = $stmt->fetchAll();
    
    json_success(['courses' => $courses]);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
