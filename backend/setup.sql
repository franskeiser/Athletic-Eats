

CREATE DATABASE IF NOT EXISTS athleticeats
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE athleticeats;

CREATE TABLE IF NOT EXISTS recipes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    category    ENUM('breakfast', 'lunch', 'dinner', 'snacks') NOT NULL,
    description TEXT,
    image       VARCHAR(255),
    calories    INT NOT NULL,
    protein     INT NOT NULL,
    carbs       INT NOT NULL,
    fat         INT NOT NULL,
    ingredients TEXT,
    steps       TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_title (title)
);
