<?php
/**
 * トークルーム取得API
 */

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/functions.php';

if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

$course_year_id = get('course_year_id');

if (empty($course_year_id)) {
    json_error('course_year_idが必要です');
}

try {
    $stmt = $pdo->prepare('
        SELECT chat_room_id, room_name
        FROM chat_rooms
        WHERE course_year_id = ?
    ');
    $stmt->execute([$course_year_id]);
    $chatRoom = $stmt->fetch();
    
    if (!$chatRoom) {
        json_error('トークルームが見つかりません');
    }
    
    json_success($chatRoom);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
