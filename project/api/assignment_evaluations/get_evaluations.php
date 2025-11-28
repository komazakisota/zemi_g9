<?php
/**
 * 課題評価一覧取得API
 */

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/functions.php';

if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

$user_id = get_current_user_id();
$assignment_id = get('assignment_id');

if (empty($assignment_id)) {
    json_error('assignment_idが必要です');
}

try {
    // 課題情報を取得
    $stmt = $pdo->prepare('SELECT assignment_name, deadline, has_time FROM assignments WHERE assignment_id = ?');
    $stmt->execute([$assignment_id]);
    $assignment = $stmt->fetch();
    
    // 評価一覧を取得
    $stmt = $pdo->prepare('
        SELECT 
            ae.evaluation_id,
            ae.rating,
            ae.comment,
            ae.created_at,
            u.username,
            CASE WHEN ae.user_id = ? THEN TRUE ELSE FALSE END as is_mine
        FROM assignment_evaluations ae
        JOIN users u ON ae.user_id = u.user_id
        WHERE ae.assignment_id = ?
        ORDER BY is_mine DESC, ae.created_at DESC
    ');
    $stmt->execute([$user_id, $assignment_id]);
    $evaluations = $stmt->fetchAll();
    
    // 平均評価を計算
    $avg_rating = 0;
    if (count($evaluations) > 0) {
        $total = array_sum(array_column($evaluations, 'rating'));
        $avg_rating = $total / count($evaluations);
    }
    
    json_success([
        'assignment' => $assignment,
        'evaluations' => $evaluations,
        'avg_rating' => $avg_rating
    ]);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
