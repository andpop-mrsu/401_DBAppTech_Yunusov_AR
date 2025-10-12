<?php

namespace Aidar555\hangman\Controller;

use Aidar555\hangman\Model\{Game, Dictionary};

use function Aidar555\hangman\View\{
    showHelp,
    showStartScreen,
    showMaskedWord,
    showHangman,
    showResult,
    showMessage,
    promptLetter
};

function startGame(): void
{
    $opts = parseArgs();

    if ($opts['help']) {
        showHelp();
        return;
    }

    switch ($opts['mode']) {
        case 'list':
            showMessage('Listing saved games is not implemented (DB not connected).');
            return;

        case 'replay':
            if ($opts['replay_id'] === null) {
                showMessage('Replay mode requires id: --replay <id>');
            } else {
                showMessage("Replay of game id {$opts['replay_id']} is not implemented (DB missing).");
            }
            return;

        case 'new':
        default:
            runNewGame($opts);
            return;
    }
}

function parseArgs(): array
{
    $argv = $_SERVER['argv'] ?? [];
    array_shift($argv);

    $opts = [
        'mode' => 'new',
        'help' => false,
        'player' => 'Player',
        'replay_id' => null,
    ];

    $i = 0;
    while (isset($argv[$i])) {
        $arg = $argv[$i];

        if ($arg === '--help' || $arg === '-h') {
            $opts['help'] = true;
            return $opts;
        }

        if ($arg === '--new' || $arg === '-n') {
            $opts['mode'] = 'new';
            $i++;
            continue;
        }

        if ($arg === '--list' || $arg === '-l') {
            $opts['mode'] = 'list';
            $i++;
            continue;
        }

        if ($arg === '--replay' || $arg === '-r') {
            $opts['mode'] = 'replay';
            $opts['replay_id'] = isset($argv[$i + 1]) ? (int)$argv[$i + 1] : null;
            $i += 2;
            continue;
        }

        if (str_starts_with($arg, '--player=')) {
            $opts['player'] = substr($arg, 9);
            $i++;
            continue;
        }

        if ($arg === '--player' || $arg === '-p') {
            $opts['player'] = $argv[$i + 1] ?? 'Player';
            $i += 2;
            continue;
        }

        $i++;
    }

    return $opts;
}

function runNewGame(array $opts): void
{
    $player = $opts['player'];

    $dict = new Dictionary();
    $word = $dict->getRandomWord();

    $game = new Game($word);

    showStartScreen($player);

    $attempts = 0;
    while (!$game->isWon() && !$game->isLost()) {
        showHangman($game->getWrongCount());
        showMaskedWord($game->getMaskedWord());

        $letter = promptLetter();
        if ($letter === '') {
            continue;
        }

        $attempts++;
        $result = $game->guess($letter);

        if ($result === 'ok') {
            showMessage("Good: '{$letter}' found");
        } elseif ($result === 'miss') {
            showMessage("Wrong: '{$letter}' not in word");
        } else {
            showMessage("Already tried '{$letter}'");
        }
    }

    $won = $game->isWon();
    showHangman($game->getWrongCount());
    showMaskedWord($game->getMaskedWord());
    showResult($won, $game->getWord());
    showMessage('Note: results are not saved — DB not implemented yet.');
}