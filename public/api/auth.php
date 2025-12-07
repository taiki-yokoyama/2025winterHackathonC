<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

// ログイン処理
if ($method === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        jsonResponse(['error' => 'ユーザー名とパスワードを入力してください'], 400);
    }
    
    $db = getDB();
    $stmt = $db->prepare("SELECT id, username, display_name, avatar, team_id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['display_name'] = $user['display_name'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION['team_id'] = $user['team_id'];
        
        jsonResponse([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'display_name' => $user['display_name'],
                'avatar' => $user['avatar'],
                'team_id' => $user['team_id']
            ]
        ]);
    } else {
        jsonResponse(['error' => 'ユーザー名またはパスワードが正しくありません'], 401);
    }
}

// ログアウト処理
if ($method === 'POST' && isset($_POST['action']) && $_POST['action'] === 'logout') {
    session_destroy();
    jsonResponse(['success' => true]);
}

// 現在のユーザー情報取得
if ($method === 'GET') {
    if (isset($_SESSION['user_id'])) {
        jsonResponse([
            'logged_in' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'display_name' => $_SESSION['display_name'],
                'avatar' => $_SESSION['avatar'],
                'team_id' => $_SESSION['team_id']
            ]
        ]);
    } else {
        jsonResponse(['logged_in' => false]);
    }
}