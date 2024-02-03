<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Lib\Hydrator;
use App\Entity\Comment;
use App\Lib\Pagination;
use App\Lib\HTTPResponse;
use App\Repository\CommentRepository;

class CommentController extends DefaultController
{
    public function displayAdminComments(): void
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

        $flashMessage = $this->getFlash();

        echo $this->twig->getTwig()->render('backend/comments.twig', [
            'published' => $commentsValidated,
            'publishedNumb' => $commentsValidatedNumb,
            'draft' => $commentsBlocked,
            'draftNumb' => $commentsBlockedNumb,
            'flashMessage' => $flashMessage

        ]);
    }

    public function displayAdminValidatedComments(): void
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

    public function displayAdminDraftedComments(): void
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

    public function create(array $params): void
    {
        $this->auth->check();
        $postData = $params['post'];

        if (!isset($_SESSION['userId'], $postData['postId'], $postData['content'])) {
            //donnees vide
            $this->addFlash('error', 'Les données du formulaire sont invalides !');
            HTTPResponse::redirect('/post/' . $postData['postId']);
        }

        $postData['userId'] = $_SESSION['userId'];
        $postData['postId'] = (int) $postData['postId'];
        $comment = new Comment;
        $comment = Hydrator::hydrate($postData, $comment);

        $commentRepository = new CommentRepository();
        $success = $commentRepository->create($comment);

        if (!$success) {
            $this->addFlash('error', 'Impossible d\'ajouter le commentaire !');
            HTTPResponse::redirect('/post/' . $comment->getPostId());
        } else {
            $this->addFlash('success', 'Commentaire en attente. Un administrateur procède à la modération. Veuillez patienter.');
            HTTPResponse::redirect('/post/' . $comment->getPostId());
        }
    }

    public function delete(array $id): void
    {
        $this->auth->check();
        $id = (int)$id['id'];

        $commentRepository = new CommentRepository();
        $comment = $commentRepository->getById($id);

        $currentUserId = $_SESSION['userId'] ?? null;
        if ($comment->getUserId() !== $currentUserId) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer ce commentaire !');
            HTTPResponse::redirect('/post/' . $comment->getPostId());
        }

        $success = $commentRepository->delete($comment);

        if (!$success) {
            $this->addFlash('error', 'Impossible de supprimer le commentaire !');
            HTTPResponse::redirect('/post/' . $comment->getPostId());
        } else {
            $this->addFlash('success', 'Suppression réussie du commentaire.');
            HTTPResponse::redirect('/post/' . $comment->getPostId());
        }
    }

    public function adminDelete(array $id): void
    {
        $this->auth->checkAdmin();
        $id = (int)$id['id'];

        $commentRepository = new CommentRepository();
        $comment = $commentRepository->getById($id);
        $success = $commentRepository->delete($comment);

        if (!$success) {
            $this->addFlash('error', 'Impossible de supprimer ce commentaire !');
            HTTPResponse::redirect('/admin/comments');
        } else {
            $this->addFlash('success', 'Commentaire supprimé !');
            HTTPResponse::redirect('/admin/comments');
        }
    }

    public function adminUpdateForm(array $id): void
    {
        $this->auth->checkAdmin();
        $commentId = (int)$id['id'];

        $commentRepository = new CommentRepository();
        $existingComment = $commentRepository->getById($commentId);

        if(!$existingComment) {
            header($_SERVER["SERVER_PROTOCOL"] . '404 Not Found');
            echo 'Le commentaire n\'existe pas 404 not found';
        }

        echo $this->twig->getTwig()->render('backend/forms/editComment.twig', [
            'comment' => $existingComment
        ]);
    }

    public function adminUpdate(array $id): void
    {
        $this->auth->checkAdmin();
        $commentId = (int)$id['id'];
        $commentData = $_POST;


        if (!isset($commentId, $commentData['commentStatus'])) {
            $this->addFlash('error', 'Erreur sur la modification de ce commentaire !');
            HTTPResponse::redirect('/admin/comments');
        }

        

        $commentRepository = new CommentRepository();
        $comment = $commentRepository->getById($commentId);
        
        if($comment) {
            $comment = Hydrator::hydrate($commentData, $comment);
            $success = $commentRepository->update($comment);

            if (!$success) {
                $this->addFlash('error', 'Impossible de modifier le commentaire !');
                HTTPResponse::redirect('/admin/comments');
            } else {
                $this->addFlash('success', 'Commentaire modifié avec succès !');
                HTTPResponse::redirect('/admin/comments');
            }
        } else {
            $this->addFlash('error', 'Commentaire non trouvé.');
            HTTPResponse::redirect('/admin/comments');
        }

    }
}
