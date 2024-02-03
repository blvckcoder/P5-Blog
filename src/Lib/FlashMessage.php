<?php

namespace App\Lib;

class FlashMessage
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function addFlashMessage(string $type, string $message): void
    {
        $_SESSION['flashMessage'][$type][] = $message;
    }

    public function getFlashMessage(): array
    {
        $messages = $_SESSION['flashMessage'] ?? [];
        unset($_SESSION['flashMessage']);
        return $messages;
    }
}
