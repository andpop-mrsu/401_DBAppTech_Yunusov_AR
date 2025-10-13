<?php

namespace aidar555\hangman\Repository;

use PDO;

class AttemptRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function addAttempt(int $gameId, int $number, string $letter, string $outcome): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO attempts (game_id, attempt_number, letter, outcome)
            VALUES (:game_id, :number, :letter, :outcome)
        ");
        $stmt->execute([
            ':game_id' => $gameId,
            ':number' => $number,
            ':letter' => $letter,
            ':outcome' => $outcome
        ]);
    }

    public function getAttempts(int $gameId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM attempts WHERE game_id = ? ORDER BY attempt_number ASC");
        $stmt->execute([$gameId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}