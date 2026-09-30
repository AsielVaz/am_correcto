<?php
include_once('../config/conectorBD.php');

class Notificacion
{
    public $id;
    public $prioridad;
    public $empresa;
    public $mensaje;
    public $fechaAsignacion;
    public $fechaFinalizado;
    public $logoEmpresa;
    public $idEmpresa;

    public function __construct($id, $prioridad, $empresa, $mensaje)
    {
        $this->id = $id;
        $this->prioridad = $prioridad;
        $this->empresa = $empresa;
        $this->mensaje = $mensaje;
    }
}

class AdminNotificaciones extends conector
{
    public function nuevaNotificacion($prioridad, $empresa, $mensaje)
    {
        $sql = "INSERT INTO notificacion (prioridad, empresa, mensaje) VALUES ('$prioridad', '$empresa', '$mensaje')";
        $this->ejecutar($sql);
    }

    public function posponerNotificacion($id)
    {
        $hoy = date("Y-m-d");
        $sql = "UPDATE notificacion SET prioridad = 4, fecha_asignacion = '$hoy' WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function recuperarNotificacion($id)
    {
        $hoy = date("Y-m-d");
        $sql = "UPDATE notificacion SET prioridad = 1, fecha_asignacion = '$hoy' WHERE id = '$id'";
        $resultado = $this->ejecutar($sql);
    }

    public function eliminarNotificacionPorCompletada($empresa, $mensaje)
    {
        $sql = "DELETE FROM notificacion WHERE empresa = '$empresa' AND mensaje = '$mensaje'";
        $this->ejecutar($sql);
    }

    public function eliminarNotificacion($id)
    {
        $sql = "DELETE FROM notificacion WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function dameNotificaciones()
    {
        $sql = "SELECT notificacion.id, notificacion.empresa as id_empresa ,mensaje, logo, razon as empresa, notificacion.prioridad, fecha_asignacion, fecha_completado FROM `notificacion`
        INNER JOIN empresas
        ON notificacion.empresa = empresas.id 
        WHERE notificacion.prioridad = 1 OR notificacion.prioridad = 4
        ORDER BY prioridad ASC";
        $result = $this->ejecutar($sql);
        $notificaciones = array();
        while ($row = $result->fetch_assoc()) {
            $notificacion = new Notificacion($row['id'], $row['prioridad'], $row['empresa'], $row['mensaje']);
            $notificacion->fechaAsignacion = $row['fecha_asignacion'];
            $notificacion->fechaFinalizado = $row['fecha_completado'];
            $notificacion->logoEmpresa = $row['logo'];
            $notificacion->idEmpresa = $row['id_empresa'];

            array_push($notificaciones, $notificacion);
        }
        return $notificaciones;
    }

    public function existeNotificacion($empresa, $mensaje)
    {
        $sql = "SELECT * FROM notificacion WHERE empresa = '$empresa' AND mensaje = '$mensaje'";
        $result = $this->ejecutar($sql);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function dameNotificacionesPorEmpresa($empresa)
    {
        $sql = "SELECT notificacion.id, mensaje, logo, razon as empresa, notificacion.prioridad, fecha_asignacion, fecha_completado FROM `notificacion`
        INNER JOIN empresas 
        ON notificacion.empresa = empresas.id
        WHERE notificacion.empresa = '$empresa' ORDER BY prioridad ASC";
        $result = $this->ejecutar($sql);
        $notificaciones = array();
        while ($row = $result->fetch_assoc()) {
            $notificacion = new Notificacion($row['id'], $row['prioridad'], $row['empresa'], $row['mensaje']);
            $notificacion->fechaAsignacion = $row['fecha_asignacion'];
            $notificacion->fechaFinalizado = $row['fecha_completado'];
            $notificacion->logoEmpresa = $row['logo'];

            array_push($notificaciones, $notificacion);
        }
        return $notificaciones;
    }

    public function dameNotificacionesPostergadas()
    {
        $sql = "SELECT notificacion.id, mensaje, logo, razon as empresa, notificacion.prioridad, fecha_asignacion, fecha_completado FROM `notificacion`
        INNER JOIN empresas 
        ON notificacion.empresa = empresas.id
        WHERE notificacion.prioridad = 4 ORDER BY prioridad ASC";
        $result = $this->ejecutar($sql);
        $notificaciones = array();
        while ($row = $result->fetch_assoc()) {
            $notificacion = new Notificacion($row['id'], $row['prioridad'], $row['empresa'], $row['mensaje']);
            $notificacion->fechaAsignacion = $row['fecha_asignacion'];
            $notificacion->fechaFinalizado = $row['fecha_completado'];
            $notificacion->logoEmpresa = $row['logo'];

            array_push($notificaciones, $notificacion);
        }
        return $notificaciones;
    }

    public function marcarComoCompletada($id)
    {
        $hoy = date("Y-m-d");
        $sql = "UPDATE notificacion SET prioridad = 5, fecha_completado = '$hoy' WHERE id = '$id'";
        $this->ejecutar($sql);
    }
}
