<?php
/**
 * 授業一覧取得API
 * 
 * GET /api/courses/get_courses.php
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

// ログインチェック
if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

$user_id = get_current_user_id();

try {
    // ユーザーが登録している授業一覧を取得（表示順でソート）
    $stmt = $pdo->prepare('
        SELECT 
            c.course_id,
            c.course_name,
            c.description,
            uco.display_order,
            AVG(ce.rating) as avg_rating
        FROM user_courses uc
        JOIN courses c ON uc.course_id = c.course_id
        LEFT JOIN user_course_order uco ON uc.course_id = uco.course_id AND uc.user_id = uco.user_id
        LEFT JOIN course_evaluations ce ON c.course_id = ce.course_id
        WHERE uc.user_id = ?
        GROUP BY c.course_id, c.course_name, c.description, uco.display_order
        ORDER BY COALESCE(uco.display_order, 999999), c.created_at
    ');
    $stmt->execute([$user_id]);
    $courses = $stmt->fetchAll();
    
    json_success([
        'courses' => $courses
    ]);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
