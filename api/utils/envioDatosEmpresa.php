<?php
include_once('adminEmpresas.php');



use PHPMailer\PHPMailer\PHPMailer;

function mailer($mail_destino, $nombre_destino, $asunto, $mensaje, $attachment, $attachment_name, $mail_oculto)
{
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
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



    for ($i = 0; $i <= count($attachment); $i++) {
        $mail->AddAttachment($attachment[$i]);
    }


    if (!$mail->send()) {
        return array('error' => true, 'message' => 'Mailer Error: ' . $mail->ErrorInfo);
    } else {
        return array('error' => false, 'message' => 'Message sent!');
    }
}



$adminEmpresas = new AdministradorEmpresa();

$idEmpresa = $_POST['idEmpresa'];
$email = $_POST['correo'];


$empresa = $adminEmpresas->dameEmpresa($idEmpresa);
$cuentas = $adminEmpresas->dameCuentaPorEmpresa($empresa->razon);

$mensaje = '
<div style="text-align: center; width: 100px !important; height: 100px !important"><img src="https://10h.mx/img/logo10h.png" target="_blank" class="img-fluid" style=" width: 100px !important; height: 100px !important"></div>
<p id="isPasted" style="text-align: center; margin: 0cm 0cm 8pt; color: rgb(0, 0, 0); line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><br></p>
<p id="isPasted" style="text-align: center; margin: 0cm 0cm 8pt; color: rgb(0, 0, 0); line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;">Información de la Empresa ' . $empresa->razon . '<span style="background-color: rgba(var(--mdb-white-rgb),var(--mdb-bg-opacity)); font-weight: var(--mdb-body-font-weight);">:</span></p>
<p style="margin: 0cm 0cm 8pt; line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><br></p>
<p style="margin: 0cm 0cm 8pt; line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><br></p>
<p style="margin: 0cm 0cm 8pt; line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><span style="font-weight:bold;color:#023e8a">RFC:</span> ' . $empresa->rfc . '</p>
<p style="margin: 0cm 0cm 8pt; line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><span style="font-weight:bold;color:#023e8a">Nombre:</span> ' . $empresa->razon . '</p>
<p style="margin: 0cm 0cm 8pt; line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><span style="font-weight:bold;color:#023e8a">Dirección:</span> ' . $empresa->calle . '</p>
<p style="margin: 0cm 0cm 8pt; line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><span style="font-weight:bold;color:#023e8a">Estado:</span> ' . $empresa->estado . '</p>
<p style="margin: 0cm 0cm 8pt; line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><span style="font-weight:bold;color:#023e8a">Código Postal:</span> ' . $empresa->cp . '</p>';
if ($empresa->telefono != null) {
    $mensaje .= '<p style="margin: 0cm 0cm 8pt; line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><span style="font-weight:bold;color:#023e8a">Teléfono:</span> ' . $empresa->telefono . '</p>';
}
if ($empresa->correo != null) {
    $mensaje .= '<p style="margin: 0cm 0cm 8pt; line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><span style="font-weight:bold;color:#023e8a">Correo de contacto:</span> ' . $empresa->correo . '</p>';
}
if ($empresa->sitioWeb != null) {
    $mensaje .= '<p style="margin: 0cm 0cm 8pt; line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><span style="font-weight:bold;color:#023e8a">Sitio Web:</span> ' . $empresa->sitioWeb . '</p>';
}
// cuentas bancarias es un array
if (count($cuentas) > 0) {
    $mensaje .= '<h3 style="margin: 0cm 0cm 8pt; line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><br>Cuentas Bancarias</h3>';
    for ($i = 0; $i < count($cuentas); $i++) {
        $mensaje .= '
    <p style="margin: 0cm 0cm 8pt; line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><span style="font-weight:bold;color:#fca311;">Banco:</span> ' . $cuentas[$i]->banco . '</p>
    <p style="margin: 0cm 0cm 8pt; line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><span style="font-weight:bold;color:#fca311;">Número de Cuenta:</span> ' . $cuentas[$i]->cuenta . '</p>
    ';
    }
}
$mensaje .= '
<p style="margin: 0cm 0cm 8pt; line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><br></p>
<p style="margin: 0cm 0cm 8pt; line-height: 16.05px; font-size: 15px; font-family: Calibri, &quot;sans-serif&quot;;"><br></p>
<ul>
</ul>
<div style="text-align: center;">
';

if ($empresa->comprobanteDom != null && $empresa->comprobanteDom != '' && $empresa->comprobanteDom != 'null' && $empresa->comprobanteDom != 'NULL' && $empresa->comprobanteDom != 'Null' && $empresa->comprobanteDom != '-'  && isset($_POST['siDom'])) {
    $mensaje .= '
    <a href="https://10h.mx/' . $empresa->comprobanteDom . '"  style=" background-color: blue; 
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    float: center;
    font-size: 16px;">Comprobante de domicilio </a>
    ';
}

if ($empresa->constanciaSf != null && $empresa->constanciaSf != '' && $empresa->constanciaSf != 'null' && $empresa->constanciaSf != 'NULL' && $empresa->constanciaSf != 'Null' && $empresa->constanciaSf != '-' && isset($_POST['siCSF'])) {

    $mensaje .= '
    <a href="https://10h.mx/' . $empresa->constanciaSf . '"  style=" background-color: orange; 
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    float: center;
    font-size: 16px;">Constancia SF</a>
    ';
}

if ($empresa->logo != null && $empresa->logo != '' && $empresa->logo != 'null' && $empresa->logo != 'NULL' && $empresa->logo != 'Null' && $empresa->logo != '-'  && isset($_POST['siLogo'])) {
    $mensaje .= '
    <a href="https://10h.mx/' . $empresa->logo . '" style=" background-color: red; 
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    float: center;
    font-size: 16px;">Logo</a>
    ';
}

if ($empresa->pdf != null && $empresa->pdf != '' && $empresa->pdf != 'null' && $empresa->pdf != 'NULL' && $empresa->pdf != 'Null' && $empresa->pdf != '-'  && isset($_POST['siDoc'])) {
    $mensaje .= '
    <a href="https://10h.mx/' . $empresa->pdf . '"  style=" background-color: green; 
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    float: center;
    font-size: 16px;">32D</a>
    ';
}



$mensaje .= '


</div>

';

$asunto = 'Solicitud de informacion de empresa';

// mailer($email, $email, $asunto, $mensaje, '', '', "");

if (mailer($email, $email, $asunto, $mensaje, '', '', "")['error'] == false) {
    echo json_encode(array('error' => false, 'message' => 'Se ha enviado la información de la empresa a su correo'));
} else {
    echo json_encode(array('error' => true, 'message' => 'No se pudo enviar la información de la empresa a su correo'));
}
