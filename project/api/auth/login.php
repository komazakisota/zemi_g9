<?php
/**
 * ログインAPI
 * 
 * POST /api/auth/login.php
 * パラメータ: email, password
 */

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/functions.php';

// POSTリクエストのみ許可
if (!is_post()) {
    json_error('Invalid request method', 405);
}

// パラメータ取得
$email = post('email');
$password = post('password');

// バリデーション
if (empty($email) || empty($password)) {
    json_error('メールアドレスとパスワードを入力してください');
}

try {
    // ユーザーを検索
    $stmt = $pdo->prepare('
        SELECT user_id, username, password_hash, is_admin
        FROM users
        WHERE email = ?
    ');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // ユーザーが存在しない or パスワードが一致しない
    if (!$user || !password_verify($password, $user['password_hash'])) {
        json_error('メールアドレスまたはパスワードが正しくありません');
    }
    
    // ログイン処理
    login_user($user['user_id'], $user['username'], $user['is_admin']);
    
    json_success([
        'message' => 'ログインに成功しました',
        'user_id' => $user['user_id'],
        'username' => $user['username']
    ]);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
