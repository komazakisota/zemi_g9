<?php
/**
 * 新規登録画面
 * メンバーA担当
 */

require_once 'config.php';

// 既にログイン済みならTodo画面へ
if (isset($_SESSION['user_id'])) {
    header('Location: todo.php');
    exit;
}

$error = '';
$success = '';

// 新規登録処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $username = $_POST['username'] ?? '';

    // バリデーション
    if (empty($email) || empty($password) || empty($username)) {
        $error = '全ての項目を入力してください';
    } elseif (strlen($password) < 6) {
        $error = 'パスワードは6文字以上にしてください';
    } else {
        // メールアドレスの重複チェック
        $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'このメールアドレスは既に登録されています';
        } else {
            // ユーザー登録
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, username) VALUES (?, ?, ?)');

            if ($stmt->execute([$email, $password_hash, $username])) {
                $success = '登録が完了しました！ログインしてください。';
            } else {
                $error = '登録に失敗しました';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録 - Todoアプリ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1>📝 Todoアプリ</h1>
            <h2>新規登録</h2>

            <?php if ($error): ?>
                <div class="error-message"><?php echo h($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo h($success); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">ユーザー名</label>
                    <input type="text" id="username" name="username" required
                           placeholder="山田太郎">
                </div>

                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" id="email" name="email" required
                           placeholder="example@example.com">
                </div>

                <div class="form-group">
                    <label for="password">パスワード（6文字以上）</label>
                    <input type="password" id="password" name="password" required
                           placeholder="パスワードを入力">
                </div>

                <button type="submit" class="btn btn-primary">登録</button>
            </form>

            <p class="link-text">
                既にアカウントをお持ちの方は <a href="index.php">ログイン</a>
            </p>
        </div>
    </div>
</body>
</html>
