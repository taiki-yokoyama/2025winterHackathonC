<!-- 担当者選択モーダル -->
<div id="assignee-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>担当者を選択</h3>
            <button class="modal-close" onclick="closeAssigneeModal()">×</button>
        </div>
        <div id="assignee-list" class="assignee-list"></div>
        <button class="btn-primary" style="width: 100%;" onclick="confirmAssignees()">決定</button>
    </div>
</div>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDCAide</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <h2 class="logo">PDCAide</h2>
            <nav class="nav">
                <button onclick="showView('plan')">📝 Plan</button>
                <button onclick="showView('do')">🚀 Do</button>
                <button onclick="showView('check')">📊 Check</button>
                <button onclick="showView('action')">💡 Action</button>
            </nav>
        </aside>

        <main class="main-content" id="main-content">
            <div id="app"></div>
        </main>
    </div>

    <!-- モーダル（別ファイル参照） -->
    <div id="assignee-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>担当者を選択</h3>
                <button class="modal-close" onclick="closeAssigneeModal()">×</button>
            </div>
            <div id="assignee-list" class="assignee-list"></div>
            <button class="btn-primary" style="width: 100%;" onclick="confirmAssignees()">決定</button>
        </div>
    </div>

    <script src="js/app.js"></script>
</body>
</html>
