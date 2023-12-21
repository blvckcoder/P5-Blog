<?php

namespace App\Controllers; 

use App\Lib\Twig;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;

class PostController
{
    public $twig;

    public function __construct()
    {
        $this->twig = new Twig();
    }

    public function index()
    {
        $postRepository = new PostRepository();
        $posts = $postRepository->getAll();
        //limiter à 3

        echo $this->twig->getTwig()->render('frontend/home.twig', [
            'posts' => $posts
        ]);
    }

    public function displayPosts()
    {
        $postRepository = new PostRepository();
        $posts = $postRepository->getAll();
        //ajouter pagination

        echo $this->twig->getTwig()->render('frontend/blog.twig', [
            'posts' => $posts
        ]);
    }

    public function displayPost(array $params)
    {
        $postId = $params['id'];

        $postRepository = new PostRepository();
        $post = $postRepository->getById($postId);

        $commentRepository = new CommentRepository();
        $comments = $commentRepository->getAllBy($postId);
        //récupérer tags + category

        echo $this->twig->getTwig()->render('frontend/post.twig', [
            'post' => $post,
            'comments' => $comments
        ]);
    }

    public function displayAdminPosts()
    {
        $postRepository = new PostRepository();
        $posts = $postRepository->getAll();
 
        echo $this->twig->getTwig()->render('backend/posts.twig', [
            'posts' => $posts
        ]);
    }




}