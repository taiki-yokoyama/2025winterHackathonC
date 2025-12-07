<?php
require_once 'config.php';
checkLogin();

$db = getDB();
$team_id = $_SESSION['team_id'];

// チームメンバー取得
$stmt = $db->prepare("SELECT id, display_name, avatar FROM users WHERE team_id = ?");
$stmt->execute([$team_id]);
$team_members = $stmt->fetchAll();

// 現在のPlan取得（Doに表示するため）
$stmt = $db->prepare("
    SELECT p.id, p.text, GROUP_CONCAT(pa.user_id) as assignee_ids
    FROM plans p
    LEFT JOIN plan_assignees pa ON p.id = pa.plan_id
    WHERE p.team_id = ? AND p.status = 'current'
    GROUP BY p.id
    ORDER BY p.created_at DESC
");
$stmt->execute([$team_id]);
$current_plans = $stmt->fetchAll();

foreach ($current_plans as &$plan) {
    $plan['assignees'] = $plan['assignee_ids'] ? array_map('intval', explode(',', $plan['assignee_ids'])) : [];
}

// 前回のPlan取得
$stmt = $db->prepare("
    SELECT p.id, p.text, GROUP_CONCAT(pa.user_id) as assignee_ids
    FROM plans p
    LEFT JOIN plan_assignees pa ON p.id = pa.plan_id
    WHERE p.team_id = ? AND p.status = 'previous'
    GROUP BY p.id
    ORDER BY p.created_at DESC
");
$stmt->execute([$team_id]);
$previous_plans = $stmt->fetchAll();

foreach ($previous_plans as &$plan) {
    $plan['assignees'] = $plan['assignee_ids'] ? array_map('intval', explode(',', $plan['assignee_ids'])) : [];
}

// Doタスク取得
$stmt = $db->prepare("
    SELECT d.id, d.text, d.status, GROUP_CONCAT(da.user_id) as assignee_ids
    FROM do_tasks d
    LEFT JOIN do_assignees da ON d.id = da.do_task_id
    WHERE d.team_id = ? AND d.task_type = 'current'
    GROUP BY d.id
    ORDER BY d.created_at DESC
");
$stmt->execute([$team_id]);
$do_tasks = $stmt->fetchAll();

foreach ($do_tasks as &$task) {
    $task['assignees'] = $task['assignee_ids'] ? array_map('intval', explode(',', $task['assignee_ids'])) : [];
}

// Check評価項目取得
$stmt = $db->prepare("SELECT id, text FROM check_items WHERE team_id = ? ORDER BY created_at");
$stmt->execute([$team_id]);
$check_items = $stmt->fetchAll();

// 自分の評価を取得
foreach ($check_items as &$item) {
    $stmt = $db->prepare("SELECT rating, comment FROM check_ratings WHERE check_item_id = ? AND user_id = ?");
    $stmt->execute([$item['id'], $_SESSION['user_id']]);
    $rating = $stmt->fetch();
    $item['rating'] = $rating ? (int)$rating['rating'] : 3;
    $item['comment'] = $rating ? $rating['comment'] : '';
}

// 全メンバーの評価平均を取得（Action用）
$stmt = $db->prepare("
    SELECT 
        ci.id as item_id,
        ci.text,
        AVG(cr.rating) as avg_rating,
        COUNT(DISTINCT cr.user_id) as response_count
    FROM check_items ci
    LEFT JOIN check_ratings cr ON ci.id = cr.check_item_id
    WHERE ci.team_id = ?
    GROUP BY ci.id
");
$stmt->execute([$team_id]);
$check_averages = $stmt->fetchAll();

// 各項目のコメント取得
foreach ($check_averages as &$avg) {
    $stmt = $db->prepare("
        SELECT u.display_name, cr.comment 
        FROM check_ratings cr
        JOIN users u ON cr.user_id = u.id
        WHERE cr.check_item_id = ? AND cr.comment IS NOT NULL AND cr.comment != ''
    ");
    $stmt->execute([$avg['item_id']]);
    $avg['comments'] = $stmt->fetchAll();
}

// Action取得
$stmt = $db->prepare("
    SELECT a.id, a.text, GROUP_CONCAT(aa.user_id) as assignee_ids
    FROM actions a
    LEFT JOIN action_assignees aa ON a.id = aa.action_id
    WHERE a.team_id = ?
    GROUP BY a.id
    ORDER BY a.created_at DESC
");
$stmt->execute([$team_id]);
$actions = $stmt->fetchAll();

foreach ($actions as &$action) {
    $action['assignees'] = $action['assignee_ids'] ? array_map('intval', explode(',', $action['assignee_ids'])) : [];
}

jsonResponse([
    'team_members' => $team_members,
    'current_plans' => $current_plans,
    'previous_plans' => $previous_plans,
    'do_tasks' => $do_tasks,
    'check_items' => $check_items,
    'check_averages' => $check_averages,
    'actions' => $actions
]);