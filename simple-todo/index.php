<?php
/**
 * ログイン画面
 * メンバーA担当
 */

require_once 'config.php';

// 既にログイン済みならTodo画面へ
if (isset($_SESSION['user_id'])) {
    header('Location: todo.php');
    exit;
}

$error = '';

// ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'メールアドレスとパスワードを入力してください';
    } else {
        // ユーザー情報を取得
        $stmt = $pdo->prepare('SELECT user_id, password_hash, username FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // ログイン成功
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header('Location: todo.php');
            exit;
        } else {
            $error = 'メールアドレスまたはパスワードが間違っています';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - Todoアプリ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1>📝 Todoアプリ</h1>
            <h2>ログイン</h2>

            <?php if ($error): ?>
                <div class="error-message"><?php echo h($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" id="email" name="email" required
                           placeholder="example@example.com">
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" required
                           placeholder="パスワードを入力">
                </div>

                <button type="submit" class="btn btn-primary">ログイン</button>
            </form>

            <p class="link-text">
                アカウントをお持ちでない方は <a href="register.php">新規登録</a>
            </p>
        </div>
    </div>
</body>
</html>
