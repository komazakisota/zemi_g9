<?php
// 1. 設定ファイルを読み込んでDBに接続
require_once 'config.php'; // 先生または担当者が作ったファイル

// もし「POST」でデータが送られてきたら（＝追加ボタンが押されたら）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームの入力を受け取る
    $title = $_POST['title'];
    $deadline = $_POST['deadline'];
    // ユーザーIDは仮で「1」にしておく（ログイン機能と合体するまで）
    $user_id = 1; 

    // データベースに追加するSQL
    $sql = "INSERT INTO todos (user_id, title, deadline, is_completed, created_at) 
            VALUES (:user_id, :title, :deadline, false, NOW())";
    
    $stmt = $pdo->prepare($sql);
    
    // データをセットして実行！
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':deadline', $deadline, PDO::PARAM_STR);
    $stmt->execute();
    
    // 再読み込みして二重送信を防ぐ（PRGパターン）
    header("Location: todo.php");
    exit;
}

// 2. データを取得するSQLを準備
// ※本来は "WHERE user_id = ?" で自分のだけ絞り込むけど、まずは全員分表示してみよう
$sql = "SELECT * FROM todos ORDER BY created_at DESC";
$stmt = $pdo->query($sql);

// 3. データを全件取ってくる
$todos = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Todo一覧</title>
</head>
<body>
    <h1>Todoリスト</h1>

    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;">
        <h2>新しいタスクを追加</h2>
        <form action="todo.php" method="post">
            <label>タイトル: <input type="text" name="title" required></label>
            <label>期限: <input type="date" name="deadline" required></label>
            <button type="submit">追加する</button>
        </form>
    </div>

    <table border="1" width="100%">
        <thead>
            <tr>
                <th>タイトル</th>
                <th>期限</th>
                <th>状態</th>
            </tr>
        </thead>
        <tbody>
           <?php foreach ($todos as $todo): ?>
        <tr>
            <td><?php echo htmlspecialchars($todo['title']); ?></td>
            <td><?php echo htmlspecialchars($todo['deadline']); ?></td>
            <td>
                <?php echo $todo['is_completed'] ? '完了' : '未完了'; ?>
            </td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>