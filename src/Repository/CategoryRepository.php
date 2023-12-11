<?php

namespace App\Repository;

use App\Lib\Database;
use App\Entity\Category;
use \PDO;

class CategoryRepository implements Repository
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
    {
        $statement = $this->connection->query(
            "SELECT "
        );
    }
    
    public function getAllBy(int $id)
    {}

    public function getBy(string $value)
    {}

    public function getById(int $id)
    {
        $statement = $this->connection->prepare(
        "SELECT * FROM category WHERE id = :id"
        );
        $statement->bindValue(':id', $id);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, 'App\Entity\Category');
        $category = $statement->fetch();

        return $category;

    }
    
    public function count()
    {}
}