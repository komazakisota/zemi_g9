<?php
/**
 * セッション管理
 * 
 * ユーザーのログイン状態を管理
 */

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * ログインチェック
 * 
 * @return bool ログインしているか
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * ログインが必要なページの保護
 * 
 * ログインしていない場合、ログイン画面にリダイレクト
 */
function require_login() {
    if (!is_logged_in()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * 現在のユーザーIDを取得
 * 
 * @return int|null ユーザーID
 */
function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * 現在のユーザー名を取得
 * 
 * @return string|null ユーザー名
 */
function get_current_username() {
    return $_SESSION['username'] ?? null;
}

/**
 * 管理者かどうかチェック
 * 
 * @return bool 管理者か
 */
function is_admin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * ログイン処理
 * 
 * @param int $user_id ユーザーID
 * @param string $username ユーザー名
 * @param bool $is_admin 管理者フラグ
 */
function login_user($user_id, $username, $is_admin = false) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['is_admin'] = $is_admin;
    
    // セッションハイジャック対策
    session_regenerate_id(true);
}

/**
 * ログアウト処理
 */
function logout_user() {
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}
?>
