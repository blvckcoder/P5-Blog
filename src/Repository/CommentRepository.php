<?php

declare(strict_types=1);

namespace App\Repository;

use App\Lib\Database;
use App\Entity\Comment;
use PDO;

class CommentRepository implements RepositoryInterface
{
    public ?\PDO $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function create(object $comment): bool
    {
        if (!$comment instanceof Comment) {
            return false;
        }

        $statement = $this->connection->prepare(
            'INSERT INTO comment(content, commentStatus, userId, postId, createdDate) VALUES(:content, :commentStatus, :userId, :postId, NOW())'
        );

        $statement->bindValue(':content', $comment->getContent());
        $statement->bindValue(':commentStatus', $comment->getCommentStatus());
        $statement->bindValue(':userId', $comment->getUserId());
        $statement->bindValue(':postId', $comment->getPostId());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de l\'insertion du commentaire.');
        } else {
            return true;
        }
    }

    public function update(object $comment): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE comment SET commentStatus = :commentStatus WHERE id =:id'
        );
        $statement->bindValue(':id', $comment->getId());
        $statement->bindValue(':commentStatus', $comment->getCommentStatus());

        $affectedLines = $statement->execute();

        return ($affectedLines > 0);
    }

    public function delete(object $comment): bool
    {
        $statement = $this->connection->prepare(
            'DELETE FROM comment WHERE id = :id'
        );

        $statement->bindValue(':id', $comment->getId());

        if (!$statement->execute()) {
            throw new \RuntimeException('Erreur lors de la suppression du commentaire.');
        } else {
            return true;
        }
    }

    public function getPaginated(string $commentStatus, int $limit, int $offset): array
    {
        $statement = $this->connection->prepare(
            "SELECT id FROM comment WHERE commentStatus = :commentStatus ORDER BY createdDate DESC LIMIT :limit OFFSET :offset"
        );

        $statement->bindValue(':commentStatus', $commentStatus, PDO::PARAM_STR);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $commentIds = $statement->fetchAll();
        $comments = [];

        foreach ($commentIds as $data) {
            $comment = $this->getById($data['id']);
            $comments[] = $comment;
        }

        return $comments;
    }

    public function getAll(): array
    {
        $statement = $this->connection->query(
            "SELECT id FROM comment ORDER BY createdDate DESC"
        );

        $statement->execute();
        $commentIds = $statement->fetchAll();
        $commentRepository = new commentRepository();
        $comments = [];

        foreach ($commentIds as $data) {
            $comment = $commentRepository->getById($data['id']);
            $comments[] = $comment;
        }

        return $comments;
    }


    public function getAllBy(int $postId): array|false
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM comment WHERE postId = :postId ORDER BY createdDate DESC"
        );

        $statement->bindValue(':postId', $postId);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, 'App\Entity\Comment');

        $comments = $statement->fetchAll();

        $userRepository = new UserRepository;
        foreach ($comments as $comment) {
            $comment->setAuthor($userRepository->getById($comment->getUserId()));
        }

        return $comments;
    }

    public function getBy(string $value): ?object
    {
        return null;
    }

    public function getById(int $id): ?Comment
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM comment WHERE id = :id"
        );
        $statement->bindValue(':id', $id);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, 'App\Entity\Comment');
        $comment = $statement->fetch();

        if (!$comment) {
            return null;
        }

        $userRepository = new UserRepository();
        $comment->setAuthor($userRepository->getById($comment->getUserId()));
        $postRepository = new PostRepository();
        $comment->setPost($postRepository->getById($comment->getPostId()));
        
        return $comment;
    }

    public function count(): int
    {
        $statement = $this->connection->query("SELECT COUNT(*) FROM comment");
        return $statement->fetchColumn();
    }

    public function countByPost(int $postId, string $commentStatus): int
    {
        $statement = $this->connection->prepare("SELECT COUNT(*) FROM comment WHERE postId = :postId AND commentStatus = :commentStatus");
        $statement->bindValue(':postId', $postId);
        $statement->bindValue(':commentStatus', $commentStatus);
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function countByStatus(string $commentStatus): int
    {
        $statement = $this->connection->prepare("SELECT COUNT(*) FROM comment WHERE commentStatus = :commentStatus");
        $statement->bindValue(':commentStatus', $commentStatus);
        $statement->execute();
        return $statement->fetchColumn();
    }
}
