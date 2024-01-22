<?php

declare(strict_types=1);

namespace App\Lib;

use App\Lib\HTTPResponse;
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
            $_SESSION['userId'] = $user->getId();
            
            return true;
        }
        return false;
    }

    public function getUserInfo()
    {
        if(isset($_SESSION['userId']) && !is_null($_SESSION['userId'])) {
            return $this->userRepository->getById($_SESSION['userId']);
        }

        return null;
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
            HTTPResponse::redirect('/login');
        }
        
        if ($this->user()->getRole() !== 'admin') {
            HTTPResponse::redirect('/');
        }
    }

    public function logout()
    {
        session_destroy();
    }
}
