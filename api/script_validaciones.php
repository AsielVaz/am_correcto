<?php

include_once('adminEmpresas.php');
include_once('adminNotificaciones.php');
include_once('adminNotificacionesAviso.php');

class Aviso
{
    public $estatus;
    public $mensaje;
    public $subMensaje;
    public $nombres;
}

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





function validarCampos()
{
    $adminEmpresas = new AdministradorEmpresa();
    $adminNotificaicones = new AdminNotificaciones();
    $empresas = $adminEmpresas->dameEmpresas();
    foreach ($empresas as $empresa) {
        if ($empresa->razon == "" || $empresa->razon == null || $empresa->razon == "NULL" || $empresa->razon == "null") {
            if (!$adminNotificaicones->existeNotificacion($empresa->id, "La razón social de la empresa no se encuentra actualizada ")) {
                $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "La razón social de la empresa no se encuentra actualizada ");
            }
        } else {
            $adminNotificaicones->eliminarNotificacionPorCompletada($empresa->id, "La razón social de la empresa no se encuentra actualizada ");
        }
        if ($empresa->rfc == "" || $empresa->rfc == null || $empresa->rfc == "NULL" || $empresa->rfc == "null") {
            if (!$adminNotificaicones->existeNotificacion($empresa->id, "El RFC de la empresa no se encuentra actualizado")) {
                $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "El RFC de la empresa no se encuentra actualizado");
            }
        } else {
            $adminNotificaicones->eliminarNotificacionPorCompletada($empresa->id, "El RFC de la empresa no se encuentra actualizado");
        }
        if ($empresa->sitioWeb == "" || $empresa->sitioWeb == null || $empresa->sitioWeb == "NULL" || $empresa->sitioWeb == "null") {
            if (!$adminNotificaicones->existeNotificacion($empresa->id, "El sitio web de la empresa no se encuentra actualizado")) {
                $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "El sitio web de la empresa no se encuentra actualizado");
            }
        } else {
            $adminNotificaicones->eliminarNotificacionPorCompletada($empresa->id, "El sitio web de la empresa no se encuentra actualizado");
        }
        if ($empresa->calle == "" || $empresa->calle == null || $empresa->calle == "NULL" || $empresa->calle == "null") {
            if (!$adminNotificaicones->existeNotificacion($empresa->id, "La calle de la empresa no se encuentra actualizada")) {
                $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "La calle de la empresa no se encuentra actualizada");
            }
        } else {
            $adminNotificaicones->eliminarNotificacionPorCompletada($empresa->id, "La calle de la empresa no se encuentra actualizada");
        }
        if ($empresa->numero == "" || $empresa->numero == null || $empresa->numero == "NULL" || $empresa->numero == "null") {
            if (!$adminNotificaicones->existeNotificacion($empresa->id, "El número de la empresa no se encuentra actualizado")) {
                $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "El número de la empresa no se encuentra actualizado");
            }
        } else {
            $adminNotificaicones->eliminarNotificacionPorCompletada($empresa->id, "El número de la empresa no se encuentra actualizado");
        }
        if ($empresa->colonia == "" || $empresa->colonia == null || $empresa->colonia == "NULL" || $empresa->colonia == "null") {
            if (!$adminNotificaicones->existeNotificacion($empresa->id, "La colonia de la empresa no se encuentra actualizada")) {
                $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "La colonia de la empresa no se encuentra actualizada");
            }
        } else {
            $adminNotificaicones->eliminarNotificacionPorCompletada($empresa->id, "La colonia de la empresa no se encuentra actualizada");
        }
        if ($empresa->cp == "" || $empresa->cp == null || $empresa->cp == "NULL" || $empresa->cp == "null") {
            if (!$adminNotificaicones->existeNotificacion($empresa->id, "El código postal de la empresa no se encuentra actualizado")) {
                $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "El código postal de la empresa no se encuentra actualizado");
            }
        } else {
            $adminNotificaicones->eliminarNotificacionPorCompletada($empresa->id, "El código postal de la empresa no se encuentra actualizado");
        }
        if ($empresa->estado == "" || $empresa->estado == null || $empresa->estado == "NULL" || $empresa->estado == "null") {
            if (!$adminNotificaicones->existeNotificacion($empresa->id, "El estado de la empresa no se encuentra actualizado")) {
                $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "El estado de la empresa no se encuentra actualizado");
            }
        } else {
            $adminNotificaicones->eliminarNotificacionPorCompletada($empresa->id, "El estado de la empresa no se encuentra actualizado");
        }
        if ($empresa->telefono == "" || $empresa->telefono == null || $empresa->telefono == "NULL" || $empresa->telefono == "null") {
            if (!$adminNotificaicones->existeNotificacion($empresa->id, "El teléfono de la empresa no se encuentra actualizado")) {
                $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "El teléfono de la empresa no se encuentra actualizado");
            }
        } else {
            $adminNotificaicones->eliminarNotificacionPorCompletada($empresa->id, "El teléfono de la empresa no se encuentra actualizado");
        }
        if ($empresa->correo == "" || $empresa->correo == null || $empresa->correo == "NULL" || $empresa->correo == "null") {
            if (!$adminNotificaicones->existeNotificacion($empresa->id, "El correo de la empresa no se encuentra actualizado")) {
                $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "El correo de la empresa no se encuentra actualizado");
            }
        } else {
            $adminNotificaicones->eliminarNotificacionPorCompletada($empresa->id, "El correo de la empresa no se encuentra actualizado");
        }
        if ($empresa->logo == "" || $empresa->logo == null || $empresa->logo == "NULL" || $empresa->logo == "null") {
            if (!$adminNotificaicones->existeNotificacion($empresa->id, "El logo de la empresa no se encuentra actualizado")) {
                $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "El logo de la empresa no se encuentra actualizado");
            }
        } else {
            $adminNotificaicones->eliminarNotificacionPorCompletada($empresa->id, "El logo de la empresa no se encuentra actualizado");
        }
        if ($empresa->pdf == "" || $empresa->pdf == null || $empresa->pdf == "NULL" || $empresa->pdf == "null" || $empresa->pdf == "-") {
            if (!$adminNotificaicones->existeNotificacion($empresa->id, "El documento 32D de la empresa no se encuentra actualizado")) {
                $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "El documento 32D de la empresa no se encuentra actualizado");
            }
        } else {
            $adminNotificaicones->eliminarNotificacionPorCompletada($empresa->id, "El documento 32D de la empresa no se encuentra actualizado");
        }
        if ($empresa->constanciaSf == "" || $empresa->constanciaSf == null || $empresa->constanciaSf == "NULL" || $empresa->constanciaSf == "null" || $empresa->constanciaSf == "-") {
            if (!$adminNotificaicones->existeNotificacion($empresa->id, "La constancia de la empresa no se encuentra actualizada")) {
                $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "La constancia de la empresa no se encuentra actualizada");
            }
        } else {
            $adminNotificaicones->eliminarNotificacionPorCompletada($empresa->id, "La constancia de la empresa no se encuentra actualizada");
        }
        if ($empresa->comprobanteDom == "" || $empresa->comprobanteDom == null || $empresa->comprobanteDom == "NULL" || $empresa->comprobanteDom == "null" || $empresa->comprobanteDom == "-") {
            if (!$adminNotificaicones->existeNotificacion($empresa->id, "El comprobante de domicilio de la empresa no se encuentra actualizado")) {
                $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "El comprobante de domicilio de la empresa no se encuentra actualizado");
            }
        } else {
            $adminNotificaicones->eliminarNotificacionPorCompletada($empresa->id, "El comprobante de domicilio de la empresa no se encuentra actualizado");
        }
    }
}

