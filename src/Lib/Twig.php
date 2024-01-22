<?php

declare(strict_types=1);

namespace App\Lib;

use App\Lib\Auth;
use Twig\Extra\String\StringExtension;
use Twig\Extra\Intl\IntlExtension;

class Twig
{
    private $twig;
    private $loader;

    public function __construct(private Auth $auth)
    {
        $this->loader = new \Twig\Loader\FilesystemLoader('../templates');
        $this->twig = new \Twig\Environment($this->loader, [
            'debug' => true,
            'cache' => false, // __DIR__ . '/tmp'
        ]);

        $this->twig->addGlobal('isLoggedIn', isset($_SESSION['userId']) && $_SESSION['userId']);
        $this->twig->addGlobal('userId', $auth->getUserInfo() != null ? $auth->getUserInfo()->getId() : null);
        $this->twig->addGlobal('nickname', $auth->getUserInfo() != null ? $auth->getUserInfo()->getNickname() : 'Invité');
        $this->twig->addGlobal('picture', $auth->getUserInfo() != null ? $auth->getUserInfo()->getPicture() : 'avatar.jpg');


        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        $this->twig->addExtension(new StringExtension());
        $this->twig->addExtension(new IntlExtension());
    }

    public function getTwig(): \Twig\Environment
    {
        return $this->twig;
    }
}

