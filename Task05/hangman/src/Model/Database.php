<?php

namespace aidar555\hangman\Model;

use RedBeanPHP\R;
use Exception;

class Database
{
    public function __construct(string $dbPath = __DIR__ . '/../../data/hangman.db')
    {
        $dir = dirname($dbPath);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new Exception("Failed to create directory: $dir");
            }
        }

        R::setup('sqlite:' . $dbPath);
        R::freeze(false);
    }
}