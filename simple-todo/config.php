<?php
/**
 * データベース接続設定
 *
 * このファイルは先生が設定します
 */

// セッション開始
session_start();

// データベース接続情報（ここを環境に合わせて変更）
$host = "localhost";
$user = "nambo";           // 各自のユーザー名に変更
$password = "e6Q9JGJS";    // 各自のパスワードに変更
$dbname = "nambo";         // 各自のDB名に変更

// PostgreSQL接続
try {
    $dsn = "pgsql:host=$host;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("データベース接続エラー: " . $e->getMessage());
}

/**
 * XSS対策用HTMLエスケープ関数
 */
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * ログインチェック
 */
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}
?>
