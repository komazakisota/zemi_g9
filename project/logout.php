<?php
/**
 * ログアウト処理
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

// ログアウト
logout_user();

// ログイン画面へリダイレクト
header('Location: index.php');
exit;
?>
