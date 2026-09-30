<?php

use PHPMailer\PHPMailer\PHPMailer;


class Correo
{
  public $host;
  public $secure;
  public $port;
  public $email;
  public $password;
  public $username;

  public function __construct($host, $secure, $port, $email, $password, $username)
  {
    $this->host = $host;
    $this->secure = $secure;
    $this->port = $port;
    $this->email = $email;
    $this->password = $password;
    $this->username = $username;
  }



  public function mailer($mail_destino, $nombre_destino, $asunto, $mensaje, $attachment, $attachment_name, $mail_oculto)
  {
    $mail_host = $this->host;
    $smtp_secure = $this->secure;
    $mail_port = $this->port;
    $mail_email = $this->email;
    $mail_password = $this->password;
    $mail_username = $this->username;
    //Import PHPMailer classes into the global namespace
    require_once 'mailer/src/PHPMailer.php';
    require_once 'mailer/src/SMTP.php';
    require_once 'mailer/src/Exception.php';
    //Create a new PHPMailer instance
    $mail = new PHPMailer;
    //Tell PHPMailer to use SMTP
    $mail->isSMTP();
    //Whether to use SMTP authentication
    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;
    //Set the hostname of the mail server
    $mail->Host = $mail_host;
    $mail->Port = $mail_port;
    //Set the encryption system to use - ssl (deprecated) or tls
    $mail->SMTPSecure = $smtp_secure;
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    //Username to use for SMTP authentication - use full email address for gmail
    $mail->Username = $mail_email;
    //Password to use for SMTP authentication
    $mail->Password = $mail_password;
    //Set who the message is to be sent from
    $mail->setFrom($mail_email, $mail_username);
    //Set who the message is to be sent to
    if (is_array($mail_destino)) {
      for ($i = 0; $i <= count($mail_destino); $i++) {
        $mail->addAddress($mail_destino[$i], $nombre_destino[$i]);
      }
    } else {
      $mail->addAddress($mail_destino, $nombre_destino);
    }
    //Set CCO
    if (is_array($mail_oculto)) {
      for ($i = 0; $i <= count($mail_oculto); $i++) {
        $mail->addBCC($mail_oculto[$i]);
      }
    } else {
      $mail->addBCC($mail_oculto);
    }

    //Set the subject line
    $mail->Subject = $asunto;
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Body    = $mensaje;


    /*
    for ($i = 0; $i <= count($attachment); $i++) {
        $mail->AddAttachment($attachment[$i], $attachment_name[$i]);
    }

    */
    if (!$mail->send()) {
      return "Mailer Error: " . $mail->ErrorInfo;
    } else {
      return "Message sent!";
    }
  }
}
