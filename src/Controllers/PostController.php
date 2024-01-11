<?php

namespace App\Controllers;

use App\Entity\Post;
use App\Lib\Hydrator;
use App\Lib\Pagination;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;

class PostController extends DefaultController
{
    public function index()
    {
        $postRepository = new PostRepository();
        $posts = $postRepository->getLast();

        echo $this->twig->getTwig()->render('frontend/home.twig', [
            'posts' => $posts
        ]);
    }

    public function displayPosts()
    {
        $postStatus = "published";
        $itemsPerPage = 9;
        $currentPage = intval($_GET['page'] ?? 1);

        $postRepository = new PostRepository();
        $totalItems = $postRepository->count();
        
        $pagination = new Pagination($totalItems, $itemsPerPage, $currentPage);

        $posts = $postRepository->getPaginated($postStatus, $itemsPerPage, $pagination->getOffset());

        $paginationHtml = $pagination->renderHtml();

        echo $this->twig->getTwig()->render('frontend/blog.twig', [
            'posts' => $posts,
            'pagination' => $paginationHtml
        ]);
    }

    public function displayPost(array $params)
    {
        $postId = $params['id'];

        $postRepository = new PostRepository();
        $post = $postRepository->getById($postId);

        $commentRepository = new CommentRepository();
        $comments = $commentRepository->getAllBy($postId);
        $totalComments = $commentRepository->countByPost($postId);
        //récupérer tags + category

        echo $this->twig->getTwig()->render('frontend/post.twig', [
            'post' => $post,
            'comments' => $comments,
            'totalComments' => $totalComments
        ]);
    }

    public function displayAdminPosts()
    {
        $this->auth->checkAdmin();

        $postValidatedStatus = "published";
        $postDraftedStatus = "draft";
        $itemsPerPage = 6;
        $pagination = 0;

        $postRepository = new PostRepository();
        $postsValidated = $postRepository->getPaginated($postValidatedStatus, $itemsPerPage, $pagination);
        $postsDrafted = $postRepository->getPaginated($postDraftedStatus, $itemsPerPage, $pagination);

        echo $this->twig->getTwig()->render('backend/posts.twig', [
            'published' => $postsValidated,
            'drafts' => $postsDrafted
        ]);
    }

    public function displayAdminValidatedPosts()
    {
        $this->auth->checkAdmin();

        $postStatus = "published";
        $itemsPerPage = 9;
        $currentPage = intval($_GET['page'] ?? 1);

        $postRepository = new PostRepository();
        $totalItems = $postRepository->count();
        
        $pagination = new Pagination($totalItems, $itemsPerPage, $currentPage);

        $posts = $postRepository->getPaginated($postStatus, $itemsPerPage, $pagination->getOffset());

        $paginationHtml = $pagination->renderHtml();

        echo $this->twig->getTwig()->render('backend/postsvalidated.twig', [
            'posts' => $posts,
            'pagination' => $paginationHtml
        ]);
    }

    public function displayAdminDraftedPosts()
    {
        $this->auth->checkAdmin();

        $postStatus = "draft";
        $itemsPerPage = 9;
        $currentPage = intval($_GET['page'] ?? 1);

        $postRepository = new PostRepository();
        $totalItems = $postRepository->count();
        
        $pagination = new Pagination($totalItems, $itemsPerPage, $currentPage);

        $posts = $postRepository->getPaginated($postStatus, $itemsPerPage, $pagination->getOffset());

        $paginationHtml = $pagination->renderHtml();

        echo $this->twig->getTwig()->render('backend/postsdrafted.twig', [
            'posts' => $posts,
            'pagination' => $paginationHtml
        ]);
    }

    public function createForm()
    {
        $this->auth->checkAdmin();
        echo $this->twig->getTwig()->render('backend/forms/addPost.twig');
    }

