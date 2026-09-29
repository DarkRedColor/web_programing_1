<?php
// Database connection (MySQL).
// Change these if your MySQL setup is different.
const DB_HOST = 'localhost';
const DB_PORT = 3306;
const DB_NAME = 'student_register';
const DB_USER = 'root';
const DB_PASS = '';

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            // Connect to the server first so the database can be created if it doesn't exist yet.
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                $options
            );
        } catch (PDOException $e) {
            http_response_code(500);
            exit('Could not connect to MySQL: ' . htmlspecialchars($e->getMessage())
                . '<br>Check that MySQL is running and the settings in config/db.php are correct.');
        }

        $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '`
                    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $pdo->exec('USE `' . DB_NAME . '`');

        $pdo->exec('
            CREATE TABLE IF NOT EXISTS students (
                id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                student_number VARCHAR(12)  NOT NULL UNIQUE,
                full_name      VARCHAR(100) NOT NULL,
                email          VARCHAR(255) NOT NULL,
                course         VARCHAR(100) NOT NULL,
                year_of_study  TINYINT UNSIGNED NOT NULL,
                created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB
        ');
    }

    return $pdo;
}
