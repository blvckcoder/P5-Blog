<?php 
namespace App\Lib;

use App\Repository\UserRepository;
use App\Entity\User;

class Auth {
    private $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function login(string $email, string $password): bool {
        $user = $this->userRepository->getBy($email);
        if ($user && password_verify($password, $user->getPassword())) {
            $_SESSION['user_id'] = $user->getId();
            return true;
        }
        return false;
    }

    public function check(): bool {
        return isset($_SESSION['user_id']);
    }

    public function user(): ?User {
        if ($this->check()) {
            return $this->userRepository->getById($_SESSION['user_id']);
        }
        return null;
    }

    public function logout() {
        unset($_SESSION['user_id']);
    }
}
