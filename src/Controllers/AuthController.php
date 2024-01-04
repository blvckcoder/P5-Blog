<?php

namespace App\Controllers;

use App\Lib\Auth;
use App\Lib\Twig;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Lib\Hydrator;

class AuthController
{
    private $auth;

    public $twig;

    public function __construct()
    {
        $this->twig = new Twig();
        
        $userRepository = new UserRepository();
        $this->auth = new Auth($userRepository);
    }

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
                    var_dump($user->getRole());

                    header('Location: /admin');
                } else {
                    header('Location: /');
                }
                exit;
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
        exit;
    }

    public function registerForm()
    {
        echo $this->twig->getTwig()->render('auth/signup.twig');
    }

    public function register()
    {
        /* var_dump($_POST['name']);
        die;  */
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
            //if admin → backend, if user → home
            header('Location: /admin/users');
        }
    }
}
