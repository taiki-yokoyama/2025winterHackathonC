# 2025winterHackathonC

# PDCAide - チーム開発支援ツール

HTML、CSS、JavaScript、PHP、MySQLで構築されたPDCAサイクル管理ツールです。

## 🚀 機能

- ✅ **Plan**: チーム計画の作成と担当者割り当て
- ✅ **Do**: 実行タスクの管理
- ✅ **Check**: チームメンバーによる評価（1-5段階）
- ✅ **Action**: 評価結果に基づく改善アクション
- ✅ **チーム共有**: 複数メンバーの評価を自動集計
- ✅ **色相環デザイン**: 視覚的にわかりやすいPDCAサイクル表示

## 📁 フォルダ構成

pdca-app/
├── docker-compose.yml      # Docker環境設定
├── Dockerfile              # PHPコンテナ設定
├── README.md               # このファイル
├── sql/
│   └── init.sql           # データベース初期化SQL
└── public/                # Webルート
    ├── index.php         # メインアプリケーション
    ├── login.php         # ログイン画面
    ├── css/
    │   └── style.css     # スタイルシート
    ├── js/
    │   └── app.js        # JavaScriptロジック
    └── api/
        ├── config.php          # DB接続設定
        ├── auth.php            # 認証API
        ├── get_data.php        # データ取得API
        ├── save_plan.php       # Plan保存API
        ├── save_check.php      # Check保存API
        └── save_action.php     # Action保存API

## 🛠️ セットアップ手順

### 必要な環境

- Docker
- Docker Compose

### インストール

1. **リポジトリをクローン（またはファイルを配置）**

bash
cd pdca-app

2. **Dockerコンテナを起動**

bash
docker-compose up -d

初回起動時、自動的にデータベースが構築されます。

3. **ブラウザでアクセス**

http://localhost:8080

### デモアカウント

以下のアカウントでログインできます（全て同じパスワード: `password`）：

- **userA / password** (Aさん)
- **userB / password** (Bさん)
- **userC / password** (Cさん)
- **userD / password** (Dさん)

### phpMyAdmin（データベース管理）

http://localhost:8081

- ユーザー名: pdca_user
- パスワード: pdca_password

## 📝 使い方

### 1. Plan（計画）

1. 計画内容を入力
2. 「担当者を選択」をクリック
3. 担当者を選んで「追加」

→ 自動的にDoタスクとして追加されます

### 2. Do（実行）

- Planで追加した項目が表示されます
- 前回のPlanも参照できます

### 3. Check（評価）

1. 各評価項目を1-5段階で評価
2. コメントを入力（任意）
3. 「評価を送信」をクリック

→ 評価は共有ストレージに保存され、チーム全員で共有されます

### 4. Action（改善）

- チーム全員の評価平均が表示されます
- 最も評価が低い項目が「最優先課題」として強調表示
- 改善アクションを追加して担当者を割り当て

→ 追加したActionは次のサイクルのPlan画面に表示されます

## 🔧 カスタマイズ

### データベース設定変更

`docker-compose.yml`の環境変数を編集：

yaml
environment:
  - DB_HOST=db
  - DB_NAME=pdca_app
  - DB_USER=pdca_user
  - DB_PASS=pdca_password

### ポート番号変更

`docker-compose.yml`のポートマッピングを編集：

yaml
ports:
  - "8080:80"  # 左側を変更（例: "3000:80"）

## 🗃️ データベース構造

### 主要テーブル

- **users**: ユーザー情報
- **teams**: チーム情報
- **plans**: 計画項目
- **do_tasks**: 実行タスク
- **check_items**: 評価項目
- **check_ratings**: 各ユーザーの評価
- **actions**: 改善アクション

## 🐛 トラブルシューティング

### コンテナが起動しない

bash
# ログを確認
docker-compose logs

# コンテナを再起動
docker-compose down
docker-compose up -d

### データベースをリセット

bash
# コンテナとボリュームを削除
docker-compose down -v

# 再起動（データベースが再初期化されます）
docker-compose up -d

### ポートが使用中の場合

bash
# 別のポートを使用
# docker-compose.ymlのportsを変更してから
docker-compose up -d

## 📦 本番環境へのデプロイ

1. `docker-compose.yml`のパスワードを変更
2. `sql/init.sql`の初期データを本番用に変更
3. SSL証明書を設定（推奨）
4. ファイアウォール設定

## 🔒 セキュリティ

- パスワードは`password_hash()`でハッシュ化
- SQLインジェクション対策（プリペアドステートメント使用）
- セッション管理による認証
- XSS対策（`htmlspecialchars()`使用）

## 📄 ライセンス

このプロジェクトはMITライセンスの下で公開されています。

## 🙏 サポート

問題が発生した場合は、Issueを作成してください。