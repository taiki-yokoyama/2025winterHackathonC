<?php
require 'config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
$text = trim($data['text'] ?? '');
$assignees = $data['assignees'] ?? [];

if ($text === '') {
    echo json_encode(['success' => false, 'error' => '内容が空です']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO plans (text, assignees) VALUES (?, ?)');
    $stmt->execute([$text, json_encode($assignees)]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>