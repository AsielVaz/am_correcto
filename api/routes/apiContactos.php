
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

include_once('../controllers/adminContactos.php');
include_once('../utils/correo.php');


$accion = $_POST['accion'];
$casoAgregar = 'agregar';
$casoEliminar = 'eliminar';


function agregarContacto()
{
    include('../../config.php');
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $mensaje = $_POST['mensaje'];
    $adminContactos = new AdministradorContactos();
    $adminContactos->agregarContacto($nombre, $email, $mensaje);
    $correo = new Correo($host, $secure, $port, $emailServer, $password, $username);
    if ($nombre == '') {
        $nombre = 'Usuario';
    }
    $mensajeHtml = '
    <html>
    <head>
    <title>Nuevo contacto</title>
    </head>
    <body>
    <h1>Nuevo contacto</h1>
    <p>Nombre: ' . $nombre . '</p>
    <p>Email: ' . $email . '</p>
    <p>Mensaje: ' . $mensaje . '</p>
    <p>Hemos recibido un nuevo contacto, nos comunicaremos con usted a la mayor brevedad posible.</p>
    <p>Muchas gracias por contactarnos.</p>
    </body>
    </html>
    ';
    $correo->mailer($email, $email, 'Hemos recibido tu mensaje', $mensajeHtml, '', '', '');
    echo '1';
}


function eliminarContacto()
{
    $id = $_POST['id'];
    $adminContactos = new AdministradorContactos();
    $adminContactos->eliminarContacto($id);
}


switch ($accion) {
    case $casoAgregar:
        agregarContacto();
        break;
    case $casoEliminar:
        eliminarContacto();
        break;
}
