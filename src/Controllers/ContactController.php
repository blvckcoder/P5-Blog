<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Lib\HTTPResponse;
use App\Lib\Mail;
use App\Lib\FlashMessage;
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
            $flash = new FlashMessage;
            $flash->addFlashMessage('error', 'Impossible d\'envoyer le mail !');
            HTTPResponse::redirect('/'); 
        }
    }
}
