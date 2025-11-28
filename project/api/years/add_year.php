<?php
/**
 * 年度追加API
 * 
 * POST /api/years/add_year.php
 * パラメータ: course_id, year
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

$course_id = post('course_id');
$year = post('year');

// バリデーション
if (empty($course_id) || empty($year)) {
    json_error('授業IDと年度を入力してください');
}

try {
    $pdo->beginTransaction();
    
    // 重複チェック
    $stmt = $pdo->prepare('
        SELECT course_year_id FROM course_years
        WHERE course_id = ? AND year = ?
    ');
    $stmt->execute([$course_id, $year]);
    
    if ($stmt->fetch()) {
        json_error('この年度は既に登録されています');
    }
    
    // 年度を追加
    $stmt = $pdo->prepare('
        INSERT INTO course_years (course_id, year)
        VALUES (?, ?)
        RETURNING course_year_id
    ');
    $stmt->execute([$course_id, $year]);
    $result = $stmt->fetch();
    $course_year_id = $result['course_year_id'];
    
    // 授業名を取得
    $stmt = $pdo->prepare('SELECT course_name FROM courses WHERE course_id = ?');
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();
    $course_name = $course['course_name'];
    
    // トークルームを自動作成
    $room_name = $course_name . ' ' . $year . '年度 トークルーム';
    $stmt = $pdo->prepare('
        INSERT INTO chat_rooms (course_year_id, room_name)
        VALUES (?, ?)
    ');
    $stmt->execute([$course_year_id, $room_name]);
    
    $pdo->commit();
    
    json_success([
        'message' => '年度を追加しました（トークルームも作成されました）',
        'course_year_id' => $course_year_id
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
