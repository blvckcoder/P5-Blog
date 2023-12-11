<?php 

namespace App\Repository;

use App\Lib\Database;
use App\Entity\User;
use PDO;

class UserRepository implements Repository {

    public ?PDO $connection;

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
            "SELECT * FROM user ORDER BY id"
        );

        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, 'App\Entity\User');

        $user = $statement->fetchAll();
        return $user;
    }
    
    public function getAllBy(int $id)
    {}

    public function getBy(string $value)
    {}

    public function getById(int $id)
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM user WHERE id = :id");

        $statement->bindValue(':id', (int)$id);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, 'App\Entity\User');

        return $statement->fetch();
    }
    
    public function count()
    {}
}