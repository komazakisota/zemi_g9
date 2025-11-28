<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規登録</title>
</head>
<body>
    <h2>新規ユーザー登録</h2>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <form action="api.php" method="POST">
        <input type="hidden" name="action" value="register">
        
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        
        <button type="submit">登録する</button>
    </form>
    <p><a href="index.php">ログイン画面へ戻る</a></p>
</body>
</html>