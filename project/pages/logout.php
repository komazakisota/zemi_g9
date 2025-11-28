<?php
/**
 * ログアウト処理
 */

require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/functions.php';

// ログアウト
logout_user();

// ログイン画面へリダイレクト
header('Location: index.php');
exit;
?>
