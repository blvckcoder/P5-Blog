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

    public function create(object $user)
    {
        if(!$user instanceof User) {
            return false;
        }

        $statement = $this->connection->prepare(
            'INSERT INTO user(name, firstname, nickname, biography, picture, mail, password, role, status) VALUES(:name, :firstname, :nickname, :biography, :picture, :mail, :password, :role, :status)'
        );

        $statement->bindValue(':name', $user->getName());
        $statement->bindValue(':firstname', $user->getFirstname());
        $statement->bindValue(':nickname', $user->getNickname());
        $statement->bindValue(':biography', $user->getBiography());
        $statement->bindValue(':picture', $user->getPicture());
        $statement->bindValue(':mail', $user->getMail());
        $statement->bindValue(':password', $user->getPassword());
        $statement->bindValue(':role', $user->getRole());
        $statement->bindValue(':status', $user->getStatus());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de l\'insertion de l\'utilisateur.');
        } else {
            return true;
        }
    }

    public function update(object $user)
    {
        $statement = $this->connection->prepare(
            'UPDATE user SET firstname = :firstname, nickname = :nickname, biography = :biography, picture = :picture, mail = :mail, password = :password, role = :role, status = :status WHERE id = :id;'
        );

        $statement->bindValue(':firstname', $user->getFirstname());
        $statement->bindValue(':nickname', $user->getNickname());
        $statement->bindValue(':biography', $user->getBiography());
        $statement->bindValue(':picture', $user->getPicture());
        $statement->bindValue(':mail', $user->getMail());
        $statement->bindValue(':password', $user->getPassword());
        $statement->bindValue(':role', $user->getRole());
        $statement->bindValue(':status', $user->getStatus());
    }

    public function delete(object $user)
    {
        $statement = $this->connection->prepare(
            'DELETE FROM user WHERE id = :id'
        );

        $statement->bindValue(':id', $user->getId());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de l\'insertion de l\'utilisateur.');
        } else {
            return true;
        }
    }

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