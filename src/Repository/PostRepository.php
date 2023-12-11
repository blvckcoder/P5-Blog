<?php

namespace App\Repository;

use App\Lib\Database;
use App\Entity\Post;
use PDO;

class PostRepository implements Repository
{
    public ?\PDO $connection;

    public function __construct() 
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function getAll()
    {
        $statement = $this->connection->query(
            "SELECT id FROM post ORDER BY creationDate DESC LIMIT 0, 5");

        $statement->execute();
        $postIds = $statement->fetchAll();
        $posts = [];

        foreach ($postIds as $data) {
            $post = $this->getById($data['id']);
            $posts[] = $post;
        }

        return $posts;
    }

    public function getBy(string $value)
    {
    }

    public function getById(int $id)
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM post WHERE id = :id"
        );
        $statement->bindValue(':id', $id);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, 'App\Entity\Post');
        $post = $statement->fetch();

        $userRepository = new UserRepository;
        $post->setAuthor($userRepository->getById($post->getUserId()));
        //commentRepository
        //categoryRepository
        //tagRepository
        

        return $post;
    }
    
    public function create(object $post)
    {
        if(!$post instanceof Post) {
            return false;
        }

        $statement = $this->connection->prepare(
            'INSERT INTO post(title, excerpt, content, imgCard, imgCover, postStatus, userId, creationDate) VALUES(:title, :excerpt, :content, :imgCard, :imgCover, :postStatus, :userId, NOW())'
        );

        $statement->bindValue(':title', $post->getTitle());
        $statement->bindValue(':excerpt', $post->getExcerpt());
        $statement->bindValue(':content', $post->getContent());
        $statement->bindValue(':imgCard', $post->getImgCard());
        $statement->bindValue(':imgCover', $post->getImgCover());
        $statement->bindValue(':postStatus', $post->getPostStatus());
        $statement->bindValue(':userId', $post->getUserId());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de l\'insertion du commentaire.');
        } else {
            return true;
        }
    }

    public function update(object $post)
    {
        if(!$post instanceof Post) {
            return false;
        }

        $statement = $this->connection->prepare(
            'UPDATE post SET title = :title, excerpt = :excerpt, content = :content, imgCard = :imgCard, imgCover = :imgCover, postStatus = :postStatus,  updateDate = NOW() WHERE id =:id'
        );

        $statement->bindValue(':id', $post->getId());
        $statement->bindValue(':title', $post->getTitle());
        $statement->bindValue(':excerpt', $post->getExcerpt());
        $statement->bindValue(':content', $post->getContent());
        $statement->bindValue(':imgCard', $post->getImgCard());
        $statement->bindValue(':imgCover', $post->getImgCover());
        $statement->bindValue(':postStatus', $post->getPostStatus());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de l\'insertion du commentaire.');
        } else {
            return true;
        }
    }

    public function delete(object $post)
    {
        $statement = $this->connection->prepare(
            'DELETE FROM post WHERE id = :id'
        );

        $statement->bindValue(':id', $post->getId());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de l\'insertion du commentaire.');
        } else {
            return true;
        }
    }

    public function count()
    {
        $statement = $this->connection->query("SELECT COUNT(*) FROM post");
        return $statement->fetchColumn();
    }
}
