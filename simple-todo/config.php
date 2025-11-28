<?php
/**
 * データベース接続設定
<<<<<<< HEAD
 *
=======
 * 
>>>>>>> c74a60c1a3fd2cbe720c2e56a58e7b4c2387a517
 * PostgreSQLへの接続情報を管理
 * 担当: メンバーD
 */

// プロジェクトのベースパス
define('BASE_PATH', '/~nambo/zemi_g9/simple-todo');
define('BASE_URL', 'https://gms.gdl.jp/~nambo/zemi_g9/simple-todo');

// データベース接続情報
$host = "localhost";
$user = "knt416";
$password = "nFb55bRP";
$dbname = "knt416";

// PostgreSQL接続
try {
    $dsn = "pgsql:host=$host;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // 開発モード: エラーを表示
    die("データベース接続エラー: " . $e->getMessage());
}
?>