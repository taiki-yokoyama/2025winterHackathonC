<?php
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$current_user = [
    'id' => $_SESSION['user_id'],
    'username' => $_SESSION['username'],
    'display_name' => $_SESSION['display_name'],
    'avatar' => $_SESSION['avatar'],
    'team_id' => $_SESSION['team_id']
];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDCAide - チーム開発支援ツール</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- サイドバー -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="app-logo">
                    <div class="logo-icon"></div>
                    <h1 class="app-title">PDCAide</h1>
                </div>
                <p class="app-subtitle">チーム開発支援ツール</p>
                
                <div class="user-info">
                    <div class="user-avatar"><?php echo htmlspecialchars($current_user['avatar']); ?></div>
                    <div class="user-details">
                        <p class="user-name"><?php echo htmlspecialchars($current_user['display_name']); ?></p>
                        <p class="user-role">開発チーム</p>
                    </div>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <button class="nav-item active" data-view="home">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    ホーム
                </button>
 
                <div class="nav-section">PDCAサイクル</div>
                
                <button class="nav-item" data-view="plan">
                    <span class="nav-icon">P</span>
                    Plan
                </button>
                
                <button class="nav-item" data-view="do">
                    <span class="nav-icon">D</span>
                    Do
                </button>
                
                <button class="nav-item" data-view="check">
                    <span class="nav-icon">C</span>
                    Check
                </button>
                
                <button class="nav-item" data-view="action">
                    <span class="nav-icon">A</span>
                    Action
                </button>
            </nav>
        </div>
        
        <!-- メインコンテンツ -->
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <div class="pdca-icon">
                        <svg width="48" height="48" viewBox="0 0 55 55">
                            <circle cx="27.5" cy="27.5" r="23.5" fill="none" stroke="#ff8c69" stroke-width="4" 
                                    stroke-dasharray="36.91 147.65" stroke-dashoffset="0" stroke-linecap="round" />
                            <circle cx="27.5" cy="27.5" r="23.5" fill="none" stroke="#ffb088" stroke-width="4"
                                    stroke-dasharray="36.91 147.65" stroke-dashoffset="-36.91" stroke-linecap="round" />
                            <circle cx="27.5" cy="27.5" r="23.5" fill="none" stroke="#ffd4a3" stroke-width="4"
                                    stroke-dasharray="36.91 147.65" stroke-dashoffset="-73.82" stroke-linecap="round" />
                            <circle cx="27.5" cy="27.5" r="23.5" fill="none" stroke="#ffe9b8" stroke-width="4"
                                    stroke-dasharray="36.91 147.65" stroke-dashoffset="-110.73" stroke-linecap="round" />
                        </svg>
                        <div class="pdca-dot"></div>
                    </div>
                    <div>
                        <h2 class="header-title" id="page-title">ホーム</h2>
                        <p class="header-subtitle">チームでPDCAを回そう</p>
                    </div>
                </div>
                <button class="btn-logout" onclick="logout()">ログアウト</button>
            </header>
            
            <main id="main-view" class="view-content">
                <!-- コンテンツがここに動的に表示されます -->
            </main>
        </div>
    </div>

     <!-- 担当者選択モーダル -->
    <div id="assignee-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>担当者を選択</h3>
                <button class="modal-close" onclick="closeAssigneeModal()">&times;</button>
            </div>
            <div id="assignee-list" class="assignee-list">
                <!-- 動的に生成 -->
            </div>
            <button class="btn-primary" onclick="confirmAssignees()">追加</button>
        </div>
    </div>
    
    <script>
        // 現在のユーザー情報をJavaScriptに渡す
        window.currentUser = <?php echo json_encode($current_user); ?>;
    </script>
    <script src="js/app.js"></script>
</body>
</html>