<?php
require 'config.php';

try {
    $result = [];

    // チームメンバー
    $stmt = $pdo->query('SELECT id, display_name, avatar FROM users');
    $result['teamMembers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Plan
    $stmt = $pdo->query('SELECT * FROM plans ORDER BY id DESC');
    $result['plans'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Do
    $stmt = $pdo->query('SELECT * FROM do_tasks ORDER BY id DESC');
    $result['doTasks'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check項目
    $stmt = $pdo->query('SELECT * FROM check_items ORDER BY id ASC');
    $result['checkItems'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check評価
    $stmt = $pdo->query('SELECT * FROM check_ratings');
    $result['checkRatings'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Action
    $stmt = $pdo->query('SELECT * FROM actions ORDER BY id DESC');
    $result['actions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse(['success' => true, 'data' => $result]);
} catch (PDOException $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
?>