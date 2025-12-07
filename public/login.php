<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - PDCAide</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">
                    <div class="logo-icon"></div>
                    <h1>PDCAide</h1>
                </div>
                <p class="subtitle">チーム開発支援ツール</p>
            </div>
            
            <form id="loginForm" class="login-form">
                <div class="form-group">
                    <label for="username">ユーザー名</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn-login">ログイン</button>
                
                <div id="error-message" class="error-message" style="display: none;"></div>
            </form>
            
            <div class="demo-info">
                <p class="demo-title">デモアカウント:</p>
                <div class="demo-accounts">
                    <div>userA / password (Aさん)</div>
                    <div>userB / password (Bさん)</div>
                    <div>userC / password (Cさん)</div>
                    <div>userD / password (Dさん)</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('error-message');
            
            try {
                const formData = new FormData();
                formData.append('action', 'login');
                formData.append('username', username);
                formData.append('password', password);
                
                const response = await fetch('api/auth.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = 'index.php';
                } else {
                    errorDiv.textContent = data.error || 'ログインに失敗しました';
                    errorDiv.style.display = 'block';
                }
            } catch (error) {
                console.error('Login error:', error);
                errorDiv.textContent = 'エラーが発生しました';
                errorDiv.style.display = 'block';
            }
        });
    </script>
</body>
</html>