<?php

namespace App\Controllers;

use App\Repository\UserRepository;

class AdminController extends DefaultController
{
    public function __construct()
    {
        parent::__construct();
        $this->auth->checkAdmin();
    }


    public function index()
    {
        $user = $this->auth->getUserInfo();

        echo $this->twig->getTwig()->render('backend/home.twig', [
            'user' => $user
        ]
    );
    }

}