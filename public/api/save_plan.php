<?php
require_once 'config.php';
checkLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Invalid method'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
$text = $data['text'] ?? '';
$assignees = $data['assignees'] ?? [];

if (empty($text) || empty($assignees)) {
    jsonResponse(['error' => '計画内容と担当者を入力してください'], 400);
}

$db = getDB();
$team_id = $_SESSION['team_id'];

try {
    $db->beginTransaction();
    
    // Planを保存
    $stmt = $db->prepare("INSERT INTO plans (team_id, text, status) VALUES (?, ?, 'current')");
    $stmt->execute([$team_id, $text]);
    $plan_id = $db->lastInsertId();
    
    // 担当者を保存
    $stmt = $db->prepare("INSERT INTO plan_assignees (plan_id, user_id) VALUES (?, ?)");
    foreach ($assignees as $user_id) {
        $stmt->execute([$plan_id, $user_id]);
    }
    
    // Doタスクとしても追加
    $stmt = $db->prepare("INSERT INTO do_tasks (team_id, plan_id, text, status, task_type) VALUES (?, ?, ?, '未着手', 'current')");
    $stmt->execute([$team_id, $plan_id, $text]);
    $do_id = $db->lastInsertId();
    
    // Do担当者を保存
    $stmt = $db->prepare("INSERT INTO do_assignees (do_task_id, user_id) VALUES (?, ?)");
    foreach ($assignees as $user_id) {
        $stmt->execute([$do_id, $user_id]);
    }
    
    $db->commit();
    
    jsonResponse([
        'success' => true,
        'plan_id' => $plan_id,
        'do_id' => $do_id
    ]);
} catch (Exception $e) {
    $db->rollBack();
    error_log("Plan save error: " . $e->getMessage());
    jsonResponse(['error' => '保存に失敗しました'], 500);
}