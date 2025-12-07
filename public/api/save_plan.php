<?php
require 'config.php';
$data = json_decode(file_get_contents('php://input'), true);

$text = trim($data['text'] ?? '');
$assignees = $data['assignees'] ?? [];

if ($text === '' || empty($assignees)) {
    jsonResponse(['success' => false, 'error' => '内容または担当者が空です'], 400);
}

try {
    $stmt = $pdo->prepare('INSERT INTO plans (text, assignees, created_at) VALUES (?, ?, NOW())');
    $stmt->execute([$text, json_encode($assignees)]);
    jsonResponse(['success' => true]);
} catch (PDOException $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
?>