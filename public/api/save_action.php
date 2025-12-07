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
    jsonResponse(['error' => '改善内容と担当者を入力してください'], 400);
}

$db = getDB();
$team_id = $_SESSION['team_id'];

try {
    $db->beginTransaction();
    
    // Actionを保存
    $stmt = $db->prepare("INSERT INTO actions (team_id, text) VALUES (?, ?)");
    $stmt->execute([$team_id, $text]);
    $action_id = $db->lastInsertId();
    
    // 担当者を保存
    $stmt = $db->prepare("INSERT INTO action_assignees (action_id, user_id) VALUES (?, ?)");
    foreach ($assignees as $user_id) {
        $stmt->execute([$action_id, $user_id]);
    }
    
    $db->commit();
    
    jsonResponse([
        'success' => true,
        'action_id' => $action_id
    ]);
} catch (Exception $e) {
    $db->rollBack();
    error_log("Action save error: " . $e->getMessage());
    jsonResponse(['error' => '保存に失敗しました'], 500);
}