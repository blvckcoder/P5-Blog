<?php

declare(strict_types=1);

namespace App\Controllers;

use Exception;
use App\Entity\Post;
use App\Lib\Hydrator;
use App\Lib\Pagination;
use App\Lib\HTTPResponse;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Repository\CategoryRepository;

class PostController extends DefaultController
{
    public function index(): void
    {
        $postRepository = new PostRepository();
        $posts = $postRepository->getLast();

        echo $this->twig->getTwig()->render('frontend/home.twig', [
            'posts' => $posts
        ]);
    }

    public function displayPosts(): void
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

    public function displayPost(array $params): void
    {
        $postId = (int) $params['id'];

        $postRepository = new PostRepository();
        $post = $postRepository->getById($postId);

        $commentRepository = new CommentRepository();
        $comments = $commentRepository->getAllBy($postId);
        $totalComments = $commentRepository->countByPost($postId, "published");
        //récupérer tags + category

        echo $this->twig->getTwig()->render('frontend/post.twig', [
            'post' => $post,
            'comments' => $comments,
            'totalComments' => $totalComments
        ]);
    }

    public function displayAdminPosts(): void
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

    public function displayAdminValidatedPosts(): void
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

    public function displayAdminDraftedPosts(): void
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

    public function createForm(): void
    {
        $this->auth->checkAdmin();

        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();

        echo $this->twig->getTwig()->render('backend/forms/addPost.twig', [
            'categories' => $categories
        ]);
    }

    public function create(array $postData): void
    {
        $this->auth->checkAdmin();
        $postData = $_POST;
        $postData['userId'] = $_SESSION['userId'];

        if (!isset($postData['userId'], $postData['title'], $postData['excerpt'], $postData['content'], $postData['postStatus'])) {
            throw new \Exception('Les données de création d\'article sont invalides.');
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

        if ($success) {
            $postId = $postRepository->getLastInsertId();

            if (isset($postData['categoryIds']) && is_array($postData['categoryIds'])) {
                foreach ($postData['categoryIds'] as $categoryId) {
                    $postRepository->addCategoryToPost((int)$postId, (int)$categoryId);
                }
            } else {
                $postRepository->addCategoryToPost((int)$postId, (int)1);
            }
            $success = true; 
        }


        if (!$success) {
            throw new \Exception('Impossible d\'ajouter l\'article !');
        } else {
            HTTPResponse::redirect('/admin/posts');
        }
    }

    public function updateForm(array $id): void
    {
        $this->auth->checkAdmin();
        $postId = (int)$id['id'];

        $postRepository = new PostRepository();
        $existingPost = $postRepository->getById($postId);
        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();

        if (!$existingPost) {
            header($_SERVER["SERVER_PROTOCOL"] . '404 Not Found');
            echo 'Le post n\'existe pas 404 not found baby';
        }

        echo $this->twig->getTwig()->render('backend/forms/editPost.twig', [
            'post' => $existingPost,
            'categories' => $categories
        ]);
    }

    // A refactoriser
    public function update(array $id): void
    {
        $this->auth->checkAdmin();
        $postId = (int)$id['id'];

        $postData = $_POST;
        $postData['userId'] = $_SESSION['userId'];

        if (!isset($postData['userId'], $postData['title'], $postData['excerpt'], $postData['content'])) {
            throw new \Exception('Les données de modification d\'article sont invalides.');
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
            $categoryRepository = new CategoryRepository();
            $post = $postRepository->getById($postId);

            if ($post) {
                $post = Hydrator::hydrate($postData, $post);
                $success = $postRepository->update($post);

                if (!$success) {
                    throw new \Exception('Impossible de mettre à jour le post!');
                } 
                
            $currentCategories = $categoryRepository->getForPost($postId);
            $currentCategoryIds = array_map(function ($category) {
                return $category->getId();
            }, $currentCategories);

            $submittedCategoryIds = $postData['categoryIds'] ?? [];

            $categoriesToAdd = array_diff($submittedCategoryIds, $currentCategoryIds);

            foreach ($categoriesToAdd as $categoryId) {
                $postRepository->addCategoryToPost($postId, (int)$categoryId);
            }

            $categoriesToRemove = array_diff($currentCategoryIds, $submittedCategoryIds);

            foreach ($categoriesToRemove as $categoryId) {
                $postRepository->removeCategoryToPost($postId, (int)$categoryId);
            }
                    HTTPResponse::redirect('/admin/posts');
            } else {
                throw new \Exception('Post non trouvé.');
            }
        }
    }

    public function delete(array $id): void
    {
        $this->auth->checkAdmin();
        $id = (int)$id['id'];

        $postRepository = new PostRepository();
        $commentRepository = new CommentRepository();

        $post = $postRepository->getById($id);

        if ($post !== null && $post->getId() === $id) {
            $comments = $commentRepository->getAllBy($id);
            foreach ($comments as $comment) {
                $commentRepository->delete($comment);
            }
            $success = $postRepository->delete($post);

            if (!$success) {
                throw new \Exception('Impossible de supprimer l\'article !');
            }

            HTTPResponse::redirect('/admin/posts');
        } else {
            throw new \Exception('Le post n\'a pas été trouvé ou l\'ID est incorrect');
        }
    }
}
