<?php

namespace App\Controllers;

use App\Lib\Hydrator;
use App\Entity\Comment;
use App\Lib\Pagination;
use App\Repository\CommentRepository;

class CommentController extends DefaultController
{
    public function displayAdminComments()
    {
        $this->auth->checkAdmin();

        $commentValidatedStatus = "published";
        $commentBlockedStatus = "blocked";
        $itemsPerPage = 6;
        $pagination = 0;

        $commentRepository = new CommentRepository();
        $commentsValidated = $commentRepository->getPaginated($commentValidatedStatus, $itemsPerPage, $pagination);
        $commentsValidatedNumb = $commentRepository->countByStatus($commentValidatedStatus);

        $commentsBlocked = $commentRepository->getPaginated($commentBlockedStatus, $itemsPerPage, $pagination);
        $commentsBlockedNumb = $commentRepository->countByStatus($commentBlockedStatus);


        echo $this->twig->getTwig()->render('backend/comments.twig', [
            'published' => $commentsValidated,
            'publishedNumb' => $commentsValidatedNumb,
            'draft' => $commentsBlocked,
            'draftNumb' => $commentsBlockedNumb

        ]);
    }

    public function displayAdminValidatedComments()
    {
        $this->auth->checkAdmin();

        $commentStatus = "published";
        $itemsPerPage = 9;
        $currentPage = intval($_GET['page'] ?? 1);

        $commentRepository = new CommentRepository();
        $totalItems = $commentRepository->count();

        $pagination = new Pagination($totalItems, $itemsPerPage, $currentPage);

        $comments = $commentRepository->getPaginated($commentStatus, $itemsPerPage, $pagination->getOffset());

        $paginationHtml = $pagination->renderHtml();

        echo $this->twig->getTwig()->render('backend/commentsvalidated.twig', [
            'comments' => $comments,
            'pagination' => $paginationHtml
        ]);
    }

    public function displayAdminDraftedComments()
    {
        $this->auth->checkAdmin();

        $commentStatus = "blocked";
        $itemsPerPage = 9;
        $currentPage = intval($_GET['page'] ?? 1);

        $commentRepository = new CommentRepository();
        $totalItems = $commentRepository->count();

        $pagination = new Pagination($totalItems, $itemsPerPage, $currentPage);

        $comments = $commentRepository->getPaginated($commentStatus, $itemsPerPage, $pagination->getOffset());

        $paginationHtml = $pagination->renderHtml();

        echo $this->twig->getTwig()->render('backend/commentsdrafted.twig', [
            'comments' => $comments,
            'pagination' => $paginationHtml
        ]);
    }

    public function create(array $params)
    {
        $this->auth->check();
        if (!isset($_SESSION['userId'], $params['post']['postId'], $params['post']['content'])) {
            throw new \Exception('Les données du formulaire sont invalides.');
        }

        $params['post']['userId'] = $_SESSION['userId'];
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
        $this->auth->check();
        $id = (int)$id['id'];

        $commentRepository = new CommentRepository();
        $comment = $commentRepository->getById($id);

        $currentUserId = $_SESSION['userId'] ?? null;
        if ($comment->getUserId() !== $currentUserId) {
            throw new \Exception('Vous n\'êtes pas autorisé à supprimer ce commentaire.');
        }

        $success = $commentRepository->delete($comment);

        if (!$success) {
            throw new \Exception('Impossible de supprimer le commentaire !');
        } else {
            header('Location: /post/' . $comment->getPostId());
            exit;
        }
    }

    public function adminDelete(array $id)
    {
        $this->auth->checkAdmin();
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
