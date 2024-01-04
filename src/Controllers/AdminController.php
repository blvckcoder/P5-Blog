<?php

namespace App\Controllers;

use App\Lib\Auth;
use App\Lib\Twig;
use App\Repository\UserRepository;

class AdminController
{

    public $twig;

    private $auth;

    public function __construct()
    {
        $this->twig = new Twig();

        $userRepository = new UserRepository();
        $this->auth = new Auth($userRepository);
        $this->auth->checkAdmin();
    }


    public function index()
    {
        echo $this->twig->getTwig()->render('backend/home.twig');
    }

}