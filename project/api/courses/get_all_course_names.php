<?php
/**
 * 全授業名取得API
 * 
 * GET /api/courses/get_all_course_names.php
 */

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/functions.php';

// ログインチェック
if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

try {
    $user_id = get_current_user_id();

    // 自分がまだ登録していない授業名を取得（重複なし、アルファベット順）
    $stmt = $pdo->prepare('
        SELECT DISTINCT c.course_name
        FROM courses c
        WHERE c.course_id NOT IN (
            SELECT course_id
            FROM user_courses
            WHERE user_id = ?
        )
        ORDER BY c.course_name ASC
    ');
    $stmt->execute([$user_id]);
    $courses = $stmt->fetchAll(PDO::FETCH_COLUMN);

    json_success([
        'course_names' => $courses
    ]);

} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>