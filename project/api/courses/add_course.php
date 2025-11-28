<?php
/**
 * 授業追加API
 * 
 * POST /api/courses/add_course.php
 * パラメータ: course_name, description
 */

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/functions.php';

// ログインチェック
if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

// POSTリクエストのみ許可
if (!is_post()) {
    json_error('Invalid request method', 405);
}

$user_id = get_current_user_id();
$course_name = post('course_name');
$description = post('description');

// バリデーション
if (empty($course_name)) {
    json_error('授業名を入力してください');
}

try {
    $pdo->beginTransaction();
    
    // 重複チェック
    $stmt = $pdo->prepare('SELECT course_id FROM courses WHERE course_name = ?');
    $stmt->execute([$course_name]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // 既存の授業がある場合は、それを登録
        $course_id = $existing['course_id'];
        
        // 既に登録済みかチェック
        $stmt = $pdo->prepare('SELECT user_course_id FROM user_courses WHERE user_id = ? AND course_id = ?');
        $stmt->execute([$user_id, $course_id]);
        
        if ($stmt->fetch()) {
            json_error('この授業は既に登録されています');
        }
    } else {
        // 新しい授業を作成
        $stmt = $pdo->prepare('
            INSERT INTO courses (course_name, description)
            VALUES (?, ?)
            RETURNING course_id
        ');
        $stmt->execute([$course_name, $description]);
        $course = $stmt->fetch();
        $course_id = $course['course_id'];
    }
    
    // ユーザーと授業を紐付け
    $stmt = $pdo->prepare('
        INSERT INTO user_courses (user_id, course_id)
        VALUES (?, ?)
    ');
    $stmt->execute([$user_id, $course_id]);
    
    $pdo->commit();
    
    json_success([
        'message' => '授業を追加しました',
        'course_id' => $course_id
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
