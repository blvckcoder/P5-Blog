<?php

namespace App\Lib;

class HTTPResponse
{
    public static function redirect($url) 
    {
        header("Location: $url");
        return true;
    }
}