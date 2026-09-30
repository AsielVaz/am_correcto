<?php
include_once('../config/conectorBD.php');


class Contacto
{
    public $id;
    public $nombre;
    public $email;
    public $mensaje;

    public function __construct($id, $nombre, $email, $mensaje)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->mensaje = $mensaje;
    }
}


class AdministradorContactos extends conector
{
    public function agregarContacto($nombre, $email, $mensaje)
    {
        $query = "INSERT INTO `contactos` (`nombre`, `email`, `mensaje`) VALUES ( '$nombre', '$email', '$mensaje')";
        $result = $this->ejecutar($query);
        return $result;
    }


    public function eliminarContacto($id)
    {
        $query = "DELETE FROM `contactos` WHERE `id` = '$id'";
        $result = $this->ejecutar($query);
        return $result;
    }

    public function dameContacto($id)
    {
        $query = "SELECT * FROM `contactos` WHERE `id` = '$id'";
        $result = $this->ejecutar($query);
        $row = mysqli_fetch_assoc($result);
        $contacto = new Contacto($row['id'], $row['nombre'], $row['email'], $row['mensaje']);
        return $contacto;
    }

    public function dameContactos()
    {
        $query = "SELECT * FROM `contactos`";
        $result = $this->ejecutar($query);
        $contactos = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $contacto = new Contacto($row['id'], $row['nombre'], $row['email'], $row['mensaje']);
            array_push($contactos, $contacto);
        }
        return $contactos;
    }
}
