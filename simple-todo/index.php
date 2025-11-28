<?php
/**
 * ログインAPI
 * * POST /api/auth/login.php
 * パラメータ: email, password
 */

// ▼▼▼ 1. 設定ファイルの読み込み ▼▼▼
// データベース接続情報($pdo)を使いたいので読み込みます
// パスは実際のフォルダ構成に合わせて調整してください
require_once __DIR__ . '/../../core/config.php';

// ▼▼▼ 2. セッション開始 ▼▼▼
// 外部ファイルを使わずここで開始します
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ▼▼▼ 3. ヘルパー関数の定義（このファイル内だけで使う用） ▼▼▼
// JSONを返して終了する関数をここで定義してしまいます
function send_json_response($data, $code = 200) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($code);
    echo json_encode($data);
    exit;
}

// --- メイン処理開始 ---

// POSTリクエストのみ許可 (is_post() の代わり)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['status' => 'error', 'message' => 'Invalid request method'], 405);
}

// パラメータ取得 (post() の代わり)
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// バリデーション
if (empty($email) || empty($password)) {
    send_json_response(['status' => 'error', 'message' => 'メールアドレスとパスワードを入力してください'], 400);
}

try {
    // ユーザーを検索
    // config.php で生成された $pdo を使用します
    $stmt = $pdo->prepare('
        SELECT user_id, username, password_hash, is_admin
        FROM users
        WHERE email = ?
    ');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // ユーザーが存在しない or パスワードが一致しない
    if (!$user || !password_verify($password, $user['password_hash'])) {
        // セキュリティのため「メアドかパスワードか」はぼかして伝えます
        send_json_response(['status' => 'error', 'message' => 'メールアドレスまたはパスワードが正しくありません'], 401);
    }
    
    // ▼▼▼ 4. ログイン処理 (login_user() の代わり) ▼▼▼
    // セッションIDの再生成（セッションハイジャック対策）
    session_regenerate_id(true);
    
    // セッション変数に保存
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['is_admin'] = $user['is_admin'];
    $_SESSION['logged_in'] = true;
    
    // 成功レスポンス
    send_json_response([
        'status' => 'success',
        'message' => 'ログインに成功しました',
        'user_id' => $user['user_id'],
        'username' => $user['username']
    ]);
    
} catch (PDOException $e) {
    // 本番環境ではエラー詳細はログに残し、ユーザーには汎用メッセージを返すのが安全です
    // error_log($e->getMessage()); 
    send_json_response(['status' => 'error', 'message' => 'データベースエラーが発生しました'], 500);
}
?>