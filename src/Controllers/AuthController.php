<?php

namespace App\Controllers;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Lib\Hydrator;

class AuthController extends DefaultController
{
    public function loginForm()
    {
        echo $this->twig->getTwig()->render('auth/login.twig');
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if ($this->auth->login($email, $password)) {
                $user = $this->auth->user();

                if ($user->getRole() === 'admin') {
                    header('Location: /admin');
                } else {
                    header('Location: /');
                }
            } else {
                throw new \Exception('Identifiants incorrects. Veuillez réessayer.');
            }
        }

        return $this->loginForm();
    }

    public function logout()
    {
        $this->auth->logout();

        header('Location: /login');
    }

    public function registerForm()
    {
        echo $this->twig->getTwig()->render('auth/signup.twig');
    }

    public function register()
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
            header('Location: /admin/users');
        }
    }
}