    public function create(array $postData)
    {
        $this->auth->checkAdmin();
        $postData = $_POST;
        $postData['userId'] = $_SESSION['userId'];

        if (!isset($postData['userId'], $postData['title'], $postData['excerpt'], $postData['content'], $postData['postStatus'])) {
            throw new \Exception('Les données du formulaire sont invalides.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            function handleFileUpload(array $file, string $destinationFolder): void
            {
                $tmpFilePath = $file['tmp_name'];
                $originalFileName = $file['name'];
                $destinationFilePath = $destinationFolder . $originalFileName;
                move_uploaded_file($tmpFilePath, $destinationFilePath);
            }

            if (isset($_FILES['imgCover']) && $_FILES['imgCover']['error'] === UPLOAD_ERR_OK) {
                $destinationFolder = 'assets/img/covers/';
                handleFileUpload($_FILES['imgCover'], $destinationFolder);
                $postData['imgCover'] = $_FILES['imgCover']['name'];
            }

            if (isset($_FILES['imgCard']) && $_FILES['imgCard']['error'] === UPLOAD_ERR_OK) {
                $destinationFolder = 'assets/img/cards/';
                handleFileUpload($_FILES['imgCard'], $destinationFolder);
                $postData['imgCard'] = $_FILES['imgCard']['name'];
            }
        }

        $post = new Post();
        $post = Hydrator::hydrate($postData, $post);

        $postRepository = new PostRepository();
        $success = $postRepository->create($post);

        if (!$success) {
            throw new \Exception('Impossible d\'ajouter l\'article !');
        } else {
            header('Location: /admin/posts');
        }
    }

    public function updateForm(array $id)
    {
        $this->auth->checkAdmin();
        $postId = (int)$id['id'];

        $postRepository = new PostRepository();
        $existingPost = $postRepository->getById($postId);

        if (!$existingPost) {
            header($_SERVER["SERVER_PROTOCOL"] . '404 Not Found');
            echo 'Le post n\'existe pas 404 not found baby';
            die();
        }

        echo $this->twig->getTwig()->render('backend/forms/editPost.twig', [
            'post' => $existingPost
        ]);
    }

    public function update(array $id)
    {
        $this->auth->checkAdmin();
        $postId = (int)$id['id'];

        $postData = $_POST;
        $postData['userId'] = $_SESSION['userId'];

        if (!isset($postData['userId'], $postData['title'], $postData['excerpt'], $postData['content'])) {
            throw new \Exception('Les données du formulaire sont invalides.');
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            function handleFileUpload(array $file, string $destinationFolder): void
            {
                $tmpFilePath = $file['tmp_name'];
                $originalFileName = $file['name'];
                $destinationFilePath = $destinationFolder . $originalFileName;
                move_uploaded_file($tmpFilePath, $destinationFilePath);
            }

            if (isset($_FILES['imgCover']) && $_FILES['imgCover']['error'] === UPLOAD_ERR_OK) {
                $destinationFolder = 'assets/img/covers/';
                handleFileUpload($_FILES['imgCover'], $destinationFolder);
                $postData['imgCover'] = $_FILES['imgCover']['name'];
            }

            if (isset($_FILES['imgCard']) && $_FILES['imgCard']['error'] === UPLOAD_ERR_OK) {
                $destinationFolder = 'assets/img/cards/';
                handleFileUpload($_FILES['imgCard'], $destinationFolder);
                $postData['imgCard'] = $_FILES['imgCard']['name'];
            }

            $postRepository = new PostRepository();
            $post = $postRepository->getById($postId);

            if ($post) {
                $post = Hydrator::hydrate($postData, $post);
                $success = $postRepository->update($post);

                if (!$success) {
                    throw new \Exception('Impossible de mettre à jour le post!');
                } else {
                    header('Location: /admin/posts');
                }
            } else {
                throw new \Exception('Post non trouvé.');
            }
        }
    }

    public function delete(array $id)
    {
        $this->auth->checkAdmin();
        $id = (int)$id['id'];

        $postRepository = new PostRepository();
        $commentRepository = new CommentRepository();

        $post = $postRepository->getById($id);

        if ($post->getId() === $id) {
            $comments = $commentRepository->getAllBy($id);
            foreach ($comments as $comment) {
                $commentRepository->delete($comment);
            }
            $success = $postRepository->delete($post);
        } else {
            return false;
        }

        if (!$success) {
            throw new \Exception('Impossible de supprimer l\'article !');
        } else {
            header('Location: /admin/posts');
        }
    }
}
