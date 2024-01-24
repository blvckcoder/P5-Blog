<?php

declare(strict_types=1);

namespace App\Lib;

class HTTPResponse
{
    public static function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }
}

