<?php
include_once('../utils/jwt_helper.php');
$headers = getallheaders();
$token = null;
if (isset($headers['Authorization'])) {
    $auth = $headers['Authorization'];
    if (stripos($auth, 'Bearer ') === 0) {
        $token = trim(substr($auth, 7));
    }
} elseif (isset($_POST['token'])) {
    $token = $_POST['token'];
}
if (!$token || !JwtHelper::validar($token)) {
    http_response_code(401);
    echo json_encode(['error' => 'Token inválido o expirado']);
    exit;
}

include_once('../controllers/adminEmpleado.php');
include_once('../controllers/adminAspirantes.php');

$accion = $_POST['accion'];

$casoAgregar = "agregar";
$casoEliminar = "eliminar";
$casoModificar = "modificar";



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



function GUIDv4($trim = true)
{
    // Windows
    if (function_exists('com_create_guid') === true) {
        if ($trim === true)
            return trim(com_create_guid(), '{}');
        else
            return com_create_guid();
    }

    // OSX/Linux
    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // Fallback (PHP 4.2+)
    mt_srand((float)microtime() * 10000);
    $charid = strtolower(md5(uniqid(rand(), true)));
    $hyphen = chr(45);                  // "-"
    $lbrace = $trim ? "" : chr(123);    // "{"
    $rbrace = $trim ? "" : chr(125);    // "}"
    $guidv4 = $lbrace .
        substr($charid,  0,  8) . $hyphen .
        substr($charid,  8,  4) . $hyphen .
        substr($charid, 12,  4) . $hyphen .
        substr($charid, 16,  4) . $hyphen .
        substr($charid, 20, 12) .
        $rbrace;
    return $guidv4;
}


function procesarImagen()
{

    $carpetaDestino = '/Imagenes/Empleados/';
    $pesoMaxicoImagen = 2000000;
    $nombreCompuestoImagen = "default.txt";
    $nombre_imagen = basename($_FILES['archivo']['name']);
    $tipo_Imgaen = $_FILES['archivo']['type'];
    $tamanio_imagen = $_FILES['archivo']['size'];

    $casoPng = ".png";
    $casoJpeg = ".jpg";
    //comprovadores de peso y tipo de imagen

    if ($tamanio_imagen < $pesoMaxicoImagen) {
        if ($tipo_Imgaen == "image/jpeg" || $tipo_Imgaen == "image/png") {
            //mueve la imagen a la carpeta seleccionada 
            move_uploaded_file($_FILES['archivo']['tmp_name'], APP_ROOT . $carpetaDestino . $nombre_imagen);
            chmod(appFilesystemPath($carpetaDestino . $nombre_imagen), 0640);
            switch ($tipo_Imgaen) {
                case "image/jpeg":
                    $nombreCompuestoImagen = $carpetaDestino . "Imagen" . rand(0, 40000) . $casoJpeg;
                    break;
                case "image/png":
                    $nombreCompuestoImagen = $carpetaDestino . "Imagen" . rand(0, 40000) .  $casoPng;
                    break;
            }
            rename(APP_ROOT . $carpetaDestino . $nombre_imagen, APP_ROOT . $nombreCompuestoImagen);
            chmod(APP_ROOT . $nombreCompuestoImagen, 0640);
      
            return $nombreCompuestoImagen;
        } else {
            echo json_encode("El formato de la imagen no esta permitido");
        }
    } else {
        echo json_encode("La imagen supera el tamaño establecido");
    }
}

function agregarEmpleado()
{
    $adminEmpleados = new AdministradorEmpleado();
    $adminAspirantes = new AdministradorAspirantes();
    $nombre = $_POST['nombre'];
    $puesto = $_POST['puesto'];
    $email = $_POST['email'];
    $imagen = procesarImagen();
    $token = GUIDv4();
    $adminEmpleados->agregarEmpleado($nombre, $puesto, $email, $imagen);
    $adminAspirantes->agregarAspirante($adminEmpleados->dameElUltimoId(), $token, $email);
    $mensaje = '<html><body>';
    $mensaje .= '<div style="text-align: center;"><span style="background-color: rgba(255,255,255,var(--mdb-bg-opacity)); font-family: var(--mdb-font-roboto); font-size: var(--mdb-body-font-size); font-weight: var(--mdb-body-font-weight); text-align: var(--mdb-body-text-align);">&nbsp; &nbsp; &nbsp; &nbsp;Has si do dado de alta por el servicio de empleados de 10H</span></div><div style="text-align: center;"><span style="background-color: rgba(255,255,255,var(--mdb-bg-opacity)); font-family: var(--mdb-font-roboto); font-size: var(--mdb-body-font-size); font-weight: var(--mdb-body-font-weight); text-align: var(--mdb-body-text-align);">Para concluir tu registro entra en el siguiente enlace.&nbsp;</span></div><div style="text-align: center;"><span style="background-color: rgba(255,255,255,var(--mdb-bg-opacity)); text-align: var(--mdb-body-text-align);">https://10h.mx/admin/registro.php?aspirante=' . $token . '<br></span></div><div style="text-align: center;"><span style="background-color: rgba(255,255,255,var(--mdb-bg-opacity)); font-family: var(--mdb-font-roboto); font-size: var(--mdb-body-font-size); font-weight: var(--mdb-body-font-weight); text-align: var(--mdb-body-text-align);">Completa tu registro con una contraseña para que puedas comenzar con tus labores administrativas.</span></div><div style="text-align: center;"><span style="background-color: rgba(255,255,255,var(--mdb-bg-opacity)); text-align: var(--mdb-body-text-align);"><font face="var(--mdb-font-roboto)"><span style="font-weight: var(--mdb-body-font-weight);">Buen </span></font>día<font face="var(--mdb-font-roboto)"><span style="font-weight: var(--mdb-body-font-weight);">.</span></font></span></div>';
    $mensaje .= '</body></html>';
    mailer($email, $email, "Alta de nuevo personal", $mensaje, "", "", "");
    echo "1";
}

function eliminarEmpleado()
{
    $adminEmpleados = new AdministradorEmpleado();
    $id = $_POST['id'];
    $adminEmpleados->eliminarEmpleado($id);
    echo "1";
}

function modificarEmpleado()
{
    $adminEmpleados = new AdministradorEmpleado();
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $puesto = $_POST['puesto'];
    $email = $_POST['email'];
    $imagen = procesarImagen();
    $adminEmpleados->modificarEmpleado($id, $nombre, $puesto, $email, $imagen);
    echo "1";
}


switch ($accion) {
    case $casoAgregar:
        agregarEmpleado();
        break;
    case $casoEliminar:
        eliminarEmpleado();
        break;
    case $casoModificar:
        modificarEmpleado();
        break;
}
