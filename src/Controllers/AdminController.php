<?php

namespace App\Controllers;

class AdminController extends DefaultController
{
    public function __construct()
    {
        parent::__construct();
        $this->auth->checkAdmin();
    }


    public function index()
    {
        echo $this->twig->getTwig()->render('backend/home.twig');
    }

}