CREATE DATABASE IF NOT EXISTS pdca_app CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE pdca_app;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    display_name VARCHAR(50) NOT NULL,
    avatar VARCHAR(10) NOT NULL
);

CREATE TABLE plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    text VARCHAR(255) NOT NULL,
    assignees JSON NOT NULL,
    created_at DATETIME
);

CREATE TABLE do_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    text VARCHAR(255),
    assignees JSON,
    status VARCHAR(20) DEFAULT '進行中',
    created_at DATETIME
);

CREATE TABLE check_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    text VARCHAR(255) NOT NULL
);

CREATE TABLE check_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    rating INT,
    comment TEXT,
    created_at DATETIME
);

CREATE TABLE actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    text VARCHAR(255) NOT NULL,
    assignees JSON,
    created_at DATETIME
);

-- 初期データ
INSERT INTO users (display_name, avatar) VALUES
('Aさん', 'A'), ('Bさん', 'B'), ('Cさん', 'C'), ('Dさん', 'D');

INSERT INTO check_items (text) VALUES
('タスクの進捗'), ('チームの連携'), ('成果物の品質'), ('時間管理'), ('改善意欲');