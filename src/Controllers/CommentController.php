<?php

namespace App\Controllers;

use App\Lib\Twig;
use App\Lib\Hydrator;
use App\Entity\Comment;
use App\Repository\CommentRepository;

class CommentController
{
    public $twig;

    public function __construct()
    {
        $this->twig = new Twig();
    }

    public function displayAdminComments()
    {
        $commentRepository = new CommentRepository();
        $comments = $commentRepository->getAll();

        echo $this->twig->getTwig()->render('backend/comments.twig', [
            'comments' => $comments
        ]);

    }
    
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

    public function delete(array $id)
    {
        $id = (int)$id['id'];

        $commentRepository = new CommentRepository();
        $comment = $commentRepository->getById($id);
        $success = $commentRepository->delete($comment);

        // Redirection après le succès
        if (!$success) {
            throw new \Exception('Impossible d\'ajouter le commentaire !');
        } else {
            header('Location: /admin/comments');
        }
    }

}
