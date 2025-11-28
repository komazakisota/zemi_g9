-- ==========================================
-- 課題管理・情報共有Webアプリ
-- データベースセットアップSQL
-- ==========================================

-- 既存のテーブルを削除（初回セットアップ時のみ）
DROP TABLE IF EXISTS chat_messages CASCADE;
DROP TABLE IF EXISTS chat_rooms CASCADE;
DROP TABLE IF EXISTS course_evaluations CASCADE;
DROP TABLE IF EXISTS assignment_evaluations CASCADE;
DROP TABLE IF EXISTS assignment_order CASCADE;
DROP TABLE IF EXISTS assignment_completion CASCADE;
DROP TABLE IF EXISTS assignments CASCADE;
DROP TABLE IF EXISTS course_years CASCADE;
DROP TABLE IF EXISTS user_course_order CASCADE;
DROP TABLE IF EXISTS user_courses CASCADE;
DROP TABLE IF EXISTS courses CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- ==========================================
-- テーブル作成
-- ==========================================

-- 1. usersテーブル（ユーザー情報）
CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. coursesテーブル（授業情報）
CREATE TABLE courses (
    course_id SERIAL PRIMARY KEY,
    course_name VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. user_coursesテーブル（ユーザーと授業の紐付け）
CREATE TABLE user_courses (
    user_course_id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    course_id INTEGER NOT NULL REFERENCES courses(course_id) ON DELETE CASCADE,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, course_id)
);

-- 4. user_course_orderテーブル（授業の表示順）
CREATE TABLE user_course_order (
    order_id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    course_id INTEGER NOT NULL REFERENCES courses(course_id) ON DELETE CASCADE,
    display_order INTEGER NOT NULL,
    UNIQUE(user_id, course_id)
);

-- 5. course_yearsテーブル（授業の年度）
CREATE TABLE course_years (
    course_year_id SERIAL PRIMARY KEY,
    course_id INTEGER NOT NULL REFERENCES courses(course_id) ON DELETE CASCADE,
    year INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(course_id, year)
);

-- 6. assignmentsテーブル（課題情報）
CREATE TABLE assignments (
    assignment_id SERIAL PRIMARY KEY,
    course_year_id INTEGER NOT NULL REFERENCES course_years(course_year_id) ON DELETE CASCADE,
    created_by INTEGER NOT NULL REFERENCES users(user_id) ON DELETE SET NULL,
    assignment_name VARCHAR(255) NOT NULL,
    deadline TIMESTAMP NULL,
    has_time BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(course_year_id, assignment_name)
);

-- 7. assignment_completionテーブル（課題完了状態）
CREATE TABLE assignment_completion (
    completion_id SERIAL PRIMARY KEY,
    assignment_id INTEGER NOT NULL REFERENCES assignments(assignment_id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    is_completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP,
    UNIQUE(assignment_id, user_id)
);

-- 8. assignment_orderテーブル（課題の表示順）
CREATE TABLE assignment_order (
    order_id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    course_year_id INTEGER NOT NULL REFERENCES course_years(course_year_id) ON DELETE CASCADE,
    assignment_id INTEGER NOT NULL REFERENCES assignments(assignment_id) ON DELETE CASCADE,
    display_order INTEGER NOT NULL,
    UNIQUE(user_id, course_year_id, assignment_id)
);

-- 9. assignment_evaluationsテーブル（課題評価）
CREATE TABLE assignment_evaluations (
    evaluation_id SERIAL PRIMARY KEY,
    assignment_id INTEGER NOT NULL REFERENCES assignments(assignment_id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(assignment_id, user_id)
);

-- 10. course_evaluationsテーブル（授業評価）
CREATE TABLE course_evaluations (
    course_evaluation_id SERIAL PRIMARY KEY,
    course_id INTEGER NOT NULL REFERENCES courses(course_id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(course_id, user_id)
);

-- 11. chat_roomsテーブル（トークルーム）
CREATE TABLE chat_rooms (
    chat_room_id SERIAL PRIMARY KEY,
    course_year_id INTEGER UNIQUE NOT NULL REFERENCES course_years(course_year_id) ON DELETE CASCADE,
    room_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 12. chat_messagesテーブル（チャットメッセージ）
CREATE TABLE chat_messages (
    message_id SERIAL PRIMARY KEY,
    chat_room_id INTEGER NOT NULL REFERENCES chat_rooms(chat_room_id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- インデックス作成（パフォーマンス向上）
-- ==========================================

CREATE INDEX idx_user_courses_user_id ON user_courses(user_id);
CREATE INDEX idx_user_courses_course_id ON user_courses(course_id);
CREATE INDEX idx_assignments_course_year_id ON assignments(course_year_id);
CREATE INDEX idx_assignments_deadline ON assignments(deadline);
CREATE INDEX idx_assignment_completion_user_id ON assignment_completion(user_id);
CREATE INDEX idx_assignment_evaluations_assignment_id ON assignment_evaluations(assignment_id);
CREATE INDEX idx_course_evaluations_course_id ON course_evaluations(course_id);
CREATE INDEX idx_chat_messages_chat_room_id ON chat_messages(chat_room_id);
CREATE INDEX idx_chat_messages_created_at ON chat_messages(created_at);

-- ==========================================
-- 初期データ投入
-- ==========================================

-- 初期管理者アカウント（パスワード: admin123）
INSERT INTO users (email, username, password_hash, is_admin) VALUES
('admin@university.ac.jp', '管理者', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);

-- テスト用授業データ
INSERT INTO courses (course_name, description) VALUES
('2025_web-db', 'Webアプリケーション開発とデータベース'),
('データと経済統計', 'データ分析と統計学の基礎');

-- テスト用年度データ
INSERT INTO course_years (course_id, year) VALUES
(1, 2024), (1, 2025),
(2, 2024), (2, 2025);

-- トークルーム自動作成
INSERT INTO chat_rooms (course_year_id, room_name) VALUES
(1, '2025_web-db 2024年度 トークルーム'),
(2, '2025_web-db 2025年度 トークルーム'),
(3, 'データと経済統計 2024年度 トークルーム'),
(4, 'データと経済統計 2025年度 トークルーム');

-- テスト用課題データ
INSERT INTO assignments (course_year_id, created_by, assignment_name, deadline, has_time) VALUES
(2, 1, '最終課題', '2025-11-15 23:59:00', TRUE),
(2, 1, '第1回・第2回 授業資料', '2025-09-25 00:00:00', FALSE),
(2, 1, '中間レポート', NULL, FALSE);

-- ==========================================
-- セットアップ完了確認
-- ==========================================

SELECT 'データベースセットアップが完了しました！' AS message;
SELECT COUNT(*) AS user_count FROM users;
SELECT COUNT(*) AS course_count FROM courses;
SELECT COUNT(*) AS assignment_count FROM assignments;
