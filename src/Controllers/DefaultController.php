<?php

namespace App\Controllers;

use App\Lib\Auth;
use App\Lib\Twig;
use App\Lib\HTTPResponse;
use App\Repository\UserRepository;

class DefaultController 
{
    protected $auth;

    public $twig;

    public function __construct()
    {
        $userRepository = new UserRepository();
        $this->auth = new Auth($userRepository);
        $this->twig = new Twig($this->auth);
    }

}