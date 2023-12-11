<?php

//declare(strict_types=1);

namespace App\Repository;

use App\Lib\Database;
use App\Entity\Comment;
use PDO;

class CommentRepository implements Repository
{
    public ?\PDO $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function create(object $comment)
    {
        if(!$comment instanceof Comment) {
            //throw new \InvalidArgumentException('$comment n\'est pas un objet Comment');
            return false;
        }

        $statement = $this->connection->prepare(
            'INSERT INTO comment(content, commentStatus, userId, postId, creationDate) VALUES(:content, :commentStatus, :userId, :postId, NOW())'
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

    public function update(object $entity)
    {
        $statement = $this->connection->prepare(
            'UPDATE comment SET userId = ?, content = ? WHERE id = ?'
        );
        $statement->bindValue(':content', $comment->getContent());
        $statement->bindValue(':commentStatus', $comment->getCommentStatus());
        $statement->bindValue(':userId', $comment->getUserId());
        $statement->bindValue(':postId', $comment->getPostId());
    
        $affectedLines = $statement->execute();

        return ($affectedLines > 0);
    }

    public function delete(object $comment)
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

    public function getAll() {
        $statement = $this->connection->query(
            "SELECT id FROM comment ORDER BY creationDate DESC");

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


    public function getAllBy(int $postId)
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM comment WHERE postId = :postId ORDER BY creationDate DESC"
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

    public function getBy(string $value)
    {}

    public function getById(int $id)
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM comment WHERE id = :id"
        );
        $statement->bindValue(':id', $id);
        $statement->execute();
        //vérifier que le commentaire existe bien
        $statement->setFetchMode(PDO::FETCH_CLASS, 'App\Entity\Comment');
        $comment = $statement->fetch();

        $userRepository = new UserRepository();
        $comment->setAuthor($userRepository->getById($comment->getUserId()));

        return $comment;
    }
    
    public function count()
    {}

}
