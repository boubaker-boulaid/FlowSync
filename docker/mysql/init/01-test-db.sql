-- Runs only when the mysql_data volume is empty.
-- The entrypoint has already created the `flowsync` database and user from the env vars.
CREATE DATABASE IF NOT EXISTS flowsync_test
    CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

GRANT ALL PRIVILEGES ON flowsync_test.* TO 'flowsync'@'%';
