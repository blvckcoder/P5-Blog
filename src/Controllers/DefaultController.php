<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Lib\Auth;
use App\Lib\Twig;
use App\Lib\HTTPResponse;
use App\Lib\FlashMessage;
use App\Repository\UserRepository;

class DefaultController
{
    protected $auth;

    protected $flash;

    public $twig;

    public function __construct()
    {
        $userRepository = new UserRepository();
        $this->auth = new Auth($userRepository);
        $this->twig = new Twig($this->auth);
        $this->flash = new FlashMessage();
    }

    public function addFlash(string $type, string $message): void
    {
        $this->flash->addFlashMessage($type, $message);
    }

    public function getFlash(): array
    {
        return $this->flash->getFlashMessage();
    }
}
