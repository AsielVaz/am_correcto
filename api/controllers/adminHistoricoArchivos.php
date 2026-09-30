<?php

include_once('../config/conectorBD.php');

class HistoricoArchivos
{
    public $id;
    public $nombre;
    public $fecha;
    public $idEmpresa;
    public $url;

    public function __construct()
    {
        $this->id = 0;
        $this->nombre = "";
        $this->fecha = "";
        $this->idEmpresa = 0;
        $this->url = "";
    }
}


class AdministradorHistoricoArchivos extends conector
{
    public function obtenerHistoricoArchivos()
    {
        $sql = "SELECT * FROM historico_archivos";
        $resultado = $this->ejecutar($sql);
        $historicoArchivos = array();
        while ($fila = $resultado->fetch_array()) {
            $historicoArchivo = new HistoricoArchivos();
            $historicoArchivo->id = $fila['id'];
            $historicoArchivo->nombre = $fila['nombre'];
            $historicoArchivo->fecha = $fila['fecha'];
            $historicoArchivo->idEmpresa = $fila['id_empresa'];
            $historicoArchivo->url = $fila['url'];
            array_push($historicoArchivos, $historicoArchivo);
        }
        return $historicoArchivos;
    }

    public function obtenerHistoricoArchivosPorId($id)
    {
        $sql = "SELECT * FROM historico_archivos WHERE id = '$id'";
        $resultado = $this->ejecutar($sql);
        $historicoArchivo = new HistoricoArchivos();
        while ($fila = $resultado->fetch_array()) {
            $historicoArchivo->id = $fila['id'];
            $historicoArchivo->nombre = $fila['nombre'];
            $historicoArchivo->fecha = $fila['fecha'];
            $historicoArchivo->idEmpresa = $fila['id_empresa'];
            $historicoArchivo->url = $fila['url'];
        }
        return $historicoArchivo;
    }

    public function agregarHistoricoArchivos($nombre, $idEmpresa, $url)
    {
        $sql = "INSERT INTO historico_archivos (nombre_archivo, id_empresa, url, fecha) VALUES ('$nombre', '$idEmpresa', '$url', now())";
        $resultado = $this->ejecutar($sql);
        if ($resultado) {
            return "1";
        }
    }

    public function obtenerElTercerRegistroMasAntiguo($nombre, $idEmpresa)
    {
        // ej. selecciona todo de historico_archivos y comprueba si hay mas de 3 registros, si hay mas de 3 registros, selecciona el registro mas antiguo y retornalo
        $sql = "SELECT * FROM historico_archivos WHERE nombre_archivo = '$nombre' AND id_empresa = '$idEmpresa'";
        $resultado = $this->ejecutar($sql);
        if ($resultado->num_rows == 3) {
            $sql = "SELECT * FROM historico_archivos WHERE nombre_archivo = '$nombre' AND id_empresa = '$idEmpresa' ORDER BY fecha ASC LIMIT 1";
            // solo retornar el campo url
            $resultado = $this->ejecutar($sql);
            $fila = $resultado->fetch_array();
            return $fila['url'];
        }
    }

    public function eliminarHistoricoArchivo($nombre, $idEmpresa, $url)
    {
        $sql = "DELETE FROM historico_archivos WHERE nombre_archivo = '$nombre' AND id_empresa = '$idEmpresa' AND url = '$url'";
        $resultado = $this->ejecutar($sql);
        return $resultado;
    }

    public function obtenerHistoricoArchivosPorEmpresaYNombreArchivo($idEmpresa, $nombre)
    {
        $sql = "SELECT * FROM historico_archivos WHERE id_empresa = '$idEmpresa' AND nombre_archivo = '$nombre'";
        $resultado = $this->ejecutar($sql);
        $historicoArchivos = array();
        while ($fila = $resultado->fetch_array()) {
            $historicoArchivo = new HistoricoArchivos();
            $historicoArchivo->id = $fila['id'];
            $historicoArchivo->url = $fila['url'];
            array_push($historicoArchivos, $historicoArchivo);
        }
        return $historicoArchivos;
    }
}
