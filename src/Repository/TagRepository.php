<?php

declare(strict_types=1);

namespace App\Repository;

use App\Lib\Database;
use App\Entity\Tag;
use \PDO;

class TagRepository implements RepositoryInterface
{
    public ?\PDO $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function create(object $tag): bool
    {
        if (!$tag instanceof Tag) {
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

    public function update(object $tag): bool
    {
        if (!$tag instanceof Tag) {
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

    public function delete(object $tag): bool
    {
        if (!$tag instanceof Tag) {
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

    public function getPaginated(int $limit, int $offset): array
    {
        $statement = $this->connection->prepare(
            "SELECT id FROM tag ORDER BY id LIMIT :limit OFFSET :offset"
        );

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $tagIds = $statement->fetchAll();
        $tags = [];

        foreach ($tagIds as $data) {
            $tag = $this->getById($data['id']);
            $tags[] = $tag;
        }

        return $tags;
    }

    public function getAll(): array
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

    public function getById(int $id): ?Tag
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM tag WHERE id = :id"
        );
        $statement->bindValue(':id', $id);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, 'App\Entity\Tag');
        $tag = $statement->fetch();

        if (!$tag) {
            return null;
        }

        return $tag;
    }

    public function getBy(string $value): ?object
    {
        return null;
    }

    public function count(): int
    {
        $statement = $this->connection->query("SELECT COUNT(*) FROM tag");
        return $statement->fetchColumn();
    }
}

