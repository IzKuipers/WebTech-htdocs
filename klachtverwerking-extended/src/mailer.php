<?php

require_once "../vendor/autoload.php";
require_once "./env.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendComplaint($name, $email, $message) {
  $subject = "Uw klacht is in behandeling.";
  $safeMessage = htmlspecialchars($message);

  $mail = new PHPMailer(true);

  try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = MAILER_LOGIN;
    $mail->Password   = MAILER_SECRET;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
  
    $mail->setFrom($email);
    $mail->addAddress($email, $name);
    $mail->addCC('izaak.kuipers@gmail.com');
  
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = "<ul><li>Naam: $name</li><li>E-mail: $email</li></ul><hr>$safeMessage";
    $mail->AltBody = "Naam: $name\nE-mail: $email\n\n$message";
  
    $mail->send();
  } catch (Exception $e) {
    echo "Something went wrong: " . $e->getMessage();
  }
}
