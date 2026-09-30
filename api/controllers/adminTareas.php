<?php

include_once('../config/conectorBD.php');

class Tarea
{

    public $id;
    public $idEmpresa;
    public $accion;
    public $campo;
    public $fechaHora;
    public $empresa;

    public function __construct()
    {
        $this->id = 0;
        $this->idEmpresa = 0;
        $this->accion = '';
        $this->campo = '';
        $this->fechaHora = '';
        $this->empresa = null;
    }
}

class Empresa
{
    public $id;
    public $razon;
    public $logo;


    public function __construct()
    {
        $this->id = 0;
        $this->razon = '';
        $this->logo = '';
    }
}

class AdministradorTareas extends conector
{

    public function agregarTarea($idEmpresa, $accionTarea, $campo)
    {
        $query = "INSERT INTO tareas_realizadas (empresa_id, accion, campo, fecha_hora) VALUES ('" . $idEmpresa . "', '" . $accionTarea . "', '" . $campo . "', now())";
        $result = $this->ejecutar($query);
        return $result;
    }

    public function obtenerTareas()
    {
        // inner join para traer los datos de la empresa ordenados por fecha mas reciente
        $query = "SELECT t.*, e.razon, e.logo FROM tareas_realizadas t INNER JOIN empresas e ON t.empresa_id = e.id ORDER BY t.fecha_hora DESC";
        $result = $this->ejecutar($query);
        $tareas = array();
        while ($row = mysqli_fetch_array($result)) {
            $tarea = new Tarea();
            $tarea->id = $row['id'];
            $tarea->idEmpresa = $row['empresa_id'];
            $tarea->accion = $row['accion'];
            $tarea->campo = $row['campo'];
            $tarea->fechaHora = $row['fecha_hora'];
            $tarea->empresa = new Empresa();
            $tarea->empresa->id = $row['empresa_id'];
            $tarea->empresa->razon = $row['razon'];
            $tarea->empresa->logo = $row['logo'];
            array_push($tareas, $tarea);
        }
        return $tareas;
    }

    public function obtenerTareasPorEmpresa($idEmpresa)
    {
        $query = "SELECT * FROM tareas_realizadas WHERE empresa_id = '" . $idEmpresa . "'";
        $result = $this->ejecutar($query);
        $tareas = array();
        while ($row = mysqli_fetch_array($result)) {
            $tarea = new Tarea();
            $tarea->id = $row['id'];
            $tarea->idEmpresa = $row['empresa_id'];
            $tarea->accion = $row['accion'];
            $tarea->campo = $row['campo'];
            $tarea->fechaHora = $row['fecha_hora'];
            array_push($tareas, $tarea);
        }
        return $tareas;
    }

    public function eliminarTarea($idTarea)
    {
        $query = "DELETE FROM tareas_realizadas WHERE id = '" . $idTarea . "'";
        $result = $this->ejecutar($query);
        return $result;
    }

    public function eliminarTareas()
    {
        $query = "DELETE FROM tareas_realizadas";
        $result = $this->ejecutar($query);
        return $result;
    }
}