function notificarPdfAtrasado()
{
    $adminNotificaicones = new AdminNotificaciones();
    $adminEmpresas = new AdministradorEmpresa();
    $empresas = $adminEmpresas->dameEmpreasConPdfAtrasado();
    foreach ($empresas as $empresa) {
        if (!$adminNotificaicones->existeNotificacion($empresa->id, "El documento 32D de la empresa está atrasado")) {
            $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "El documento 32D de la empresa está atrasado");
        }
    }
}


function notificarPeriodos()
{
    $adminEmpresas = new AdministradorEmpresa();
    $adminAvisos = new AdminAviso();
    $adminNotificaicones = new AdminNotificaciones();
    $aviso = new Aviso();
    $aviso->estatus = "Nada";
    $aviso->mensaje = "No tienes tareas pendientes";

    $tareas = " - ";
    $empresas = $adminEmpresas->dameEmpresas();
    foreach ($empresas as $empresa) {
        $hoy = date("Y-m-d");
        $avisoTelefono = $adminAvisos->dameUltimoAviso($empresa->id, "telefono");
        $avisoCorreo = $adminAvisos->dameUltimoAviso($empresa->id, "correo");
        $avisoSitio = $adminAvisos->dameUltimoAviso($empresa->id, "sitio");
        $avisoDireccion = $adminAvisos->dameUltimoAviso($empresa->id, "direccion");
        switch ($empresa->periodoTel) {
            case "1 dia":
                $envioTel = date("Y-m-d", strtotime($avisoCorreo->notificado . "+ 1 day"));
                break;
            case "1 sem":
                $envioTel = date("Y-m-d", strtotime($avisoTelefono->notificado . "+ 1 week"));
                break;
            case "2 sem":
                $envioTel = date("Y-m-d", strtotime($avisoTelefono->notificado . "+ 2 week"));
                break;
            case "1 mes":
                $envioTel = date("Y-m-d", strtotime($avisoTelefono->notificado . "+ 1 month"));
                break;
            case "2 mes":
                $envioTel = date("Y-m-d", strtotime($avisoTelefono->notificado . "+ 2 month"));
                break;
            case "3 mes":
                $envioTel = date("Y-m-d", strtotime($avisoTelefono->notificado . "+ 3 month"));
                break;
        }

        switch ($empresa->periodoCorreo) {
            case "1 dia":
                $envioCorreo = date("Y-m-d", strtotime($avisoCorreo->notificado . "+ 1 day"));
                break;
            case "1 sem":
                $envioCorreo = date("Y-m-d", strtotime($avisoCorreo->notificado . "+ 1 week"));
                break;
            case "2 sem":
                $envioCorreo = date("Y-m-d", strtotime($avisoCorreo->notificado . "+ 2 week"));
                break;
            case "1 mes":
                $envioCorreo = date("Y-m-d", strtotime($avisoCorreo->notificado . "+ 1 month"));
                break;
            case "2 mes":
                $envioCorreo = date("Y-m-d", strtotime($avisoCorreo->notificado . "+ 2 month"));
                break;
            case "3 mes":
                $envioCorreo = date("Y-m-d", strtotime($avisoCorreo->notificado . "+ 3 month"));
                break;
        }

        switch ($empresa->periodoDireccion) {
            case "1 dia":
                $envioDireccion = date("Y-m-d", strtotime($avisoDireccion->notificado . "+ 1 day"));
                break;
            case "1 sem":
                $envioDireccion = date("Y-m-d", strtotime($avisoDireccion->notificado . "+ 1 week"));
                break;
            case "2 sem":
                $envioDireccion = date("Y-m-d", strtotime($avisoDireccion->notificado . "+ 2 week"));
                break;
            case "1 mes":
                $envioDireccion = date("Y-m-d", strtotime($avisoDireccion->notificado . "+ 1 month"));
                break;
            case "2 mes":
                $envioDireccion = date("Y-m-d", strtotime($avisoDireccion->notificado . "+ 2 month"));
                break;
            case "3 mes":
                $envioDireccion = date("Y-m-d", strtotime($avisoDireccion->notificado . "+ 3 month"));
                break;
        }

        switch ($empresa->periodoWeb) {
            case "1 dia":
                $envioSitio = date("Y-m-d", strtotime($avisoSitio->notificado . "+ 1 day"));
                break;
            case "1 sem":
                $envioSitio = date("Y-m-d", strtotime($avisoSitio->notificado . "+ 1 week"));
                break;
            case "2 sem":
                $envioSitio = date("Y-m-d", strtotime($avisoSitio->notificado . "+ 2 week"));
                break;
            case "1 mes":
                $envioSitio = date("Y-m-d", strtotime($avisoSitio->notificado . "+ 1 month"));
                break;
            case "2 mes":
                $envioSitio = date("Y-m-d", strtotime($avisoSitio->notificado . "+ 2 month"));
                break;
            case "3 mes":
                $envioSitio = date("Y-m-d", strtotime($avisoSitio->notificado . "+ 3 month"));
                break;
        }

        if ($hoy > $envioTel) {
            $adminAvisos->agregarAviso($empresa->id, "telefono");
            $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "Recuerda revisar el teléfono de la empresa");
            foreach ($empresa->correos as $correo) {
                $mail_destino = $correo->correo;
                $nombre_destino = $empresa->razon;
                $direccion = $empresa->calle . ' ' . $empresa->numero . ', ' . $empresa->colonia . ', ' . $empresa->cp . ', ' . $empresa->estado;
                $asunto = "Recordatorio de revisión de teléfono";
                $mensaje = "Hola, este es un recordatorio para la revision de la direccion " . $empresa->calle . " de la empresa " . $empresa->razon . " con RFC " . $empresa->rfc . " y con domicilio en " . $direccion;
                $attachment = "";
                $attachment_name = "";
                $mail_oculto = "";
                mailer($mail_destino, $nombre_destino, $asunto, $mensaje, $attachment, $attachment_name, $mail_oculto);
            }
            $aviso->estatus = "Existe";
            $aviso->mensaje = "Tienes tareas pendientes";
            $aviso->nombres[] = "Telefono";
        }
        if ($hoy > $envioCorreo) {
            $adminAvisos->agregarAviso($empresa->id, "correo");
            $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "Recuerda revisar el correo de la empresa");
            foreach ($empresa->correos as $correo) {
                $mail_destino = $correo->correo;
                $nombre_destino = $empresa->razon;
                $asunto = "Recordatorio de pago";
                $direccion = $empresa->calle . ' ' . $empresa->numero . ', ' . $empresa->colonia . ', ' . $empresa->cp . ', ' . $empresa->estado;
                $mensaje = "Hola, este es un recordatorio para la revision de la direccion " . $empresa->calle . " de la empresa " . $empresa->razon . " con RFC " . $empresa->rfc . " y con domicilio en " . $direccion;
                $attachment = "";
                $attachment_name = "";
                $mail_oculto = "";
                mailer($mail_destino, $nombre_destino, $asunto, $mensaje, $attachment, $attachment_name, $mail_oculto);
            }
            $aviso->estatus = "Existe";
            $aviso->mensaje = "Tienes tareas pendientes";
            $aviso->nombres[] = "Correo";
        }
        if ($hoy > $envioDireccion) {
            $adminAvisos->agregarAviso($empresa->id, "direccion");
            $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "Recuerda revisar la dirección de la empresa");
            foreach ($empresa->correos as $correo) {
                $mail_destino = $correo->correo;
                $nombre_destino = $empresa->razon;
                $asunto = "Recordatorio de pago";
                $direccion = $empresa->calle . ' ' . $empresa->numero . ', ' . $empresa->colonia . ', ' . $empresa->cp . ', ' . $empresa->estado;
                $mensaje = "Hola, este es un recordatorio para la revision de la direccion " . $empresa->calle . " de la empresa " . $empresa->razon . " con RFC " . $empresa->rfc . " y con domicilio en " . $direccion;
                $attachment = "";
                $attachment_name = "";
                $mail_oculto = "";
                mailer($mail_destino, $nombre_destino, $asunto, $mensaje, $attachment, $attachment_name, $mail_oculto);
            }
            $aviso->estatus = "Existe";
            $aviso->mensaje = "Tienes tareas pendientes";
            $aviso->nombres[] = "Direccion";
        }
        if ($hoy > $envioSitio) {
            $adminAvisos->agregarAviso($empresa->id, "sitio");
            $adminNotificaicones->nuevaNotificacion(1, $empresa->id, "Recuerda revisar el sitio web de la empresa");
            foreach ($empresa->correos as $correo) {
                $mail_destino = $correo->correo;
                $nombre_destino = $empresa->razon;
                $asunto = "Recordatorio de pago";
                $direccion = $empresa->calle . ' ' . $empresa->numero . ', ' . $empresa->colonia . ', ' . $empresa->cp . ', ' . $empresa->estado;
                $mensaje = "Hola, este es un recordatorio para la revision del ditio Web " . $empresa->sitioWeb . " de la empresa " . $empresa->razon . " con RFC " . $empresa->rfc . " y con domicilio en " . $direccion;
                $attachment = "";
                $attachment_name = "";
                $mail_oculto = "";
                mailer($mail_destino, $nombre_destino, $asunto, $mensaje, $attachment, $attachment_name, $mail_oculto);
            }
            $aviso->estatus = "Existe";
            $aviso->mensaje = "Tienes tareas pendientes";
            $aviso->nombres[] = "Sitio Web";
        }
    }

    echo json_encode($aviso);
}

function despost()
{
    $hoy = date("Y-m-d");
    $adminNotificaciones = new AdminNotificaciones();
    $notificaciones = $adminNotificaciones->dameNotificacionesPostergadas();
    foreach ($notificaciones as $notificacion) {
        if ($notificacion->fechaAsignacion < $hoy) {
            //echo $notificacion->fechaAsignacion . " Contra " . $hoy;
            $adminNotificaciones->recuperarNotificacion($notificacion->id);
        }
    }
}




validarCampos();
notificarPdfAtrasado();
notificarPeriodos();
despost();
