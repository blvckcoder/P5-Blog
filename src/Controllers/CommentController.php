<?php

namespace App\Controllers;

use App\Lib\Hydrator;
use App\Entity\Comment;
use App\Repository\CommentRepository;

class CommentController
{
    public function create(array $params)
    {
        if (!isset($params['post']['userId'], $params['post']['postId'], $params['post']['content'])) {
            throw new \Exception('Les données du formulaire sont invalides.');
        }

        $comment = new Comment;
        $comment = Hydrator::hydrate($params['post'], $comment);

        $commentRepository = new CommentRepository();
        $success = $commentRepository->create($comment);

        if (!$success) {
            throw new \Exception('Impossible d\'ajouter le commentaire !');
        } else {
            header('Location: /post/' . $comment->getPostId());
        }
    }
}
