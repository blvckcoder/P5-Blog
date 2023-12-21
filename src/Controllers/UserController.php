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

}