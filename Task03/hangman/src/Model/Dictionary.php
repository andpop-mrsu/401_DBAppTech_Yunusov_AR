<?php

namespace Aidar555\hangman\Model;

class Dictionary
{
    private array $words = [
        'planet',
        'silver',
        'castle',
        'button',
        'garden',
        'friend',
        'bridge',
        'school',
        'mother',
    ];

    public function getRandomWord(): string
    {
        return $this->words[array_rand($this->words)];
    }
}