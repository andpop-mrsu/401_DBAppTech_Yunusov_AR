<?php

namespace aidar555\hangman\Repository;

use RedBeanPHP\R;
use aidar555\hangman\Model\Attempt;

class AttemptRepository
{
    public function addAttempt(int $gameId, int $number, string $letter, string $outcome): void
    {
        $attempt = R::dispense('attempt');
        $attempt->game_id = $gameId;
        $attempt->attempt_number = $number;
        $attempt->letter = $letter;
        $attempt->outcome = $outcome;
        R::store($attempt);
    }

    public function getAttempts(int $gameId): array
    {
        $beans = R::findAll('attempt', 'game_id = ? ORDER BY attempt_number ASC', [$gameId]);
        return array_map(fn($b) => $b->export(), $beans);
    }
}