<?php
/**
 * ログイン画面
 */

require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/functions.php';

// 既にログイン済みの場合はホームへ
if (is_logged_in()) {
    header('Location: home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - 課題管理システム</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>課題管理システム</h1>
            <p class="subtitle">ログイン</p>
            
            <div id="error-message" class="error-message" style="display: none;"></div>
            
            <form id="login-form">
                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="example@university.ac.jp">
                </div>
                
                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" required
                           placeholder="パスワードを入力">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">ログイン</button>
            </form>
            
            <p class="register-link">
                アカウントをお持ちでない方は <a href="register.php">こちら</a>
            </p>
            
        </div>
    </div>
    
    <script src="../assets/js/login.js"></script>
</body>
</html>
