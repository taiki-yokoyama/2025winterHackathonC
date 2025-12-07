<?php
require 'config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
$ratings = $data['ratings'] ?? [];

if (empty($ratings)) {
    echo json_encode(['success' => false, 'error' => '評価データが空です']);
    exit;
}

try {
    $pdo->beginTransaction();
    foreach ($ratings as $r) {
        $stmt = $pdo->prepare('INSERT INTO check_ratings (item_id, user_id, rating, comment)
            VALUES (?, ?, ?, ?)');
        $stmt->execute([$r['item_id'], $_SESSION['user_id'], $r['rating'], $r['comment']]);
    }
    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>