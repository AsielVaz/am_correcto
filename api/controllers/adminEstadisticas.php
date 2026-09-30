<?php
include_once('../config/conectorBD.php');

class Visita
{
    public $id;
    public $fecha;
    public $proyecto;
    public $identificador;

    public function __construct($id, $fecha, $proyecto, $identificador)
    {
        $this->id = $id;
        $this->fecha = $fecha;
        $this->proyecto = $proyecto;
        $this->identificador = $identificador;
    }
}

class AdministradorEstadisticas extends conector
{
    public function agregarVisita($fecha, $proyecto, $identificador)
    {
        $query = "INSERT INTO `estadisticas` (`fecha`, `id_proyecto`, `identificador`) VALUES ('$fecha', '$proyecto', '$identificador')";
        $result = $this->ejecutar($query);
        return $result;
    }

    public function eliminarVisita($id)
    {
        $query = "DELETE FROM `estadisticas` WHERE `id` = '$id'";
        $result = $this->ejecutar($query);
        return $result;
    }

    public function dameVisita($id)
    {
        $query = "SELECT * FROM `estadisticas` WHERE `id` = '$id'";
        $result = $this->ejecutar($query);
        $row = mysqli_fetch_assoc($result);
        $visita = new Visita($row['id'], $row['fecha'], $row['id_proyecto'], $row['identificador']);
        return $visita;
    }

    public function dameVisitas()
    {
        $query = "SELECT * FROM `estadisticas`";
        $result = $this->ejecutar($query);
        $visitas = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $visita = new Visita($row['id'], $row['fecha'], $row['id_proyecto'], $row['identificador']);
            array_push($visitas, $visita);
        }
        return $visitas;
    }

    public function dameVisitasPorProyecto($id)
    {
        $query = "SELECT * FROM `estadisticas` WHERE `id_proyecto` = '$id'";
        $result = $this->ejecutar($query);
        $visitas = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $visita = new Visita($row['id'], $row['fecha'], $row['id_proyecto'], $row['identificador']);
            array_push($visitas, $visita);
        }
        return $visitas;
    }

    public function dameVisitasPorMes($mes, $proyecto)
    {
        $query = "SELECT * FROM `estadisticas` WHERE MONTH(fecha) = '$mes' AND `id_proyecto` = '$proyecto'";
        $result = $this->ejecutar($query);
        $visitas = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $visita = new Visita($row['id'], $row['fecha'], $row['id_proyecto'], $row['identificador']);
            array_push($visitas, $visita);
        }
        return $visitas;
    }

    public function cuentaVisitasPorMes($anio, $mes, $proyecto)
    {
        $query = "SELECT COUNT(*) FROM `estadisticas` WHERE YEAR(fecha) = '$anio' AND MONTH(fecha) = '$mes' AND `id_proyecto` = '$proyecto'";
        $result = $this->ejecutar($query);
        $row = mysqli_fetch_row($result);
        return $row[0];
    }

    public function cuentaVisitasPorProyecto($proyecto)
    {
        $query = "SELECT COUNT(*) FROM `estadisticas` WHERE `id_proyecto` = '$proyecto'";
        $result = $this->ejecutar($query);
        $row = mysqli_fetch_row($result);
        return $row[0];
    }

    public function cuentaVisitasTotalesPorMes($anio, $mes){
        $query = "SELECT COUNT(*) FROM `estadisticas` WHERE YEAR(fecha) = '$anio' AND MONTH(fecha) = '$mes'";
        $result = $this->ejecutar($query);
        $row = mysqli_fetch_row($result);
        return $row[0];
    }
}
