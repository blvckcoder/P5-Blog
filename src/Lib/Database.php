<?php

declare(strict_types=1);

namespace App\Lib;

class Database
{
    public ?\PDO $database = null;

    public function getConnection(): \PDO
    {
        if ($this->database === null) {
            $this->database = new \PDO('mysql:host=localhost;dbname=p5blog;charset=utf8', 'root', '');
        }

        return $this->database;
    }
}
