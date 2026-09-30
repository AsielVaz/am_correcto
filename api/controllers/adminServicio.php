<?php
include_once('../config/conectorBD.php');


class Servicio
{
    public $id;
    public $nombre;
    public $descripcion;
    public $imagen;

    public function __construct($id, $nombre, $descripcion, $imagen)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->imagen = $imagen;
    }
}


class AdministradorServicios extends conector
{
    public function agregarServicio($nombre, $descripcion, $imagen)
    {
        $sql = "INSERT INTO servicios (nombre, descripcion, imagen) VALUES ('$nombre', '$descripcion', '$imagen')";
        $this->ejecutar($sql);
    }

    public function eliminarServicio($id)
    {
        $sql = "DELETE FROM servicios WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function modificarServicio($id, $nombre, $descripcion, $imagen)
    {
        $sql = "UPDATE servicios SET nombre = '$nombre', descripcion = '$descripcion', imagen = '$imagen' WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function obtenerServicio($id)
    {
        $sql = "SELECT * FROM servicios WHERE id = '$id'";
        $resultado = $this->ejecutar($sql);
        $fila = $resultado->fetch_assoc();
        $servicio = new Servicio($fila['id'], $fila['nombre'], $fila['descripcion'], $fila['imagen']);
        return $servicio;
    }

    public function obtenerServicios()
    {
        $sql = "SELECT * FROM servicios";
        $resultado = $this->ejecutar($sql);
        $servicios = array();
        while ($fila = $resultado->fetch_assoc()) {
            $servicio = new Servicio($fila['id'], $fila['nombre'], $fila['descripcion'], $fila['imagen']);
            array_push($servicios, $servicio);
        }
        return $servicios;
    }
}
