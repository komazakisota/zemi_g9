<?php
/**
 * 新規登録画面
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
    <title>新規登録 - 課題管理システム</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>課題管理システム</h1>
            <p class="subtitle">新規登録</p>
            
            <div id="error-message" class="error-message" style="display: none;"></div>
            <div id="success-message" class="success-message" style="display: none;"></div>
            
            <form id="register-form">
                <div class="form-group">
                    <label for="username">ユーザー名</label>
                    <input type="text" id="username" name="username" required 
                           placeholder="山田太郎">
                </div>
                
                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="example@university.ac.jp">
                </div>
                
                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" required
                           placeholder="8文字以上" minlength="8">
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">パスワード（確認）</label>
                    <input type="password" id="password_confirm" name="password_confirm" required
                           placeholder="もう一度入力" minlength="8">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">登録</button>
            </form>
            
            <p class="register-link">
                既にアカウントをお持ちの方は <a href="index.php">こちら</a>
            </p>
        </div>
    </div>
    
    <script src="../assets/js/register.js"></script>
</body>
</html>
