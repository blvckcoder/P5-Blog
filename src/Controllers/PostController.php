<?php

namespace App\Controllers; 

use App\Lib\Twig;
use App\Entity\Post;
use App\Lib\Hydrator;
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

    public function createForm()
    {
        echo $this->twig->getTwig()->render('backend/forms/addPost.twig');
    }

    public function create(array $params)
    {
        // Validation des données du formulaire
        if (!isset($params['post']['userId'], $params['post']['title'], $params['post']['excerpt'], $params['post']['content'], $params['post']['imgCover'], $params['post']['imgCard'])) {
            throw new \Exception('Les données du formulaire sont invalides.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement du téléchargement de l'image
            function handleFileUpload(array $file, string $destinationFolder): string
            {
                // Emplacement temporaire du fichier téléchargé
                $tmpFilePath = $file['tmp_name'];

                // Le nom du fichier d'origine
                $originalFileName = $file['name'];

                // Emplacement où tu veux sauvegarder le fichier
                  // Assure-toi que ce dossier existe et a les bonnes permissions
                $destinationFilePath = $destinationFolder . $originalFileName;

                // Déplace le fichier de l'emplacement temporaire vers l'emplacement de destination
                move_uploaded_file($tmpFilePath, $destinationFilePath);

                // À ce stade, $destinationFilePath contient le chemin complet du fichier téléchargé
                return $destinationFilePath;
            }

            if (isset($_FILES['imgCover']) && $_FILES['imgCover']['error'] === UPLOAD_ERR_OK) {
                $destinationFolder = 'assets/img/covers/';
                $imgCoverFilePath = handleFileUpload($_FILES['imgCover'], $destinationFolder);
                // Enregistre $imgCoverFilePath en base de données ou effectue d'autres opérations nécessaires
                $params['post']['imgCover'] = $imgCoverFilePath;
            }

            // Traitement du téléchargement de l'image imgCard
            if (isset($_FILES['imgCard']) && $_FILES['imgCard']['error'] === UPLOAD_ERR_OK) {
                $destinationFolder = 'assets/img/cards/';
                $imgCardFilePath = handleFileUpload($_FILES['imgCard'], $destinationFolder);
                // Enregistre $imgCardFilePath en base de données ou effectue d'autres opérations nécessaires
                $params['post']['imgCard'] = $imgCardFilePath;
            }

        }

        // Hydrate les données du formulaire dans un objet Post
        $post = new Post();
        $post = Hydrator::hydrate($params['post'], $post);

        // Appel de la méthode create du PostRepository
        $postRepository = new PostRepository();
        $success = $postRepository->create($post);

        // Redirection après le succès
        if (!$success) {
            throw new \Exception('Impossible d\'ajouter le commentaire !');
        } else {
            header('Location: /admin/posts');
        }
    }




}