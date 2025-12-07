<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$ratings = $data['ratings'] ?? [];

if (empty($ratings)) {
    jsonResponse(['success' => false, 'error' => '評価がありません'], 400);
}

try {
    $pdo->beginTransaction();
    foreach ($ratings as $r) {
        $stmt = $pdo->prepare('INSERT INTO check_ratings (item_id, rating, comment, created_at)
            VALUES (?, ?, ?, NOW())');
        $stmt->execute([$r['item_id'], $r['rating'], $r['comment']]);
    }
    $pdo->commit();

    jsonResponse(['success' => true]);
} catch (PDOException $e) {
    $pdo->rollBack();
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
?>