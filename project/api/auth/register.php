<?php
/**
 * 新規登録API
 * 
 * POST /api/auth/register.php
 * パラメータ: username, email, password
 */

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/functions.php';

// POSTリクエストのみ許可
if (!is_post()) {
    json_error('Invalid request method', 405);
}

// パラメータ取得
$username = post('username');
$email = post('email');
$password = post('password');

// バリデーション
if (empty($username) || empty($email) || empty($password)) {
    json_error('すべての項目を入力してください');
}

if (strlen($password) < 8) {
    json_error('パスワードは8文字以上で入力してください');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_error('有効なメールアドレスを入力してください');
}

try {
    // メールアドレスの重複チェック
    $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        json_error('このメールアドレスは既に登録されています');
    }
    
    // パスワードをハッシュ化
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // ユーザーを登録
    $stmt = $pdo->prepare('
        INSERT INTO users (email, username, password_hash)
        VALUES (?, ?, ?)
        RETURNING user_id
    ');
    $stmt->execute([$email, $username, $password_hash]);
    $user = $stmt->fetch();
    
    json_success([
        'message' => '登録が完了しました',
        'user_id' => $user['user_id']
    ]);
    
} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>
