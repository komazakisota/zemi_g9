/**
 * Todoアプリ JavaScript
 * メンバーC担当
 */

// ページ読み込み時に実行
document.addEventListener('DOMContentLoaded', function() {
    // Todo追加フォーム
    const addForm = document.getElementById('add-todo-form');
    if (addForm) {
        addForm.addEventListener('submit', handleAddTodo);
    }

    // 完了ボタン
    const completeButtons = document.querySelectorAll('.complete-btn');
    completeButtons.forEach(btn => {
        btn.addEventListener('click', handleCompleteTodo);
    });

    // 削除ボタン
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', handleDeleteTodo);
    });
});

/**
 * Todo追加処理
 */
async function handleAddTodo(e) {
    e.preventDefault(); // フォーム送信をキャンセル

    const title = document.getElementById('title').value;
    const deadline = document.getElementById('deadline').value;

    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=add&title=${encodeURIComponent(title)}&deadline=${encodeURIComponent(deadline)}`
        });

        const data = await response.json();

        if (data.success) {
            // 成功したらページをリロード
            alert('Todoを追加しました！');
            location.reload();
        } else {
            alert('エラー: ' + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('通信エラーが発生しました');
    }
}

/**
 * Todo完了処理
 */
async function handleCompleteTodo(e) {
    const todoId = e.target.dataset.id;

    if (!confirm('このTodoを完了にしますか？')) {
        return;
    }

    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=complete&todo_id=${todoId}`
        });

        const data = await response.json();

        if (data.success) {
            // 成功したらページをリロード
            location.reload();
        } else {
            alert('エラー: ' + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('通信エラーが発生しました');
    }
}

/**
 * Todo削除処理
 */
async function handleDeleteTodo(e) {
    const todoId = e.target.dataset.id;

    if (!confirm('本当に削除しますか？')) {
        return;
    }

    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&todo_id=${todoId}`
        });

        const data = await response.json();

        if (data.success) {
            // 成功したらページをリロード
            location.reload();
        } else {
            alert('エラー: ' + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('通信エラーが発生しました');
    }
}
