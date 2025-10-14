<?php

namespace aidar555\hangman\Repository;

use PDO;

class GameRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function createGame(string $player, string $word): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO games (player_name, word, result, date)
            VALUES (:player, :word, 'unfinished', :date)
        ");
        $stmt->execute([
            ':player' => $player,
            ':word' => $word,
            ':date' => date('Y-m-d H:i:s'),
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updateResult(int $id, string $result): void
    {
        $stmt = $this->pdo->prepare("UPDATE games SET result = :result WHERE id = :id");
        $stmt->execute([':result' => $result, ':id' => $id]);
    }

    public function listGames(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM games ORDER BY date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGameById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM games WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}