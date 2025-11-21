<?php
/**
 * Homeタブ（メイン画面）
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

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
    <title>Home - 課題管理システム</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/autocomplete.css">
</head>
<body>
    <!-- ヘッダー -->
    <header class="header">
        <div class="header-left">
            <h1 class="app-title">DS授業・課題管理サイト</h1>
        </div>
        <div class="header-center">
            <nav class="tabs">
                <a href="home.php" class="tab active">課題管理</a>
                <a href="class-evaluation.php" class="tab">授業評価</a>
            </nav>
        </div>
        <div class="header-right">
            <span class="username">👤 <?php echo h($username); ?></span>
            <a href="logout.php" class="btn btn-secondary btn-sm">ログアウト</a>
        </div>
    </header>

    <!-- メインコンテンツ -->
    <div class="main-container">
        <!-- 左サイドバー: 授業一覧 -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>授業一覧</h2>
                <button class="btn btn-primary btn-sm" onclick="openAddCourseModal()">+ 授業</button>
            </div>
            <div id="course-list" class="course-list">
                <p class="loading-text">読み込み中...</p>
            </div>
        </aside>

        <!-- 右メインエリア -->
        <main class="main-content">
            <!-- 授業・年度選択エリア -->
            <div class="control-bar">
                <div class="control-left">
                    <div class="form-group-inline">
                        <label>授業:</label>
                        <select id="course-select" class="form-control">
                            <option value="">授業を選択してください</option>
                        </select>
                    </div>
                    <div class="form-group-inline">
                        <label>年度:</label>
                        <select id="year-select" class="form-control">
                            <option value="">年度を選択してください</option>
                        </select>
                    </div>
                    <button class="btn btn-success btn-sm" onclick="openAddYearModal()">+ 年度</button>
                    <button class="btn btn-primary btn-sm" id="open-chat-btn" onclick="openChatModal()" style="display: none;">
                        💬 トークルーム
                    </button>
                </div>
            </div>

            <!-- 課題エリア -->
            <div class="assignment-area" id="assignment-area" style="display: none;">
                <!-- 課題フィルター -->
                <div class="assignment-controls">
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-filter="incomplete">未提出</button>
                        <button class="filter-btn" data-filter="completed">提出済み</button>
                    </div>
                    <div class="assignment-actions">
                        <button class="btn btn-secondary btn-sm" onclick="resetAssignmentOrder()">
                            🔄 期限順に戻す
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="openAddAssignmentModal()">+ 課題追加</button>
                    </div>
                </div>

                <!-- 課題一覧 -->
                <div id="assignment-list" class="assignment-list">
                    <p class="info-message">課題を追加してください</p>
                </div>
            </div>

            <!-- 初期メッセージ -->
            <div id="initial-message" class="initial-message">
                <p>👈 左サイドバーから授業を選択するか、新しい授業を追加してください</p>
            </div>
        </main>
    </div>

    <!-- モーダル: 授業追加 -->
    <div id="add-course-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>授業を追加</h2>
            </div>
            <div class="modal-body">
                <form id="add-course-form">
                    <div class="form-group">
                        <label for="course-name">授業名 *</label>
                        <div class="autocomplete-wrapper">
                            <input type="text" id="course-name" name="course_name" required
                            placeholder="例: 2025_web-db" autocomplete="off">
                        </div>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeAddCourseModal()">キャンセル</button>
                <button class="btn btn-primary" onclick="addCourse()">追加</button>
            </div>
        </div>
    </div>

    <!-- モーダル: 年度追加 -->
    <div id="add-year-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>年度を追加</h2>
            </div>
            <div class="modal-body">
                <form id="add-year-form">
                    <div class="form-group">
                        <label for="year-input">年度 *</label>
                        <input type="number" id="year-input" name="year" required
                               placeholder="例: 2025" min="2020" max="2100">
                    </div>
                    <p class="info-message">※ 年度を追加すると、自動的にトークルームが作成されます</p>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeAddYearModal()">キャンセル</button>
                <button class="btn btn-primary" onclick="addYear()">追加</button>
            </div>
        </div>
    </div>

    <!-- モーダル: 課題追加 -->
    <div id="add-assignment-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>課題を追加</h2>
            </div>
            <div class="modal-body">
                <form id="add-assignment-form">
                    <div class="form-group">
                        <label for="assignment-name">課題名 *</label>
                        <div class="autocomplete-wrapper">
                            <input type="text" id="assignment-name" name="assignment_name" required
                                   placeholder="例: 最終課題" autocomplete="off">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>期限設定</label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="has_deadline" value="no" checked
                                       onchange="toggleDeadlineFields()">
                                期限なし
                            </label>
                            <label>
                                <input type="radio" name="has_deadline" value="yes"
                                       onchange="toggleDeadlineFields()">
                                期限あり
                            </label>
                        </div>
                    </div>

                    <div id="deadline-fields" style="display: none;">
                        <div class="form-group">
                            <label for="deadline-date">日付 *</label>
                            <input type="date" id="deadline-date" name="deadline_date">
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="has-time" name="has_time">
                                時間を指定
                            </label>
                        </div>
                        <div id="time-field" class="form-group" style="display: none;">
                            <label for="deadline-time">時間</label>
                            <input type="time" id="deadline-time" name="deadline_time" value="23:59">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeAddAssignmentModal()">キャンセル</button>
                <button class="btn btn-primary" onclick="addAssignment()">追加</button>
            </div>
        </div>
    </div>

    <!-- モーダル: 課題評価フォーム -->
    <div id="evaluation-form-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="eval-assignment-name">課題評価</h2>
            </div>
            <div class="modal-body">
                <form id="evaluation-form">
                    <input type="hidden" id="eval-assignment-id">
                    
                    <div class="form-group">
                        <label>難易度評価 *</label>
                        <div class="rating-stars" id="rating-input">
                            <span data-rating="1">☆</span>
                            <span data-rating="2">☆</span>
                            <span data-rating="3">☆</span>
                            <span data-rating="4">☆</span>
                            <span data-rating="5">☆</span>
                        </div>
                        <input type="hidden" id="rating-value" name="rating" required>
                        <p class="help-text">★が多いほど大変な課題です</p>
                    </div>

                    <div class="form-group">
                        <label for="eval-comment">コメント（任意）</label>
                        <textarea id="eval-comment" name="comment" rows="4"
                                  placeholder="課題の感想やアドバイスを入力"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeEvaluationForm()">戻る</button>
                <button class="btn btn-primary" onclick="submitEvaluation()">評価を投稿</button>
            </div>
        </div>
    </div>

    <!-- モーダル: 課題評価詳細 -->
    <div id="evaluation-detail-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="detail-assignment-name">課題評価詳細</h2>
            </div>
            <div class="modal-body" id="evaluation-detail-body">
                <p class="loading-text">読み込み中...</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeEvaluationDetail()">閉じる</button>
            </div>
        </div>
    </div>

    <!-- モーダル: トークルーム -->
    <div id="chat-modal" class="modal-overlay">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h2 id="chat-room-title">トークルーム</h2>
            </div>
            <div class="modal-body">
                <div id="chat-messages" class="chat-messages">
                    <p class="loading-text">読み込み中...</p>
                </div>
                <div class="chat-input-area">
                    <textarea id="chat-message-input" placeholder="メッセージを入力..." rows="2"></textarea>
                    <button class="btn btn-primary" onclick="sendMessage()">送信</button>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeChatModal()">閉じる</button>
            </div>
        </div>
    </div>

    <script src="js/autocomplete.js"></script>
    <script src="js/courses.js"></script>
    <script src="js/years.js"></script>
    <script src="js/assignments.js"></script>
    <script src="js/assignment_evaluations.js"></script>
    <script src="js/chat.js"></script>
    <script src="js/dragdrop.js"></script>
</body>
</html>
