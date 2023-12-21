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
    
    public function create(object $category)
    {
        if(!$category instanceof Category) {
            return false;
        }

        $statement = $this->connection->prepare(
            'INSERT INTO category(name, description, slug) VALUES(:name, :description, :slug)'
        );

        $statement->bindValue(':name', $category->getName());
        $statement->bindValue(':description', $category->getDescription());
        $statement->bindValue(':slug', $category->getSlug());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de l\'insertion de la categorie.');
        } else {
            return true;
        }
    }

    public function update(object $category)
    {
        if(!$category instanceof Category) {
            return false;
        }

        $statement = $this->connection->prepare(
            "UPDATE category SET name = :name, description = :description, slug = :slug WHERE id = :id"
        );

        $statement->bindValue(':id', $category->getId(), PDO::PARAM_INT);
        $statement->bindValue(':name', $category->getName());
        $statement->bindValue(':description', $category->getDescription());
        $statement->bindValue(':slug', $category->getSlug());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de la modification de la catégorie.');
        } else {
            return true;
        }
    }

    public function delete(object $category)
    {
        if(!$category instanceof Category) {
            return false;
        }

        $statement = $this->connection->prepare(
            'DELETE FROM category WHERE id = :id'
        );

        $statement->bindValue(':id', $category->getId());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de la suppression de la categorie.');
        } else {
            return true;
        }
    }

    public function getAll()
    {
        $statement = $this->connection->query(
            "SELECT * FROM category "
        );

        $statement->execute();
        $categoryIds = $statement->fetchAll();
        $categories = [];

        foreach ($categoryIds as $data) {
            $category = $this->getById($data['id']);
            $categories[] = $category;
        }

        return $categories;
    }

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
    {
        $statement = $this->connection->query("SELECT COUNT(*) FROM category");
        return $statement->fetchColumn();
    }
}