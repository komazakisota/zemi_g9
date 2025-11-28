<?php
/**
 * API処理（Todo追加・完了・削除）
 * メンバーB・C担当
 */

require_once 'config.php';
checkLogin(); // ログインチェック

header('Content-Type: application/json; charset=UTF-8');

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

try {
    // Todo追加（メンバーB担当）
    if ($action === 'add') {
        $title = $_POST['title'] ?? '';
        $deadline = $_POST['deadline'] ?? null;

        if (empty($title)) {
            echo json_encode(['success' => false, 'error' => 'タイトルを入力してください']);
            exit;
        }

        $stmt = $pdo->prepare('INSERT INTO todos (user_id, title, deadline) VALUES (?, ?, ?)');
        $stmt->execute([$user_id, $title, $deadline ?: null]);

        echo json_encode(['success' => true, 'message' => 'Todoを追加しました']);

    // Todo完了（メンバーC担当）
    } elseif ($action === 'complete') {
        $todo_id = $_POST['todo_id'] ?? 0;

        $stmt = $pdo->prepare('UPDATE todos SET is_completed = TRUE WHERE todo_id = ? AND user_id = ?');
        $stmt->execute([$todo_id, $user_id]);

        echo json_encode(['success' => true, 'message' => 'Todoを完了しました']);

    // Todo削除（メンバーC担当）
    } elseif ($action === 'delete') {
        $todo_id = $_POST['todo_id'] ?? 0;

        $stmt = $pdo->prepare('DELETE FROM todos WHERE todo_id = ? AND user_id = ?');
        $stmt->execute([$todo_id, $user_id]);

        echo json_encode(['success' => true, 'message' => 'Todoを削除しました']);

    } else {
        echo json_encode(['success' => false, 'error' => '無効なアクション']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'データベースエラー: ' . $e->getMessage()]);
}
?>
