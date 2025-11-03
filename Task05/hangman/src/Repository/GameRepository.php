<?php

namespace aidar555\hangman\Repository;

use RedBeanPHP\R;
use aidar555\hangman\Model\Game;

class GameRepository
{
    public function createGame(string $player, string $word): int
    {
        $bean = R::dispense('game');
        $bean->player_name = $player;
        $bean->word = $word;
        $bean->result = 'unfinished';
        $bean->date = date('Y-m-d H:i:s');

        return R::store($bean);
    }

    public function updateResult(int $id, string $result): void
    {
        $bean = R::load('game', $id);
        $bean->result = $result;
        R::store($bean);
    }

    public function listGames(): array
    {
        $beans = R::findAll('game', 'ORDER BY date DESC');
        return array_map(fn($b) => $b->export(), $beans);
    }

    public function getGameById(int $id): ?array
    {
        $bean = R::load('game', $id);
        return $bean->id ? $bean->export() : null;
    }
}