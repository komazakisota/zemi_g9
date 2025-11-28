<?php
require_once 'config.php';

// 既にログイン済みならプロフィールへ
if (isset($_SESSION['user_id'])) {
    header('Location: todo.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログインテスト</title>
</head>
<body>
    <h2>ログインAPIテスト</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES); ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="api.php" method="POST">
        <input type="hidden" name="action" value="login">

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        
        <button type="submit">ログインする</button>
    </form>

    <p><a href="register.php">新規登録はこちら</a></p>
</body>
</html>