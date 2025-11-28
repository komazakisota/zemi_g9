## 🗄️ データベース構造（超シンプル！2テーブルだけ）

### テーブル1: users（ユーザー情報）

| カラム名 | 型 | 説明 |
|---------|-----|------|
| user_id | SERIAL PRIMARY KEY | ユーザーID（自動採番） |
| email | VARCHAR(255) UNIQUE | メールアドレス |
| password_hash | VARCHAR(255) | パスワード（ハッシュ化） |
| username | VARCHAR(100) | ユーザー名 |
| created_at | TIMESTAMP | 登録日時 |

### テーブル2: todos（Todo情報）

| カラム名 | 型 | 説明 |
|---------|-----|------|
| todo_id | SERIAL PRIMARY KEY | TodoID（自動採番） |
| user_id | INTEGER | ユーザーID（外部キー） |
| title | VARCHAR(255) | Todoのタイトル |
| deadline | DATE | 期限 |
| is_completed | BOOLEAN | 完了フラグ |
| created_at | TIMESTAMP | 作成日時 |

**リレーション**: users.user_id ← todos.user_id（1対多）
