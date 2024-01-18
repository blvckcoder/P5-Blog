<?php

namespace App\Lib;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    public static function send($name, $email, $subject, $message)
    {
        $mail = new PHPMailer(True);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Votre serveur SMTP
            $mail->SMTPAuth = true;
            $mail->Username = ''; // Votre nom d'utilisateur SMTP
            $mail->Password = ''; // Votre mot de passe SMTP
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            
            

            // Destinataires
            $mail->setFrom($email, $name);
            $mail->addAddress('');

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = '<p>Vous avez reçu un mail du formulaire de contact du blog Blvckcoder. <br><br>Mail de : ' . $name . '<br>Email: ' . $email .  '<br><br> Message : ' . $message . '</p>';

            $mail->send();
            $mail->SMTPDebug = 1;

            return true;
        } catch (Exception $e) {
            throw new \Exception('Le message n\'a pas pu être envoyé: ' . $mail->ErrorInfo);
        }
    }
}
