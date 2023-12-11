<?php

namespace App\Repository;

use App\Lib\Database;
use App\Entity\Tag;
use \PDO;

class TagRepository implements Repository
{
    public ?\PDO $connection;

    public function __construct() 
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }
    
    public function create(object $entity)
    {}

    public function update(object $entity)
    {}

    public function delete(object $entity)
    {}

    public function getAll()
    {}

    public function getById(int $id)
    {}
    
    public function getAllBy(int $id)
    {}

    public function getBy(string $value)
    {}
    
    public function count()
    {}
}