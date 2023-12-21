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
    
    public function create(object $tag)
    {
        if(!$tag instanceof Tag) {
            return false;
        }

        $statement = $this->connection->prepare(
            'INSERT INTO tag(name, description, slug) VALUES(:name, :description, :slug)'
        );

        $statement->bindValue(':name', $tag->getName());
        $statement->bindValue(':description', $tag->getDescription());
        $statement->bindValue(':slug', $tag->getSlug());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de l\'insertion du tag.');
        } else {
            return true;
        }
    }

    public function update(object $tag)
    {
        if(!$tag instanceof Tag) {
            return false;
        }

        $statement = $this->connection->prepare(
            "UPDATE tag SET name = :name, description = :description, slug = :slug WHERE id = :id"
        );

        $statement->bindValue(':id', $tag->getId(), PDO::PARAM_INT);
        $statement->bindValue(':name', $tag->getName());
        $statement->bindValue(':description', $tag->getDescription());
        $statement->bindValue(':slug', $tag->getSlug());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de la modification du tag.');
        } else {
            return true;
        }
    }

    public function delete(object $tag)
    {
        if(!$tag instanceof Tag) {
            return false;
        }

        $statement = $this->connection->prepare(
            'DELETE FROM tag WHERE id = :id'
        );

        $statement->bindValue(':id', $tag->getId());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de la suppression du tag.');
        } else {
            return true;
        }
    }

    public function getAll()
    {
        $statement = $this->connection->query(
            "SELECT id FROM tag ORDER BY id"
        );

        $statement->execute();
        $tagIds = $statement->fetchAll();
        $tags = [];

        foreach ($tagIds as $data) {
            $tag = $this->getById($data['id']);
            $tags[] = $tag;
        }

        return $tags;
    }

    public function getById(int $id)
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM tag WHERE id = :id"
        );
        $statement->bindValue(':id', $id);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, 'App\Entity\Tag');
        $tag = $statement->fetch();

        $userRepository = new UserRepository;
        $tag->setAuthor($userRepository->getById($tag->getUserId()));
    }

    public function getBy(string $value)
    {}
    
    public function count()
    {
        $statement = $this->connection->query("SELECT COUNT(*) FROM tag");
        return $statement->fetchColumn();
    }
}