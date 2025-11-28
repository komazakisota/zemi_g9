<?php
/**
 * Class Evaluationタブ（授業評価画面）
 */

require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/functions.php';

// ログインチェック
require_login();

$user_id = get_current_user_id();
$username = get_current_username();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Evaluation - 課題管理システム</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/class-evaluation.css">
    <link rel="stylesheet" href="../assets/css/modal.css">
</head>
<body>
    <!-- ヘッダー -->
    <header class="header">
        <div class="header-left">
            <h1 class="app-title">DS授業・課題管理サイト</h1>
        </div>
        <div class="header-center">
            <nav class="tabs">
                <a href="home.php" class="tab">ホーム</a>
                <a href="class-evaluation.php" class="tab active">授業評価</a>
            </nav>
        </div>
        <div class="header-right">
            <span class="username">👤 <?php echo h($username); ?></span>
            <a href="logout.php" class="btn btn-secondary btn-sm">ログアウト</a>
        </div>
    </header>

    <!-- メインコンテンツ -->
    <div class="evaluation-container">
        <h2>授業評価</h2>
        <p class="subtitle">授業を評価して、後輩に情報を共有しましょう</p>
        
        <div id="course-evaluation-list" class="course-evaluation-list">
            <p class="loading-text">読み込み中...</p>
        </div>
    </div>

    <!-- モーダル: 授業評価投稿/詳細 -->
    <div id="course-evaluation-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-course-name">授業評価</h2>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- 動的にコンテンツを挿入 -->
            </div>
            <div class="modal-footer" id="modal-footer">
                <button class="btn btn-secondary" onclick="closeCourseEvaluationModal()">閉じる</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/course_evaluations.js"></script>
</body>
</html>
