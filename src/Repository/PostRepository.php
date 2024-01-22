<?php

declare(strict_types=1);

namespace App\Repository;

use App\Lib\Database;
use App\Lib\HTTPResponse;
use App\Entity\Post;
use PDO;

class PostRepository implements RepositoryInterface
{
    public ?\PDO $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function getLast(): array
    {
        $statement = $this->connection->query(
            "SELECT id FROM post WHERE postStatus = 'published' ORDER BY createdDate DESC LIMIT 3"
        );

        $statement->execute();
        $postIds = $statement->fetchAll();
        $posts = [];

        foreach ($postIds as $data) {
            $post = $this->getById($data['id']);
            $posts[] = $post;
        }

        return $posts;
    }

    public function getPaginated(string $postStatus, int $limit, int $offset): array
    {
        $statement = $this->connection->prepare(
            "SELECT id FROM post WHERE postStatus = :postStatus ORDER BY createdDate DESC LIMIT :limit OFFSET :offset"
        );

        $statement->bindValue(':postStatus', $postStatus, PDO::PARAM_STR);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $postIds = $statement->fetchAll();
        $posts = [];

        foreach ($postIds as $data) {
            $post = $this->getById($data['id']);
            $posts[] = $post;
        }

        return $posts;
    }

    public function getAll(): array
    {
        $statement = $this->connection->query(
            "SELECT id FROM post ORDER BY createdDate DESC"
        );

        $statement->execute();
        $postIds = $statement->fetchAll();
        $posts = [];

        foreach ($postIds as $data) {
            $post = $this->getById($data['id']);
            $posts[] = $post;
        }

        return $posts;
    }

    public function getBy(string $value): ?object
    {
        return null;
    }

    public function getById(int $id): ?Post
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM post WHERE id = :id"
        );
        $statement->bindValue(':id', $id);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, 'App\Entity\Post');
        $post = $statement->fetch();

        if (!$post) {
            HTTPResponse::redirect('/');
        }

        $userRepository = new UserRepository;
        $author = $userRepository->getById($post->getUserId());
        $post->setAuthor($author);

        $commentRepository = new CommentRepository;
        $comments = $commentRepository->getAllBy($post->getId());
        $post->setComment($comments);
        //categoryRepository
        //tagRepository


        return $post;
    }

    public function create(object $post): bool
    {
        if (!$post instanceof Post) {
            return false;
        }

        $statement = $this->connection->prepare(
            'INSERT INTO post(title, excerpt, content, imgCard, imgCover, postStatus, userId, createdDate) VALUES(:title, :excerpt, :content, :imgCard, :imgCover, :postStatus, :userId, NOW())'
        );

        $statement->bindValue(':title', $post->getTitle());
        $statement->bindValue(':excerpt', $post->getExcerpt());
        $statement->bindValue(':content', $post->getContent());
        $statement->bindValue(':imgCard', $post->getImgCard());
        $statement->bindValue(':imgCover', $post->getImgCover());
        $statement->bindValue(':postStatus', $post->getPostStatus());
        $statement->bindValue(':userId', $post->getUserId());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de l\'insertion du post.');
        } else {
            return true;
        }
    }

    public function update(object $post): bool
    {
        if (!$post instanceof Post) {
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
            throw new \RuntimeException('Erreur lors de l\'insertion du post.');
        } else {
            return true;
        }
    }

    public function delete(object $post): bool
    {
        $statement = $this->connection->prepare(
            'DELETE FROM post WHERE id = :id'
        );

        $statement->bindValue(':id', $post->getId());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de l\'insertion du post.');
        } else {
            return true;
        }
    }

    public function count(): int
    {
        $statement = $this->connection->query("SELECT COUNT(*) FROM post");
        return $statement->fetchColumn();
    }
}
