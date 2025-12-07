# 2025winterHackathonC

# PDCAide - チーム開発支援ツール

HTML / CSS / JS / PHP / MySQL / Docker構成の  
**PDCAサイクル管理アプリケーション**です。

---

## 🚀 セットアップ手順

### ① クローン & 起動

```bash
git clone [your-repo-url]
cd pdca-app
docker compose up -d
② アクセス
Webアプリ: http://localhost:8080

phpMyAdmin: http://localhost:8081

DB情報:

user: pdca_user

pass: pdca_password

③ デモログイン
userA / password

userB / password

userC / password

userD / password

🧩 フォルダ構成
pgsql
コードをコピーする
pdca-app/
├── Dockerfile
├── docker-compose.yml
├── sql/init.sql
└── public/
    ├── login.php
    ├── index.php
    ├── css/style.css
    ├── js/app.js
    └── api/
        ├── config.php
        ├── auth.php
        ├── get_data.php
        ├── save_plan.php
        ├── save_check.php
        └── save_action.php
🧠 機能一覧
✅ Plan: チーム計画と担当者設定
✅ Do: 実行タスクの可視化
✅ Check: 評価とコメント
✅ Action: 改善提案と次サイクル連携
✅ チーム全員の評価自動集計

🐳 Dockerコマンド集
bash
コードをコピーする
# 起動
docker compose up -d

# 停止
docker compose down

# ログ確認
docker compose logs -f app

# DBリセット
docker compose down -v && docker compose up -d
🔒 セキュリティ
SQLインジェクション防止 (PDO)

パスワードハッシュ化対応

XSS対策 (htmlspecialchars())

🏁 開発完了チェック
✅ Plan → Do → Check → Actionが一連で動作
✅ DB初期化OK
✅ phpMyAdminアクセスOK
✅ Docker起動で即動作