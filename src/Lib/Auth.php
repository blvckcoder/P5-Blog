<?php

namespace App\Lib;

use App\Repository\UserRepository;
use App\Entity\User;

class Auth
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->userRepository->getBy($email);

        if ($user && password_verify($password, $user->getPassword())) {
/*             if (session_status() === PHP_SESSION_NONE) {
                session_start();
            } */
            $_SESSION['userId'] = $user->getId();
            $_SESSION['nickname'] = $user->getNickname();
            $_SESSION['picture'] = $user->getPicture();
            

            return true;
        }
        return false;
    }

    public function check(): bool
    {
        return isset($_SESSION['userId']);
    }

    public function user(): ?User
    {
        if ($this->check()) {
            return $this->userRepository->getById($_SESSION['userId']);
        }
        return null;
    }

    public function checkAdmin()
    {
        if (!$this->check()) {
            header('Location: /login');
            exit;
        }
        
        if ($this->user()->getRole() !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function logout()
    {
        unset($_SESSION['userId']);
        session_destroy();
    }
}
