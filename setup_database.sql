-- PostgreSQL schema for Stories in Whispers.
-- The app database lives on the local PostgreSQL server (127.0.0.1:5432).
--
-- One-time provisioning (run as the postgres superuser):
--   sudo -u postgres psql -c "CREATE ROLE stories_in_whispers LOGIN PASSWORD 'CHANGE_ME';"
--   sudo -u postgres psql -c "CREATE DATABASE stories_in_whispers OWNER stories_in_whispers \
--     ENCODING 'UTF8' TEMPLATE template0 LC_COLLATE 'C.UTF-8' LC_CTYPE 'C.UTF-8';"
--
-- Then load this schema into that database:
--   psql -h 127.0.0.1 -U stories_in_whispers -d stories_in_whispers -f setup_database.sql

CREATE TABLE IF NOT EXISTS poems (
    id              SERIAL PRIMARY KEY,
    player_name     VARCHAR(255) NOT NULL,
    poem_text       TEXT NOT NULL,
    poem_lines      JSONB NOT NULL,          -- structured poem data
    syllables_count INTEGER NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_player_name    ON poems (player_name);
CREATE INDEX IF NOT EXISTS idx_created_at     ON poems (created_at);
CREATE INDEX IF NOT EXISTS idx_player_created ON poems (player_name, created_at);
