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
        $flashMessage = $this->getFlash();

        echo $this->twig->getTwig()->render('auth/login.twig', [
            'flashMessage' => $flashMessage
        ]);
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if ($this->auth->login($email, $password)) {
                $user = $this->auth->user();

                if ($user->getRole() === 'admin') {
                    $this->addFlash('success', 'Connexion administrateur réussie !');
                    HTTPResponse::redirect('/admin');
                } else {
                    $this->addFlash('success', 'Connexion réussie !');
                    HTTPResponse::redirect('/');
                }
            } else {
                $this->addFlash('error', 'Identifiants ou mot de passe incorrects. Veuillez réessayer!');
                HTTPResponse::redirect('/login');
            }
        }

        $this->loginForm();
    }

    public function logout(): void
    {
        $this->auth->logout();

        $this->addFlash('success', 'Vous avez été déconnecté.');
        HTTPResponse::redirect('/login');
    }

    public function registerForm(): void
    {
        $flashMessage = $this->getFlash();
        echo $this->twig->getTwig()->render('auth/signup.twig', [
            'flashMessage' => $flashMessage
        ]);
    }

    public function register(): void
    {
        $postData = $_POST;

        $name = trim($postData['name'] ?? '');
        $firstname = trim($postData['firstname'] ?? '');
        $nickname = trim($postData['nickname'] ?? '');
        $mail = trim($postData['mail'] ?? '');
        $password = trim($postData['password'] ?? '');

        if (empty($name) || empty($firstname) || empty($nickname) || empty($mail) || empty($password)) {
            $this->addFlash('error', 'Les données du formulaire sont vides ou ne sont pas valides');
            HTTPResponse::redirect('/signup');
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
            $this->addFlash('error', 'Impossible d\'enregistrer votre inscription.');
            HTTPResponse::redirect('/signup');
        } else {
            $this->addFlash('success', 'Inscription réussie. Veuillez patienter, un administrateur validera votre compte');
            HTTPResponse::redirect('/');
        }
    }
}
