<?php
// api.php
require_once 'config.php';

// POSTリクエスト以外はリダイレクト
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$action = $_POST['action'] ?? '';

// --- ログイン処理 ---
if ($action === 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // メールアドレスでユーザーを検索
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    // ユーザーが存在し、パスワードハッシュが一致するか検証
    // 指定されたカラム名 'password_hash' を使用
    if ($user && password_verify($password, $user['password_hash'])) {
        
        // ログイン成功：セッションに情報を保存
        session_regenerate_id(true); // セキュリティ対策
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        
        header('Location: todo.php');
        exit;
    } else {
        // ログイン失敗
        $_SESSION['error'] = 'メールアドレスまたはパスワードが間違っています。';
        header('Location: index.php');
        exit;
    }
}

// --- 新規登録処理（register.phpから呼ばれる想定） ---
elseif ($action === 'register') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // パスワードのハッシュ化
    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :pass)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':pass', $hash); // ハッシュ化したものを保存
        $stmt->execute();

        $_SESSION['error'] = '登録完了しました。ログインしてください。'; // エラー変数を使ってメッセージ伝達
        header('Location: index.php');
    } catch (PDOException $e) {
        $_SESSION['error'] = '登録に失敗しました（Emailが重複している可能性があります）。';
        header('Location: register.php');
    }
    exit;
}