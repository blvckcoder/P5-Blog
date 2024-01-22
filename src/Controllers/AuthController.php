<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Lib\HTTPResponse;
use App\Lib\Hydrator;

class AuthController extends DefaultController
{
    public function loginForm(): void
    {
        echo $this->twig->getTwig()->render('auth/login.twig');
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if ($this->auth->login($email, $password)) {
                $user = $this->auth->user();

                if ($user->getRole() === 'admin') {
                    HTTPResponse::redirect('/admin');
                } else {
                    HTTPResponse::redirect('/');
                }
            } else {
                throw new \Exception('Identifiants incorrects. Veuillez réessayer.');
            }
        }

        $this->loginForm();
    }

    public function logout(): void
    {
        $this->auth->logout();

        HTTPResponse::redirect('/login');
    }

    public function registerForm(): void
    {
        echo $this->twig->getTwig()->render('auth/signup.twig');
    }

    public function register(): void
    {
        if (!isset($_POST['name'], $_POST['firstname'], $_POST['nickname'], $_POST['mail'], $_POST['password'])) {
            throw new \Exception('Les données du formulaire sont invalides.');
        } else {
            $_POST['biography'] = null;
            $_POST['picture'] = "avatar.jpg";
            $_POST['role'] = "user";
            $_POST['status'] = "inactive";
        }

        $user = new User();
        $user = Hydrator::hydrate($_POST, $user);

        $hashedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);

        $userRepository = new UserRepository();
        $success = $userRepository->create($user);

        if (!$success) {
            throw new \Exception('Impossible d\'ajouter l\'utilisateur !');
        } else {
            HTTPResponse::redirect('/admin/users');
        }
    }
}
