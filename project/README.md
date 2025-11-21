# DS授業・課題管理システム

## 📚 プロジェクト概要

データサイエンス学部専用の課題管理・情報共有Webアプリケーション。
学生同士が授業・課題の情報を共有し、過去の履修者から難易度やアドバイスを得られるシステムです。

### 主な特徴

- **授業・課題の一元管理**: 年度ごとに授業と課題を管理
- **予測変換機能**: 他の学生が登録済みの授業名・課題名を候補として表示（重複防止）
- **評価システム**: 課題の難易度を5段階評価でシェア
- **トークルーム**: 授業・年度ごとのチャット機能で情報交換
- **直感的なUI**: ドラッグ&ドロップで並び替え可能

## 🚀 セットアップ

### 1. データベース初期化

```bash
psql -h localhost -U your-user -d your-db -f setup_database.sql
```

### 2. データベース接続設定

`includes/config.php` を編集：

```php
$host = "localhost";
$user = "your-username";
$password = "your-password";
$dbname = "your-database";
```

### 3. アクセス

ブラウザで `index.php` にアクセスしてログイン画面を開きます。

### 初期アカウント

- **Email**: `admin@university.ac.jp`
- **Password**: `admin123`

## 📁 ディレクトリ構成

```
project/
├── api/                    # REST API
│   ├── auth/              # 認証
│   ├── courses/           # 授業管理
│   ├── assignments/       # 課題管理
│   ├── course_evaluations/ # 授業評価
│   ├── assignment_evaluations/ # 課題評価
│   ├── years/             # 年度管理
│   └── chat/              # チャット
├── css/                   # スタイルシート
├── js/                    # JavaScript
├── includes/              # 共通PHP（設定・関数）
├── index.php              # ログイン画面
├── register.php           # 新規登録画面
├── home.php               # 課題管理画面
├── class-evaluation.php   # 授業評価画面
└── setup_database.sql     # DB初期化スクリプト
```

## 📝 主要機能

### 1. 認証機能
- ユーザー登録・ログイン・ログアウト
- セッション管理

### 2. 課題管理（Homeタブ）
- **授業管理**: 授業の追加・一覧表示・並び替え
- **年度管理**: 年度の追加（トークルーム自動作成）
- **課題管理**: 課題の追加・完了管理・期限設定
- **予測変換**: 既存の授業名・課題名を候補表示（登録済みは除外）
- **課題評価**: 難易度を5段階評価＋コメント投稿
- **並び替え**: ドラッグ&ドロップで自由に並び替え

### 3. 授業評価（Class Evaluationタブ）
- 授業ごとの評価投稿（星5段階＋コメント）
- 評価の編集・削除
- 授業詳細・評価一覧表示

### 4. トークルーム
- 授業×年度ごとのチャットルーム
- リアルタイムメッセージング
- 情報交換・質問対応

## 🔧 技術スタック

- **バックエンド**: PHP 8.4.11
- **データベース**: PostgreSQL 12.3
- **フロントエンド**: JavaScript (ES6+), HTML5, CSS3
- **デザイン**: ダークテーマUI

## 📊 データベース構造

### 主要テーブル

- **users**: ユーザー情報
- **courses**: 授業情報（授業名は全体で一意）
- **course_years**: 授業×年度の組み合わせ
- **assignments**: 課題情報（授業・年度ごとに一意）
- **assignment_evaluations**: 課題の難易度評価
- **course_evaluations**: 授業の総合評価
- **chat_rooms**: トークルーム（授業×年度ごと）
- **chat_messages**: チャットメッセージ

### データの重複防止

- 授業名は全体で重複不可（`courses.course_name` に UNIQUE 制約）
- 課題名は授業・年度ごとに重複不可（`assignments` に UNIQUE 制約）
- 予測変換機能で既存データを候補表示し、入力の統一性を確保

## 🐛 トラブルシューティング

### データベース接続エラー
`includes/config.php` のデータベース接続情報を確認してください。

### ログインできない
データベースに初期管理者アカウントが作成されているか確認：
```sql
SELECT * FROM users WHERE email = 'admin@university.ac.jp';
```

### 予測変換が表示されない
- ブラウザの開発者ツール（F12）でConsoleタブのエラーを確認
- NetworkタブでAPIのレスポンスを確認

---

**データサイエンス学部専用システム**
**バージョン**: 1.0
