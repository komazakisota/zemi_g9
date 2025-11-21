<?php
/**
 * 共通関数
 * 
 * アプリ全体で使用する便利な関数
 */

/**
 * XSS対策: HTMLエスケープ
 * 
 * @param string $str エスケープする文字列
 * @return string エスケープ済み文字列
 */
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * JSON形式でレスポンスを返す
 * 
 * @param mixed $data レスポンスデータ
 * @param int $status HTTPステータスコード
 */
function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * エラーレスポンスを返す
 * 
 * @param string $message エラーメッセージ
 * @param int $status HTTPステータスコード
 */
function json_error($message, $status = 400) {
    json_response([
        'success' => false,
        'error' => $message
    ], $status);
}

/**
 * 成功レスポンスを返す
 * 
 * @param array $data レスポンスデータ
 */
function json_success($data = []) {
    json_response(array_merge(['success' => true], $data));
}

/**
 * 日付フォーマット
 * 
 * @param string $datetime 日時文字列
 * @param bool $has_time 時間を含むか
 * @return string フォーマット済み日時
 */
function format_datetime($datetime, $has_time = true) {
    if (empty($datetime)) {
        return '期限なし';
    }
    
    $dt = new DateTime($datetime);
    
    if ($has_time) {
        return $dt->format('Y/m/d H:i');
    } else {
        return $dt->format('Y/m/d');
    }
}

/**
 * 星評価を生成
 * 
 * @param float $rating 評価値（1〜5）
 * @return string 星のHTML
 */
function render_stars($rating) {
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5;
    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
    
    $html = '';
    
    // 満点の星
    for ($i = 0; $i < $full_stars; $i++) {
        $html .= '★';
    }
    
    // 半分の星
    if ($half_star) {
        $html .= '☆';
    }
    
    // 空の星
    for ($i = 0; $i < $empty_stars; $i++) {
        $html .= '☆';
    }
    
    return $html;
}

/**
 * POSTリクエストかチェック
 * 
 * @return bool POSTリクエストか
 */
function is_post() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * POSTデータを取得
 * 
 * @param string $key キー名
 * @param mixed $default デフォルト値
 * @return mixed 値
 */
function post($key, $default = null) {
    return $_POST[$key] ?? $default;
}

/**
 * GETデータを取得
 * 
 * @param string $key キー名
 * @param mixed $default デフォルト値
 * @return mixed 値
 */
function get($key, $default = null) {
    return $_GET[$key] ?? $default;
}

/**
 * CSRFトークンを生成
 * 
 * @return string トークン
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRFトークンを検証
 * 
 * @param string $token トークン
 * @return bool 有効か
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
