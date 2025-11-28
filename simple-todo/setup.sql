-- ==========================================
-- 一年生向け Todoアプリ
-- データベースセットアップSQL
-- ==========================================

-- 既存のテーブルを削除（初回セットアップ時のみ）
DROP TABLE IF EXISTS todos CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- ==========================================
-- テーブル作成
-- ==========================================

-- 1. usersテーブル（ユーザー情報）
CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    username VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. todosテーブル（Todo情報）
CREATE TABLE todos (
    todo_id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    deadline DATE,
    is_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- インデックス作成（検索を速くする）
-- ==========================================

CREATE INDEX idx_todos_user_id ON todos(user_id);
CREATE INDEX idx_todos_is_completed ON todos(is_completed);

-- ==========================================
-- テストデータ投入（動作確認用）
-- ==========================================

-- テストユーザー（パスワード: test123）
INSERT INTO users (email, password_hash, username) VALUES
('test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'テストユーザー');

-- テストTodo
INSERT INTO todos (user_id, title, deadline, is_completed) VALUES
(1, 'レポート提出', '2025-12-01', FALSE),
(1, '課題A完了', '2025-11-20', TRUE);

-- ==========================================
-- セットアップ完了確認
-- ==========================================

SELECT 'データベースセットアップが完了しました！' AS message;
SELECT COUNT(*) AS user_count FROM users;
SELECT COUNT(*) AS todo_count FROM todos;
