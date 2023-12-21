<?php

namespace App\Controllers;

use App\Lib\Database;
use App\Lib\Twig;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Lib\Hydrator;

class UserController
{
    public $twig;

    public function __construct()
    {
        $this->twig = new Twig();
    }

    public function displayAdminUsers()
    {
        $userRepository = new UserRepository();
        $users = $userRepository->getAll();

        echo $this->twig->getTwig()->render('backend/users.twig', [
            'users' => $users
        ]);

    }

    public function createForm()
    {
        echo $this->twig->getTwig()->render('backend/forms/addUser.twig');
    }

    public function create(array $params)
    {
        if (!isset($params['post']['name'], $params['post']['firstname'], $params['post']['nickname'], $params['post']['biography'], $params['post']['picture'], $params['post']['mail'], $params['post']['password'], $params['post']['role'], $params['post']['status'])) {
            throw new \Exception('Les données du formulaire sont invalides.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            function handleFileUpload(array $file, string $destinationFolder): string
            {
                $tmpFilePath = $file['tmp_name'];
                $originalFileName = $file['name'];
                $destinationFilePath = $destinationFolder . $originalFileName;
                move_uploaded_file($tmpFilePath, $destinationFilePath);
                return $destinationFilePath;
            }

            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                $destinationFolder = 'assets/img/profil/';
                $pictureFilePath = handleFileUpload($_FILES['picture'], $destinationFolder);
                // Enregistre $pictureFilePath en base de données ou effectue d'autres opérations nécessaires
                $params['post']['picture'] = $pictureFilePath;
            }

        }

        // Hydrate les données du formulaire dans un objet Post
        $user = new User();
        $user = Hydrator::hydrate($params['post'], $user);

        // Appel de la méthode create du PostRepository
        $userRepository = new UserRepository();
        $success = $userRepository->create($user);

        // Redirection après le succès
        if (!$success) {
            throw new \Exception('Impossible d\'ajouter l\'utilisateur !');
        } else {
            header('Location: /admin/users');
        }
    }

    public function updateForm(array $id)
    {
        $userId = (int)$id['id'];

        $userRepository = new UserRepository();
        $existingUser = $userRepository->getById($userId);

        if (!$existingUser) {
            header( $_SERVER["SERVER_PROTOCOL"] . '404 Not Found');
            echo 'L\'utilisateur n\'existe pas 404 not found baby';
            die();
        }

        echo $this->twig->getTwig()->render('backend/forms/editUser.twig', [
            'user' => $existingUser
        ]);
    }


    public function update(array $id)
    {
        $userId = (int)$id['id'];

        $postData = $_POST;
        
        if (!isset($postData['name'], $postData['firstname'], $postData['nickname'], $postData['biography'], $postData['mail'], $postData['password'], $postData['role'], $postData['status'])) {
            throw new \Exception('Les données du formulaire sont invalides.');
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                function handleFileUpload(array $file, string $destinationFolder): string
                {
                    $tmpFilePath = $file['tmp_name'];
                    $originalFileName = $file['name'];
                    $destinationFilePath = $destinationFolder . $originalFileName;
                    move_uploaded_file($tmpFilePath, $destinationFilePath);
                    return $destinationFilePath;
                }
    
                if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                    $destinationFolder = 'assets/img/profil/';
                    $pictureFilePath = handleFileUpload($_FILES['picture'], $destinationFolder);
                    $postData['picture'] = $_FILES['picture']['name'];
                }
            
            $userRepository = new UserRepository();
            $user = $userRepository->getById($userId);
    
            if ($user) {
                $user = Hydrator::hydrate($postData, $user);
                $success = $userRepository->update($user);
    
                if (!$success) {
                    throw new \Exception('Impossible de mettre à jour l\'utilisateur!');
                } else {
                    header('Location: /admin/users');
                }
            } else {
                throw new \Exception('Utilisateur non trouvé.');
            }
        }
        
    }


    public function delete(array $id)
    {
        $id = (int)$id['id'];

        $userRepository = new UserRepository();
        $user = $userRepository->getById($id);
        $success = $userRepository->delete($user);

        // Redirection après le succès
        if (!$success) {
            throw new \Exception('Impossible de supprimer l\'utilisateur !');
        } else {
            header('Location: /admin/users');
        }
    }

}