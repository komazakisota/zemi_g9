<?php
/**
 * 課題名候補取得API
 * 
 * GET /api/assignments/get_assignment_names.php?course_id=1&year=2025
 * 
 * 指定された授業の、指定された年度以前の全課題名を取得
 */

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/functions.php';

// ログインチェック
if (!is_logged_in()) {
    json_error('ログインが必要です', 401);
}

$course_id = get('course_id');
$year = get('year');

if (empty($course_id) || empty($year)) {
    json_error('course_idとyearが必要です');
}

try {
    // まず選択中のcourse_year_idを取得
    $stmt = $pdo->prepare('
        SELECT course_year_id
        FROM course_years
        WHERE course_id = ? AND year = ?
    ');
    $stmt->execute([$course_id, $year]);
    $current_course_year_id = $stmt->fetchColumn();

    if (!$current_course_year_id) {
        json_success(['assignment_names' => []]);
        return;
    }

    // 選択中の授業の、他の年度の課題名を取得（選択中の年度にすでに存在する課題名は除外）
    $stmt = $pdo->prepare('
        SELECT DISTINCT a.assignment_name
        FROM assignments a
        JOIN course_years cy ON a.course_year_id = cy.course_year_id
        WHERE cy.course_id = ?
          AND cy.year <= ?
          AND a.assignment_name NOT IN (
              SELECT assignment_name
              FROM assignments
              WHERE course_year_id = ?
          )
        ORDER BY a.assignment_name ASC
    ');
    $stmt->execute([$course_id, $year, $current_course_year_id]);
    $assignment_names = $stmt->fetchAll(PDO::FETCH_COLUMN);

    json_success([
        'assignment_names' => $assignment_names
    ]);

} catch (PDOException $e) {
    json_error('データベースエラー: ' . $e->getMessage(), 500);
}
?>