<?php

namespace App\Controllers;

use App\Lib\Twig;

class AdminController
{
    public $twig;

    public function __construct()
    {
        $this->twig = new Twig();
    }


    public function index()
    {
        echo $this->twig->getTwig()->render('backend/home.twig');
    }

}