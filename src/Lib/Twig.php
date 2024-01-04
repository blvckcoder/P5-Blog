<?php

namespace App\Lib;

use Twig\Extra\String\StringExtension;
use Twig\Extra\Intl\IntlExtension;

class Twig
{
    private $twig;
    private $loader;

    public function __construct()
    {
        $this->loader = new \Twig\Loader\FilesystemLoader('../templates');
        $this->twig = new \Twig\Environment($this->loader, [
            'debug' => true,
            'cache' => false, // __DIR__ . '/tmp'
        ]);

        $this->twig->addGlobal('isLoggedIn', isset($_SESSION['userId']) && $_SESSION['userId']);
        $this->twig->addGlobal('userId', $_SESSION['userId'] ?? null);
        $this->twig->addGlobal('nickname', $_SESSION['nickname'] ?? 'Invité');
        $this->twig->addGlobal('picture', $_SESSION['picture'] ?? 'avatar.jpg');


        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        $this->twig->addExtension(new StringExtension());
        $this->twig->addExtension(new IntlExtension());
    }

    public function getTwig()
    {
        return $this->twig;
    }
}