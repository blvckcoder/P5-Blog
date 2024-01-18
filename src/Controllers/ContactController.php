<?php

namespace App\Controllers;

use App\Lib\Mail;
use PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ContactController
{
    public function handleForm()
    {
        $name = $_POST['name'];
        $subject = $_POST['subject'];
        $email = $_POST['email'];
        $message = $_POST['message'];

        if (Mail::send($name, $email, $subject, $message)) {
            header("Location: /");
        } else {
            echo 'echec';
        }
    }
}