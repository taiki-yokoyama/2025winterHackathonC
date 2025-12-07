<?php
require_once 'config.php';
checkLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Invalid method'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
$ratings = $data['ratings'] ?? [];

if (empty($ratings)) {
    jsonResponse(['error' => '評価を入力してください'], 400);
}

$db = getDB();
$user_id = $_SESSION['user_id'];

try {
    $db->beginTransaction();
    
    $stmt = $db->prepare("
        INSERT INTO check_ratings (check_item_id, user_id, rating, comment) 
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)
    ");
    
    foreach ($ratings as $rating) {
        $stmt->execute([
            $rating['item_id'],
            $user_id,
            $rating['rating'],
            $rating['comment'] ?? ''
        ]);
    }
    
    $db->commit();
    
    jsonResponse(['success' => true]);
} catch (Exception $e) {
    $db->rollBack();
    error_log("Check save error: " . $e->getMessage());
    jsonResponse(['error' => '保存に失敗しました'], 500);
}