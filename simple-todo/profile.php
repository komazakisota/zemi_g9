<?php
/**
 * マイページ（プロフィール編集）
 * メンバーD担当
 */

require_once 'config.php';
checkLogin(); // ログインチェック

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// ユーザー情報を取得
$stmt = $pdo->prepare('SELECT email, username FROM users WHERE user_id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// プロフィール更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['username'] ?? '';

    if (empty($new_username)) {
        $error = 'ユーザー名を入力してください';
    } else {
        $stmt = $pdo->prepare('UPDATE users SET username = ? WHERE user_id = ?');

        if ($stmt->execute([$new_username, $user_id])) {
            $_SESSION['username'] = $new_username;
            $user['username'] = $new_username;
            $success = 'ユーザー名を更新しました！';
        } else {
            $error = '更新に失敗しました';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ - Todoアプリ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="profile-box">
            <h1>マイページ</h1>

            <?php if ($error): ?>
                <div class="error-message"><?php echo h($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo h($success); ?></div>
            <?php endif; ?>

            <div class="profile-info">
                <h2>プロフィール情報</h2>

                <div class="info-row">
                    <label>メールアドレス:</label>
                    <span><?php echo h($user['email']); ?></span>
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label for="username">ユーザー名</label>
                        <input type="text" id="username" name="username" required
                               value="<?php echo h($user['username']); ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">更新</button>
                </form>
            </div>

            <div class="profile-actions">
                <a href="todo.php" class="btn btn-secondary">Todoに戻る</a>
            </div>
        </div>
    </div>
</body>
</html>
