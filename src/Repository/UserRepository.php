<?php

declare(strict_types=1);

namespace App\Repository;

use App\Lib\Database;
use App\Entity\User;
use PDO;

class UserRepository implements RepositoryInterface
{

    public ?PDO $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function create(object $user): bool
    {
        if (!$user instanceof User) {
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

    public function update(object $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        $statement = $this->connection->prepare(
            "UPDATE user SET name = :name, firstname = :firstname, nickname = :nickname, biography = :biography, picture = :picture, mail = :mail, password = :password, role = :role, status = :status WHERE id = :id"
        );

        $statement->bindValue(':id', $user->getId(), PDO::PARAM_INT);
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
            throw new \RuntimeException('Erreur lors de la modification de l\'utilisateur.');
        } else {
            return true;
        }
    }

    public function delete(object $user): bool
    {
        $statement = $this->connection->prepare(
            'DELETE FROM user WHERE id = :id'
        );

        $statement->bindValue(':id', $user->getId());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de la suppression de l\'utilisateur.');
        } else {
            return true;
        }
    }

    public function getPaginated(int $limit, int $offset): array
    {
        $statement = $this->connection->prepare(
            "SELECT id FROM user ORDER BY id LIMIT :limit OFFSET :offset"
        );

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $userIds = $statement->fetchAll();
        $users = [];

        foreach ($userIds as $data) {
            $user = $this->getById($data['id']);
            $users[] = $user;
        }

        return $users;
    }

    public function getAll(): array|object
    {
        $statement = $this->connection->query(
            "SELECT * FROM user ORDER BY id"
        );

        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, 'App\Entity\User');

        $user = $statement->fetchAll();
        return $user;
    }

    public function getBy(string $email): ?User
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM user WHERE mail = :mail"
        );

        $statement->bindValue(':mail', $email);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, 'App\Entity\User');

        return $statement->fetch();
    }

    public function getById(int $id): ?User
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM user WHERE id = :id"
        );

        $statement->bindValue(':id', (int)$id);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, 'App\Entity\User');
        $user = $statement->fetch();

        if (!$user) {
            return null;
        }

        return $user;
    }

    public function count(): int
    {
        $statement = $this->connection->query("SELECT COUNT(*) FROM user");
        return $statement->fetchColumn();
    }
}
