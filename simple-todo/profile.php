<?php
session_start();
require_once 'config.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error_message = '';
$success_message = '';

// プロフィール更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    
    if (empty($username) || empty($email)) {
        $error_message = 'Username and email are required.';
    } else {
        try {
            // ユーザー名とメールアドレスの重複チェック（現在のユーザー以外）
            $stmt = $pdo->prepare(
                'SELECT user_id FROM users WHERE (username = :username OR email = :email) AND user_id != :user_id'
            );
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':user_id' => $user_id,
            ]);
            
            if ($stmt->fetch()) {
                $error_message = 'Username or email already exists.';
            } else {
                // プロフィール更新
                $stmt = $pdo->prepare('UPDATE users SET username = :username, email = :email WHERE user_id = :user_id');
                $stmt->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':user_id' => $user_id,
                ]);
                $success_message = 'Profile updated successfully!';
                $_SESSION['username'] = $username;
            }
        } catch (PDOException $e) {
            error_log('Profile update failed: ' . $e->getMessage());
            $error_message = 'An unexpected error occurred. Please try again later.';
        }
    }
}

// ユーザー情報取得
try {
    $stmt = $pdo->prepare('SELECT user_id, username, email, created_at FROM users WHERE user_id = :user_id');
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        header('Location: logout.php');
        exit();
    }
} catch (PDOException $e) {
    error_log('Profile fetch failed: ' . $e->getMessage());
    $error_message = 'Unable to load profile at the moment. Please try again later.';
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Simple Todo</title>
</head>
<body>
    <?php if ($user): ?>
        <h1>Profile of <?php echo htmlspecialchars($user['username']); ?></h1>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Member since:</strong> <?php echo date('Y-m-d', strtotime($user['created_at'])); ?></p>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <h2>Edit Profile</h2>
    <?php if ($user): ?>
    <form action="profile.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        
        <input type="submit" value="Update Profile" class="btn-primary">
    </form>
    <?php endif; ?>
    
    <div class="navigation">
        <a href="todo.php">Back to Todo List</a>
        <a href="logout.php">Logout</a>
    </div>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
            color: #333;
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }

        h2 {
            color: #34495e;
            margin-top: 30px;
            margin-bottom: 20px;
        }

        p {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus, input[type="email"]:focus {
            border-color: #3498db;
            outline: none;
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .navigation {
            margin-top: 30px;
            text-align: center;
        }

        .navigation a {
            display: inline-block;
            margin: 0 15px;
            padding: 10px 20px;
            text-decoration: none;
            color: #3498db;
            border: 2px solid #3498db;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .navigation a:hover {
            background-color: #3498db;
            color: white;
        }

        .error {
            background-color: #e74c3c;
            color: white;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }

        .success {
            background-color: #27ae60;
            color: white;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }

        form {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</body>
</html>