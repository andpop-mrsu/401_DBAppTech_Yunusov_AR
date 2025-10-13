<?php

namespace aidar555\hangman\Model;

use PDO;
use Exception;

class Database
{
    private PDO $pdo;

    public function __construct(string $dbPath = __DIR__ . '/../../data/hangman.db')
    {
        $dir = dirname($dbPath);

        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new Exception("Failed to create directory: $dir");
            }
        }

        $this->pdo = new PDO('sqlite:' . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->initSchema();
    }

    private function initSchema(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS games (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                player_name TEXT NOT NULL,
                word TEXT NOT NULL,
                result TEXT NOT NULL,
                date TEXT NOT NULL
            );

            CREATE TABLE IF NOT EXISTS attempts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                game_id INTEGER NOT NULL,
                attempt_number INTEGER NOT NULL,
                letter TEXT NOT NULL,
                outcome TEXT NOT NULL,
                FOREIGN KEY (game_id) REFERENCES games (id)
            );
        ");
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}