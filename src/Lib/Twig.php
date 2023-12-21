<?php

namespace App\Lib;

use Twig\Extra\String\StringExtension;

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
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        $this->twig->addExtension(new StringExtension());
    }

    public function getTwig()
    {
        return $this->twig;
    }
}