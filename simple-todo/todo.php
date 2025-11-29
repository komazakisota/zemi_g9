<?php
// 1. 設定ファイルを読み込んでDBに接続
require_once 'config.php'; 

// もし「POST」でデータが送られてきたら（＝追加ボタンが押されたら）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $deadline = $_POST['deadline'];
    $user_id = 1; 

    $sql = "INSERT INTO todos (user_id, title, deadline, is_completed, created_at) 
            VALUES (:user_id, :title, :deadline, false, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':deadline', $deadline, PDO::PARAM_STR);
    $stmt->execute();
    
    header("Location: todo.php");
    exit;
}

// 2. データを取得
$sql = "SELECT * FROM todos ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$todos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Todo一覧</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Todoリスト</h1>

        <div class="input-area">
            <form action="todo.php" method="post">
                <label>タイトル: <input type="text" name="title" required></label>
                <label>期限: <input type="date" name="deadline" required></label>
                <button type="submit">追加 +</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>タイトル</th>
                    <th>期限</th>
                    <th>状態</th>
                    <th>操作</th> </tr>
            </thead>
            <tbody>
            <?php foreach ($todos as $todo): ?>
            <tr>
                <td><?php echo htmlspecialchars($todo['title']); ?></td>
                <td><?php echo htmlspecialchars($todo['deadline']); ?></td>
                <td>
                    <?php if ($todo['is_completed']): ?>
                        <span style="color: #00e5ff;">完了</span>
                    <?php else: ?>
                        <span>未完了</span>
                    <?php endif; ?>
                </td>
                <td>
                    <button class="complete-btn">完了</button>
                    <button class="delete-btn">削除</button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        
    </div> </body>
</html>