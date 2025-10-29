-- Create database (run this first if the database doesn't exist)
CREATE DATABASE IF NOT EXISTS stories_in_whispers CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE stories_in_whispers;

-- Create poems table
CREATE TABLE IF NOT EXISTS poems (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_name VARCHAR(255) NOT NULL,
    poem_text TEXT NOT NULL,
    poem_lines JSON NOT NULL, -- Store the structured poem data
    syllables_count INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_player_name (player_name),
    INDEX idx_created_at (created_at)
);

-- Create an index for better performance
CREATE INDEX idx_player_created ON poems (player_name, created_at);
