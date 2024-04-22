<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entity\User;
use App\Lib\Hydrator;
use App\Lib\Pagination;
use App\Lib\HTTPResponse;
use App\Repository\UserRepository;

class UserController extends DefaultController
{
    public function displayAdminUsers(): void
    {
        $this->auth->checkAdmin();

        $itemsPerPage = 9;
        $currentPage = intval($_GET['page'] ?? 1);

        $userRepository = new UserRepository();
        $totalItems = $userRepository->count();

        $pagination = new Pagination($totalItems, $itemsPerPage, $currentPage);

        $users = $userRepository->getPaginated($itemsPerPage, $pagination->getOffset());

        $paginationHtml = $pagination->renderHtml();

        $flashMessage = $this->getFlash();

        echo $this->twig->getTwig()->render('backend/users.twig', [
            'users' => $users,
            'pagination' => $paginationHtml,
            'flashMessage' => $flashMessage
        ]);
    }

    public function createForm(): void
    {
        $this->auth->checkAdmin();
        echo $this->twig->getTwig()->render('backend/forms/addUser.twig');
    }

    public function create(array $params): void
    {
        $this->auth->checkAdmin();

        $postData = $_POST;

        $name = trim($postData['name'] ?? '');
        $firstname = trim($postData['firstname'] ?? '');
        $nickname = trim($postData['nickname'] ?? '');
        $mail = trim($postData['mail'] ?? '');
        $picture = $_FILES['picture'] ?? 'avatar.jpg';
        $password = trim($postData['password'] ?? '');
        $role = trim($postData['role'] ?? '');
        $status = trim($postData['status'] ?? '');

        if (empty($name) || empty($firstname) || empty($nickname) || empty($mail) || empty($picture) || empty($password) || empty($role) || empty($status)) {
            $this->addFlash('error', 'Les données du formulaire sont invalides.');
            HTTPResponse::redirect('/admin/users');
        }

        //fonction handler
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
                handleFileUpload($_FILES['picture'], $destinationFolder);
                $params['post']['picture'] = $_FILES['picture']['name'];
            }
        }

        $user = new User();
        $user = Hydrator::hydrate($params['post'], $user);

        $hashedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);

        $userRepository = new UserRepository();
        $success = $userRepository->create($user);

        if (!$success) {
            $this->addFlash('error', 'Impossible d\'ajouter l\'utilisateur !');
            HTTPResponse::redirect('/admin/users');
        } else {
            $this->addFlash('success', 'Utilisateur ajouté avec succès');
            HTTPResponse::redirect('/admin/users');
        }
    }

    public function updateForm(array $id): void
    {
        $this->auth->checkAdmin();
        $userId = (int)$id['id'];

        $userRepository = new UserRepository();
        $existingUser = $userRepository->getById($userId);

        if (!$existingUser) {
            header($_SERVER["SERVER_PROTOCOL"] . '404 Not Found');
            echo 'L\'utilisateur n\'existe pas 404 not found baby';
        }

        echo $this->twig->getTwig()->render('backend/forms/editUser.twig', [
            'user' => $existingUser
        ]);
    }


    public function update(array $id): void
    {
        $this->auth->checkAdmin();
        $userId = (int)$id['id'];

        $postData = $_POST;

        $name = trim($postData['name'] ?? '');
        $firstname = trim($postData['firstname'] ?? '');
        $nickname = trim($postData['nickname'] ?? '');
        $mail = trim($postData['mail'] ?? '');
        $password = trim($postData['password'] ?? '');
        $role = trim($postData['role'] ?? '');
        $status = trim($postData['status'] ?? '');

        if (empty($name) || empty($firstname) || empty($nickname) || empty($mail) || empty($password) || empty($role) || empty($status)) {
            $this->addFlash('error', 'Les données du formulaire sont vides ou invalides.');
            HTTPResponse::redirect('/admin/users');
        }


        //fonction handler
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
                handleFileUpload($_FILES['picture'], $destinationFolder);
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
                    $this->addFlash('error', 'Impossible de mettre à jour l\'utilisateur !');
                    HTTPResponse::redirect('/admin/users');
                } else {
                    $this->addFlash('succes', 'Mise à jour de l\'utilisateur réussie !');
                    HTTPResponse::redirect('/admin/users');
                }
            } else {
                $this->addFlash('error', 'Utilisateur non trouvé.');
                HTTPResponse::redirect('/admin/users');
            }
        }
    }


    public function delete(array $id): void
    {
        $this->auth->checkAdmin();
        $id = (int)$id['id'];

        $userRepository = new UserRepository();
        $user = $userRepository->getById($id);
        $success = $userRepository->delete($user);

        if (!$success) {
            $this->addFlash('error', 'Impossible de supprimer l\'utilisateur !');
            HTTPResponse::redirect('/admin/users');
        } else {
            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès !');
            HTTPResponse::redirect('/admin/users');
        }
    }
}
