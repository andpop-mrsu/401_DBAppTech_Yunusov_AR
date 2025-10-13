<?php

namespace Aidar555\hangman\Model;

class Game
{
    private string $word;
    private array $letters;
    private array $masked;
    private array $guessed = [];
    private int $wrong = 0;
    private int $maxWrong = 6;

    public function __construct(string $word)
    {
        $this->word = mb_strtolower($word);
        $this->letters = preg_split('//u', $this->word, -1, PREG_SPLIT_NO_EMPTY);
        $this->masked = array_fill(0, count($this->letters), '_');
    }

    public function guess(string $letter): string
    {
        $letter = mb_strtolower($letter);

        if (in_array($letter, $this->guessed, true)) {
            return 'repeat';
        }

        $this->guessed[] = $letter;
        $found = false;

        foreach ($this->letters as $i => $ch) {
            if ($ch === $letter) {
                $this->masked[$i] = $ch;
                $found = true;
            }
        }

        if ($found) {
            return 'ok';
        }

        $this->wrong++;
        return 'miss';
    }

    public function getMaskedWord(): array
    {
        return $this->masked;
    }

    public function getWrongCount(): int
    {
        return $this->wrong;
    }

    public function isWon(): bool
    {
        return !in_array('_', $this->masked, true);
    }

    public function isLost(): bool
    {
        return $this->wrong >= $this->maxWrong;
    }

    public function getWord(): string
    {
        return $this->word;
    }
}