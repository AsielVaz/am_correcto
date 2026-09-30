<?php
include_once('../config/conectorBD.php');
class Aviso
{
    public $id;
    public $notificado;
    public $empresa;
    public $tipo;

    public function __construct($id, $notificado, $empresa, $tipo)
    {
        $this->id = $id;
        $this->notificado = $notificado;
        $this->empresa = $empresa;
        $this->tipo = $tipo;
    }
}

class AdminAviso extends conector
{


    public function agregarAviso($empresa, $tipo)
    {
        $sql = "INSERT INTO aviso_notificacion (id_empresa, tipo) VALUES ('$empresa', '$tipo')";
        $this->ejecutar($sql);
    }

    public function eliminarAviso($id)
    {
        $sql = "DELETE FROM aviso_notificacion WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function dameAvisosPorEmpresa($empresa)
    {
        $sql = "SELECT * FROM aviso_notificacion WHERE id_empresa = '$empresa'";
        $result = $this->ejecutar($sql);
        $avisos = array();
        while ($row = $result->fetch_assoc()) {
            $aviso = new Aviso($row['id'], $row['notificado'], $row['id_empresa'], $row['tipo']);
            array_push($avisos, $aviso);
        }
        return $avisos;
    }

    public function dameUltimoAviso($empresa, $tipo)
    {
        $sql = "SELECT * FROM aviso_notificacion WHERE id_empresa = '$empresa' AND tipo = '$tipo' ORDER BY notificado DESC";
        $result = $this->ejecutar($sql);
        $aviso = new Aviso(0, "0000-00-00", 0, 0);
        while ($row = $result->fetch_assoc()) {
            $aviso = new Aviso($row['id'], $row['notificado'], $row['id_empresa'], $row['tipo']);
        }
        return $aviso;
    }
}
