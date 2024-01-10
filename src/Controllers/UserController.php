<?php

namespace App\Controllers;

use App\Entity\User;
use App\Lib\Hydrator;
use App\Lib\Pagination;
use App\Repository\UserRepository;

class UserController extends DefaultController
{
    public function displayAdminUsers()
    {
        $this->auth->checkAdmin();
        
        $itemsPerPage = 9;
        $currentPage = intval($_GET['page'] ?? 1);

        $userRepository = new UserRepository();
        $totalItems = $userRepository->count();

        $pagination = new Pagination($totalItems, $itemsPerPage, $currentPage);

        $users = $userRepository->getPaginated($itemsPerPage, $pagination->getOffset());

        $paginationHtml = $pagination->renderHtml();

        echo $this->twig->getTwig()->render('backend/users.twig', [
            'users' => $users,
            'pagination' => $paginationHtml
        ]);
    }

    public function createForm()
    {
        $this->auth->checkAdmin();
        echo $this->twig->getTwig()->render('backend/forms/addUser.twig');
    }

    public function create(array $params)
    {
        $this->auth->checkAdmin();
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
                $params['post']['picture'] = $pictureFilePath;
            }
        }

        $user = new User();
        $user = Hydrator::hydrate($params['post'], $user);

        $hashedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);

        $userRepository = new UserRepository();
        $success = $userRepository->create($user);

        if (!$success) {
            throw new \Exception('Impossible d\'ajouter l\'utilisateur !');
        } else {
            header('Location: /admin/users');
        }
    }

    public function updateForm(array $id)
    {
        $this->auth->checkAdmin();
        $userId = (int)$id['id'];

        $userRepository = new UserRepository();
        $existingUser = $userRepository->getById($userId);

        if (!$existingUser) {
            header($_SERVER["SERVER_PROTOCOL"] . '404 Not Found');
            echo 'L\'utilisateur n\'existe pas 404 not found baby';
            die();
        }

        echo $this->twig->getTwig()->render('backend/forms/editUser.twig', [
            'user' => $existingUser
        ]);
    }


    public function update(array $id)
    {
        $this->auth->checkAdmin();
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

                $hashedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);
                $user->setPassword($hashedPassword);
                
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
        $this->auth->checkAdmin();
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
