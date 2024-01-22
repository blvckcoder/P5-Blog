<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Lib\HTTPResponse;
use App\Lib\Mail;
use PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ContactController
{
    public function handleForm(): void
    {
        $name = $_POST['name'];
        $subject = $_POST['subject'];
        $email = $_POST['email'];
        $message = $_POST['message'];

        if (Mail::send($name, $email, $subject, $message)) {
            HTTPResponse::redirect('/');
        } else {
            throw new \Exception('Impossible d\'envoyer le mail!');
        }
    }
}
