<?php
/**
 * データベース接続設定
 * 
 * PostgreSQLへの接続情報を管理
 */

// プロジェクトのベースパス
define('BASE_PATH', '/~nambo/zemi_g9/project');
define('BASE_URL', 'https://gms.gdl.jp/~nambo/zemi_g9/project');

// データベース接続情報
$host = "localhost";
$user = "nambo";
$password = "e6Q9JGJS";
$dbname = "nambo";

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
