<?php
/**
 * 年度一覧取得API
 * 
 * GET /api/years/get_years.php?course_id=1
 */

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/functions.php';

// ログインチェック
if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

$course_id = get('course_id');

if (empty($course_id)) {
    json_error('course_idが必要です');
}

try {
    $stmt = $pdo->prepare('
        SELECT course_year_id, course_id, year, created_at
        FROM course_years
        WHERE course_id = ?
        ORDER BY year DESC
    ');
    $stmt->execute([$course_id]);
    $years = $stmt->fetchAll();
    
    json_success([
        'years' => $years
    ]);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
