<?php
/**
 * チャットメッセージ送信API
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

if (!is_post()) {
    json_error('Invalid request method', 405);
}

$user_id = get_current_user_id();
$chat_room_id = post('chat_room_id');
$message = post('message');

if (empty($chat_room_id) || empty($message)) {
    json_error('必須項目を入力してください');
}

try {
    $stmt = $pdo->prepare('
        INSERT INTO chat_messages (chat_room_id, user_id, message)
        VALUES (?, ?, ?)
        RETURNING message_id
    ');
    $stmt->execute([$chat_room_id, $user_id, $message]);
    $result = $stmt->fetch();
    
    json_success([
        'message' => 'メッセージを送信しました',
        'message_id' => $result['message_id']
    ]);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
