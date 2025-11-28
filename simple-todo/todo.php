<?php
/**
 * Todo一覧・追加画面（メイン画面）
 * メンバーB担当
 */

require_once 'config.php';
checkLogin(); // ログインチェック

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Todo一覧を取得
$stmt = $pdo->prepare('
    SELECT todo_id, title, deadline, is_completed, created_at
    FROM todos
    WHERE user_id = ?
    ORDER BY is_completed ASC, deadline ASC NULLS LAST, created_at DESC
');
$stmt->execute([$user_id]);
$todos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo一覧 - Todoアプリ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>📝 Todoアプリ</h1>
            <div class="user-info">
                <span>ようこそ、<?php echo h($username); ?>さん！</span>
                <a href="profile.php" class="btn btn-secondary">マイページ</a>
                <a href="logout.php" class="btn btn-secondary">ログアウト</a>
            </div>
        </header>

        <!-- Todo追加フォーム -->
        <div class="todo-form-box">
            <h2>新しいTodoを追加</h2>
            <form id="add-todo-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">タイトル</label>
                        <input type="text" id="title" name="title" required
                               placeholder="例: レポート提出">
                    </div>

                    <div class="form-group">
                        <label for="deadline">期限</label>
                        <input type="date" id="deadline" name="deadline">
                    </div>

                    <button type="submit" class="btn btn-primary">追加</button>
                </div>
            </form>
        </div>

        <!-- Todo一覧 -->
        <div class="todo-list-box">
            <h2>Todo一覧</h2>

            <?php if (empty($todos)): ?>
                <p class="empty-message">Todoがありません。上のフォームから追加してください！</p>
            <?php else: ?>
                <table class="todo-table">
                    <thead>
                        <tr>
                            <th>状態</th>
                            <th>タイトル</th>
                            <th>期限</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody id="todo-list">
                        <?php foreach ($todos as $todo): ?>
                            <tr class="<?php echo $todo['is_completed'] ? 'completed' : ''; ?>"
                                data-todo-id="<?php echo $todo['todo_id']; ?>">
                                <td>
                                    <?php if ($todo['is_completed']): ?>
                                        <span class="status-badge completed">✓ 完了</span>
                                    <?php else: ?>
                                        <span class="status-badge">未完了</span>
                                    <?php endif; ?>
                                </td>
                                <td class="todo-title"><?php echo h($todo['title']); ?></td>
                                <td class="todo-deadline">
                                    <?php
                                    if ($todo['deadline']) {
                                        echo h(date('Y年m月d日', strtotime($todo['deadline'])));
                                    } else {
                                        echo '期限なし';
                                    }
                                    ?>
                                </td>
                                <td class="todo-actions">
                                    <?php if (!$todo['is_completed']): ?>
                                        <button class="btn btn-success btn-sm complete-btn"
                                                data-id="<?php echo $todo['todo_id']; ?>">
                                            完了
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-danger btn-sm delete-btn"
                                            data-id="<?php echo $todo['todo_id']; ?>">
                                        削除
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
