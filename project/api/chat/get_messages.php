<?php
/**
 * チャットメッセージ取得API
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

$chat_room_id = get('chat_room_id');

if (empty($chat_room_id)) {
    json_error('chat_room_idが必要です');
}

try {
    $stmt = $pdo->prepare('
        SELECT 
            cm.message_id,
            cm.message,
            cm.created_at,
            u.username
        FROM chat_messages cm
        JOIN users u ON cm.user_id = u.user_id
        WHERE cm.chat_room_id = ?
        ORDER BY cm.created_at ASC
        LIMIT 100
    ');
    $stmt->execute([$chat_room_id]);
    $messages = $stmt->fetchAll();
    
    json_success(['messages' => $messages]);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
