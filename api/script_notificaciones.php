<?php

include_once('adminEmpresas.php');


use PHPMailer\PHPMailer\PHPMailer;

function mailer($mail_destino, $nombre_destino, $asunto, $mensaje, $attachment, $attachment_name, $mail_oculto)
{
    require_once dirname(__DIR__) . '/bootstrap.php';
    $mail_host = (string) env('SMTP_HOST', '');
    $smtp_secure = (string) env('SMTP_SECURE', 'ssl');
    $mail_port = (int) env('SMTP_PORT', 465);
    $mail_email = (string) env('SMTP_EMAIL', '');
    $mail_password = (string) env('SMTP_PASSWORD', '');
    $mail_username = (string) env('SMTP_FROM_NAME', 'Sistema AM');
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




function avisarDia()
{
    $adminEmpresas = new AdministradorEmpresa();
    $hoy = date("Y-m-d");
    $empresas = $adminEmpresas->dameEmpresasPorFecha($hoy);
    foreach ($empresas as $empresa) {
        foreach ($empresa->correos as $correo) {
            $mail_destino = $correo->correo;
            $nombre_destino = $empresa->razon;
            $asunto = "Recordatorio de pago";
            $mensaje = "Hola, este es un recordatorio de pago para el dominio " . $empresa->sitioWeb . " de la empresa " . $empresa->razon . " con RFC " . $empresa->rfc . " y con fecha de vencimiento " . $empresa->finDominio . " (el dia de hoy)";
            $attachment = "";
            $attachment_name = "";
            $mail_oculto = "";
            mailer($mail_destino, $nombre_destino, $asunto, $mensaje, $attachment, $attachment_name, $mail_oculto);
        }
    }
}

function avisar7Dias()
{
    $adminEmpresas = new AdministradorEmpresa();
    $hoy = date("Y-m-d");
    $hoy = strtotime($hoy);
    $hoy = strtotime("-7 day", $hoy);
    $hoy = date("Y-m-d", $hoy);
    $empresas = $adminEmpresas->dameEmpresasPorFecha($hoy);
    foreach ($empresas as $empresa) {
        foreach ($empresa->correos as $correo) {
            $mail_destino = $correo->correo;
            $nombre_destino = $empresa->razon;
            $asunto = "Recordatorio de pago";
            $mensaje = "Hola, este es un recordatorio de pago para el dominio " . $empresa->sitioWeb . " de la empresa " . $empresa->razon . " con RFC " . $empresa->rfc . " y con fecha de vencimiento " . $empresa->finDominio . " (en 7 dias)";
            $attachment = "";
            $attachment_name = "";
            $mail_oculto = "";
            mailer($mail_destino, $nombre_destino, $asunto, $mensaje, $attachment, $attachment_name, $mail_oculto);
        }
    }
}

function avisar15Dias()
{
    $adminEmpresas = new AdministradorEmpresa();
    $hoy = date("Y-m-d");
    $hoy = strtotime($hoy);
    $hoy = strtotime("-15 day", $hoy);
    $hoy = date("Y-m-d", $hoy);
    $empresas = $adminEmpresas->dameEmpresasPorFecha($hoy);
    foreach ($empresas as $empresa) {
        foreach ($empresa->correos as $correo) {
            $mail_destino = $correo->correo;
            $nombre_destino = $empresa->razon;
            $asunto = "Recordatorio de pago";
            $mensaje = "Hola, este es un recordatorio de pago para el dominio " . $empresa->sitioWeb . " de la empresa " . $empresa->razon . " con RFC " . $empresa->rfc . " y con fecha de vencimiento " . $empresa->finDominio . " (en 15 dias)";
            $attachment = "";
            $attachment_name = "";
            $mail_oculto = "";
            mailer($mail_destino, $nombre_destino, $asunto, $mensaje, $attachment, $attachment_name, $mail_oculto);
        }
    }
}

function avisar30Dias()
{
    $adminEmpresas = new AdministradorEmpresa();
    $hoy = date("Y-m-d");
    $hoy = strtotime($hoy);
    $hoy = strtotime("-30 day", $hoy);
    $hoy = date("Y-m-d", $hoy);
    $empresas = $adminEmpresas->dameEmpresasPorFecha($hoy);
    foreach ($empresas as $empresa) {
        foreach ($empresa->correos as $correo) {
            $mail_destino = $correo->correo;
            $nombre_destino = $empresa->razon;
            $asunto = "Recordatorio de pago";
            $mensaje = "Hola, este es un recordatorio de pago para el dominio " . $empresa->sitioWeb . " de la empresa " . $empresa->razon . " con RFC " . $empresa->rfc . " y con fecha de vencimiento " . $empresa->finDominio . " (en 30 dias)";
            $attachment = "";
            $attachment_name = "";
            $mail_oculto = "";
            mailer($mail_destino, $nombre_destino, $asunto, $mensaje, $attachment, $attachment_name, $mail_oculto);
        }
    }
}

function avisar60Dias()
{
    $adminEmpresas = new AdministradorEmpresa();
    $hoy = date("Y-m-d");
    $hoy = strtotime($hoy);
    $hoy = strtotime("-60 day", $hoy);
    $hoy = date("Y-m-d", $hoy);
    $empresas = $adminEmpresas->dameEmpresasPorFecha($hoy);
    foreach ($empresas as $empresa) {
        foreach ($empresa->correos as $correo) {
            $mail_destino = $correo->correo;
            $nombre_destino = $empresa->razon;
            $asunto = "Recordatorio de pago";
            $mensaje = "Hola, este es un recordatorio de pago para el dominio " . $empresa->sitioWeb . " de la empresa " . $empresa->razon . " con RFC " . $empresa->rfc . " y con fecha de vencimiento " . $empresa->finDominio . " (en 60 dias)";
            $attachment = "";
            $attachment_name = "";
            $mail_oculto = "";
            mailer($mail_destino, $nombre_destino, $asunto, $mensaje, $attachment, $attachment_name, $mail_oculto);
        }
    }
}


avisarDia();
avisar7Dias();
avisar15Dias();
avisar30Dias();
avisar60Dias();
