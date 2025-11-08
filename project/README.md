# 課題管理・情報共有Webアプリ

## 📚 プロジェクト概要

大学の課題を管理・共有できるWebアプリケーション。
先輩・後輩が課題の難易度や情報を共有し、授業・年度ごとにトークルームで情報交換できます。

## 🚀 セットアップ手順

### 1. データベースのセットアップ

PostgreSQLに接続して、以下のSQLファイルを実行してください：

```bash
psql -h localhost -U nambo -d nambo -f setup_database.sql
```

または、psqlコマンドラインで：

```sql
\i setup_database.sql
```

### 2. ファイルのアップロード

SFTPを使用して、以下のディレクトリ構造でサーバーにアップロードします：

```
/home/your-username/public_html/
├── index.php
├── register.php
├── home.php
├── class-evaluation.php
├── logout.php
├── includes/
│   ├── config.php
│   ├── functions.php
│   └── session.php
├── api/
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── courses/
│   ├── years/
│   ├── assignments/
│   ├── assignment_evaluations/
│   ├── course_evaluations/
│   └── chat/
├── css/
│   ├── common.css
│   ├── login.css
│   ├── home.css
│   ├── class-evaluation.css
│   └── modal.css
└── js/
    ├── login.js
    ├── register.js
    ├── courses.js
    ├── years.js
    ├── assignments.js
    ├── assignment_evaluations.js
    ├── course_evaluations.js
    ├── chat.js
    └── dragdrop.js
```

### 3. アクセス

ブラウザで以下にアクセス：

```
http://your-server/index.php
```

### 4. 初期ログイン

**テストアカウント:**
- Email: `admin@university.ac.jp`
- Password: `admin123`

## 📁 ファイル構成

| ファイル | 説明 |
|---------|------|
| `index.php` | ログイン画面 |
| `register.php` | 新規登録画面 |
| `home.php` | Homeタブ（課題管理） |
| `class-evaluation.php` | Class Evaluationタブ（授業評価） |
| `includes/config.php` | データベース接続設定 |
| `includes/session.php` | セッション管理 |
| `includes/functions.php` | 共通関数 |
| `api/auth/login.php` | ログインAPI |
| `api/auth/register.php` | 新規登録API |

## 🔧 開発環境

- PHP 8.4.11
- PostgreSQL 12.3
- JavaScript (ES6+)

## 📝 主要機能

### 認証機能
- ✅ ログイン
- ✅ 新規登録
- ✅ ログアウト

### Homeタブ
- ⏳ 授業管理（一覧・追加・並び替え）
- ⏳ 年度管理（追加・トークルーム自動作成）
- ⏳ 課題管理（一覧・追加・完了・並び替え）
- ⏳ 課題評価（投稿・詳細表示）
- ⏳ トークルーム（チャット）

### Class Evaluationタブ
- ⏳ 授業評価（投稿・編集・削除）
- ⏳ 授業評価一覧表示

## 🐛 トラブルシューティング

### データベース接続エラー

`includes/config.php` のデータベース接続情報を確認してください：

```php
$host = "localhost";
$user = "nambo";
$password = "e6Q9JGJS";
$dbname = "nambo";
```

### ログインできない

1. データベースのテーブルが正しく作成されているか確認
2. 初期管理者アカウントが登録されているか確認：

```sql
SELECT * FROM users WHERE email = 'admin@university.ac.jp';
```

### 画面が真っ白

PHPエラーログを確認してください：

```bash
tail -f /var/log/php-error.log
```

## 📞 サポート

問題が発生した場合は、開発チームに連絡してください。

## 📄 ライセンス

このプロジェクトは学内専用です。

---

**作成日**: 2025年11月7日  
**バージョン**: 1.0  
**作成者**: 開発チーム
