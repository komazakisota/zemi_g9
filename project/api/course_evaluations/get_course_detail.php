<?php
/**
 * 授業評価詳細取得API
 */

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/functions.php';

if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

$user_id = get_current_user_id();
$course_id = get('course_id');

if (empty($course_id)) {
    json_error('course_idが必要です');
}

try {
    // 自分の評価
    $stmt = $pdo->prepare('
        SELECT rating, comment, created_at, updated_at
        FROM course_evaluations
        WHERE course_id = ? AND user_id = ?
    ');
    $stmt->execute([$course_id, $user_id]);
    $myEvaluation = $stmt->fetch();
    
    // 他の人の評価
    $stmt = $pdo->prepare('
        SELECT 
            ce.rating,
            ce.comment,
            ce.created_at,
            u.username
        FROM course_evaluations ce
        JOIN users u ON ce.user_id = u.user_id
        WHERE ce.course_id = ? AND ce.user_id != ?
        ORDER BY ce.created_at DESC
    ');
    $stmt->execute([$course_id, $user_id]);
    $otherEvaluations = $stmt->fetchAll();
    
    // 平均評価
    $stmt = $pdo->prepare('
        SELECT AVG(rating) as avg_rating
        FROM course_evaluations
        WHERE course_id = ?
    ');
    $stmt->execute([$course_id]);
    $avgData = $stmt->fetch();
    $avgRating = $avgData['avg_rating'] ?? 0;
    
    json_success([
        'my_evaluation' => $myEvaluation,
        'other_evaluations' => $otherEvaluations,
        'avg_rating' => (float)$avgRating
    ]);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
