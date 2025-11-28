<?php
/**
 * 課題追加API
 * 
 * POST /api/assignments/add_assignment.php
 */

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/functions.php';

if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

if (!is_post()) {
    json_error('Invalid request method', 405);
}

$user_id = get_current_user_id();
$course_year_id = post('course_year_id');
$assignment_name = post('assignment_name');
$deadline = post('deadline');
$has_time = post('has_time') === '1' ? true : false;

if (empty($course_year_id) || empty($assignment_name)) {
    json_error('必須項目を入力してください');
}

try {
    // 重複チェック
    $stmt = $pdo->prepare('
        SELECT assignment_id FROM assignments
        WHERE course_year_id = ? AND assignment_name = ?
    ');
    $stmt->execute([$course_year_id, $assignment_name]);
    
    if ($stmt->fetch()) {
        json_error('この課題は既に登録されています');
    }
    
    // 課題を追加
    $stmt = $pdo->prepare('
        INSERT INTO assignments (course_year_id, created_by, assignment_name, deadline, has_time)
        VALUES (?, ?, ?, ?, ?)
        RETURNING assignment_id
    ');
    
    $deadline_value = empty($deadline) ? null : $deadline;
    $has_time_value = $has_time ? 'true' : 'false';
    
    $stmt->execute([
        $course_year_id,
        $user_id,
        $assignment_name,
        $deadline_value,
        $has_time_value
    ]);
    
    $result = $stmt->fetch();
    
    json_success([
        'message' => '課題を追加しました',
        'assignment_id' => $result['assignment_id']
    ]);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>