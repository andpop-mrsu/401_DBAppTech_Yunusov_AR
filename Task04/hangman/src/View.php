<?php

namespace aidar555\hangman\View;

use cli;

function showHelp(): void
{
    cli\line("Hangman game (console)");
    cli\line("Options:");
    cli\line("  -n, --new           Start new game (default)");
    cli\line("  -l, --list          List saved games (not implemented)");
    cli\line("  -r, --replay <id>   Replay saved game by id (not implemented)");
    cli\line("  -h, --help          Show this help");
    cli\line("  -p, --player <name> Set player name");
}

function showStartScreen(string $player): void
{
    cli\line("Welcome to Hangman, {$player}!");
    cli\line("Guess the 6-letter word.");
}

function showMaskedWord(array $masked): void
{
    cli\line("Word: " . implode(' ', $masked));
}

function showHangman(int $wrong): void
{
    $pics = [
        "+---+\n    |\n    |\n    |\n   ===",
        "+---+\n  0 |\n    |\n    |\n   ===",
        "+---+\n  0 |\n  | |\n    |\n   ===",
        "+---+\n  0 |\n /| |\n    |\n   ===",
        "+---+\n  0 |\n /|\\|\n    |\n   ===",
        "+---+\n  0 |\n /|\\|\n /  |\n   ===",
        "+---+\n  0 |\n /|\\|\n / \\|\n   ===",
    ];

    cli\line($pics[$wrong]);
}

function promptLetter(): string
{
    return cli\prompt("Enter a letter");
}

function showResult(bool $won, string $word): void
{
    if ($won) {
        cli\line("Congratulations! You guessed '{$word}'!");
    } else {
        cli\line("You lost! The word was '{$word}'.");
    }
}

function showMessage(string $msg): void
{
    cli\line($msg);
}