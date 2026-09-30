<?php
include_once('../config/conectorBD.php');

class Empleado
{
    public $id;
    public $nombre;
    public $puesto;
    public $email;
    public $imagen;

    public function __construct($id, $nombre, $puesto, $email, $imagen)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->puesto = $puesto;
        $this->email = $email;
        $this->imagen = $imagen;
    }
}


class AdministradorEmpleado extends conector
{
    public function agregarEmpleado($nombre, $puesto, $email, $imagen)
    {
        $query = "INSERT INTO `empleado` (`nombre`, `puesto`, `email`, `imagen`) VALUES ('$nombre', '$puesto', '$email', '$imagen')";
        $result = $this->ejecutar($query);
        return $result;
    }

    public function eliminarEmpleado($id)
    {
        $query = "DELETE FROM `empleado` WHERE `id` = '$id'";
        $result = $this->ejecutar($query);
        return $result;
    }

    public function modificarEmpleado($id, $nombre, $puesto, $email, $imagen)
    {
        $query = "UPDATE `empleado` SET `nombre` = '$nombre', `puesto` = '$puesto', `email` = '$email', `imagen` = '$imagen' WHERE `id` = '$id'";
        $result = $this->ejecutar($query);
        return $result;
    }

    public function dameElUltimoId()
    {
        $query = "SELECT MAX(id) FROM `empleado`";
        $result = $this->ejecutar($query);
        $row = $result->fetch_assoc();
        return $row['MAX(id)'];
    }

    public function obtenerEmpleado($id)
    {
        $query = "SELECT * FROM `empleado` WHERE `id` = '$id'";
        $result = $this->ejecutar($query);
        $row = mysqli_fetch_assoc($result);
        $empleado = new Empleado($row['id'], $row['nombre'], $row['puesto'], $row['email'], $row['imagen']);
        return $empleado;
    }

    public function obtenerEmpleados()
    {
        $query = "SELECT * FROM `empleado`";
        $result = $this->ejecutar($query);
        $empleados = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $empleados[] = new Empleado($row['id'], $row['nombre'], $row['puesto'], $row['email'], $row['imagen']);
        }
        return $empleados;
    }
}
