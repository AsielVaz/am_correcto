<?php

// session_start();
// $_SESSION['sesionUsuario']['temporal'] = $_SESSION['sesionUsuario']['id'];
// $_SESSION['sesionUsuario']['id'] = intval($_POST['id']);
// $_SESSION['sesionUsuario']['permisos'] = intval($_POST['permiso']);

// utilizando sesiones con php version 7.4.3

session_start();
// $_SESSION['sesionUsuario']['temporal'] = $_SESSION['sesionUsuario']['id'];
$id = $_POST['id'];
$permiso = $_POST['permiso'];
$accesos = $_POST['accesos'];

if (isset($id) && isset($permiso)) {
    $_SESSION['sesionUsuario']['id'] = intval($id);
    $_SESSION['sesionUsuario']['permisos'] = intval($permiso);
    $_SESSION['sesionUsuario']['accesos'] = $accesos;
    echo json_encode(array(
        "status" => "success",
        "id" => $_SESSION['sesionUsuario']['id'],
        "permisos" => $_SESSION['sesionUsuario']['permisos'],
        "accesos" => $_SESSION['sesionUsuario']['accesos']

    ));
} else {
    echo json_encode(array(
        "status" => "error",
        "id" => $_SESSION['sesionUsuario']['id'],
        "permisos" => $_SESSION['sesionUsuario']['permisos']
    ));
}
?>
