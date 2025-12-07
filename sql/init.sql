-- データベース作成
CREATE DATABASE IF NOT EXISTS pdca_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pdca_app;

-- ユーザーテーブル
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    display_name VARCHAR(50) NOT NULL,
    avatar CHAR(1) NOT NULL,
    team_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- チームテーブル
CREATE TABLE IF NOT EXISTS teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 計画テーブル
CREATE TABLE IF NOT EXISTS plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    text TEXT NOT NULL,
    status ENUM('current', 'previous') DEFAULT 'current',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 計画担当者テーブル
CREATE TABLE IF NOT EXISTS plan_assignees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Doタスクテーブル
CREATE TABLE IF NOT EXISTS do_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    plan_id INT,
    text TEXT NOT NULL,
    status ENUM('未着手', '進行中', '完了') DEFAULT '未着手',
    task_type ENUM('current', 'previous') DEFAULT 'current',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS do_assignees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    do_task_id INT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (do_task_id) REFERENCES do_tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- チェック評価項目テーブル
CREATE TABLE IF NOT EXISTS check_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- チェック評価テーブル（各ユーザーの評価）
CREATE TABLE IF NOT EXISTS check_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    check_item_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (check_item_id) REFERENCES check_items(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_item (check_item_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- アクション改善テーブル
CREATE TABLE IF NOT EXISTS actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- アクション担当者テーブル
CREATE TABLE IF NOT EXISTS action_assignees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action_id INT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (action_id) REFERENCES actions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- サンプルデータ挿入
INSERT INTO teams (name) VALUES ('開発チーム');

INSERT INTO users (username, password, display_name, avatar, team_id) VALUES
('userA', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Aさん', 'A', 1),
('userB', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bさん', 'B', 1),
('userC', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cさん', 'C', 1),
('userD', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dさん', 'D', 1);
-- パスワードは全て 'password'

-- サンプルの前回Plan
INSERT INTO plans (team_id, text, status) VALUES
(1, '新機能の要件定義を完了する', 'previous'),
(1, 'ユーザーテストの準備をする', 'previous');

INSERT INTO plan_assignees (plan_id, user_id) VALUES
(1, 1), (1, 2),
(2, 3);

-- サンプルのDoタスク
INSERT INTO do_tasks (team_id, text, status, task_type) VALUES
(1, 'API設計書を作成', '進行中', 'current'),
(1, 'フロントエンド実装', '未着手', 'current');

INSERT INTO do_assignees (do_task_id, user_id) VALUES
(1, 1), (1, 2),
(2, 3);

-- サンプルのCheck評価項目
INSERT INTO check_items (team_id, text) VALUES
(1, 'API設計は要件を満たしているか'),
(1, 'スケジュール通りに進行したか'),
(1, 'コードレビューは適切に行われたか');

-- サンプルの評価
INSERT INTO check_ratings (check_item_id, user_id, rating, comment) VALUES
(1, 1, 4, 'スケーラビリティも考慮されている'),
(2, 1, 2, '1週間の遅延が発生'),
(3, 1, 5, '全員が参加し、建設的な意見交換ができた');

-- サンプルのAction
INSERT INTO actions (team_id, text) VALUES
(1, '次回はスケジュール管理を改善する'),
(1, 'コミュニケーション頻度を増やす');

INSERT INTO action_assignees (action_id, user_id) VALUES
(1, 1),
(2, 2), (2, 3);