<?php

include_once('../config/conectorBD.php');

class EstatusPeriodos
{
    public $id;
    public $idEmpresa;
    public $estatusComprobante;
    public $estatusConstancia;
    public $estatus32d;
    public $estatusFinDominio;
    public $estatusTelefono;
    public $estatusCorreo;
    public $estatusSitioWeb;
    public $estatusCveSAT;
    public $estatusCveIoFact;
    public $estatusCveBancos;
    public $estatusSellosSAT;


    public function __construct()
    {
        $this->id = 0;
        $this->idEmpresa = 0;
        $this->estatusComprobante = "";
        $this->estatusConstancia = "";
        $this->estatus32d = "";
        $this->estatusFinDominio = "";
        $this->estatusTelefono = "";
        $this->estatusCorreo = "";
        $this->estatusSitioWeb = "";
        $this->estatusCveSAT = "";
        $this->estatusCveIoFact = "";
        $this->estatusCveBancos = "";
        $this->estatusSellosSAT = "";
    }
}



class AdministradorEstatusPeriodos extends conector
{

    // #region GET

    public function obtenerEstatusPeriodos()
    {
        $sql = "SELECT * FROM estatus_periodos";
        $resultado = $this->ejecutar($sql);
        $estatusPeriodos = array();
        while ($fila = $resultado->fetch_array()) {
            $estatusPeriodo = new EstatusPeriodos();
            $estatusPeriodo->id = $fila['id'];
            $estatusPeriodo->idEmpresa = $fila['id_empresa'];
            $estatusPeriodo->estatusComprobante = $fila['estatus_comprobante'];
            $estatusPeriodo->estatusConstancia = $fila['estatus_constancia'];
            $estatusPeriodo->estatus32d = $fila['estatus_32d'];
            $estatusPeriodo->estatusFinDominio = $fila['estatus_findominio'];
            $estatusPeriodo->estatusTelefono = $fila['estatus_telefono'];
            $estatusPeriodo->estatusCorreo = $fila['estatus_correo'];
            $estatusPeriodo->estatusSitioWeb = $fila['estatus_sitioweb'];
            $estatusPeriodo->estatusCveSAT = $fila['estatus_cve_sat'];
            $estatusPeriodo->estatusCveIoFact = $fila['estatus_cve_iofact'];
            $estatusPeriodo->estatusCveBancos = $fila['estatus_cve_bancos'];
            $estatusPeriodo->estatusSellosSAT = $fila['estatus_sellos_sat'];
            array_push($estatusPeriodos, $estatusPeriodo);
        }
        return $estatusPeriodos;
    }

    public function obtenerEstatusPeriodosPorId($id)
    {
        $sql = "SELECT * FROM estatus_periodos WHERE id = '$id'";
        $resultado = $this->ejecutar($sql);
        $estatusPeriodo = new EstatusPeriodos();
        while ($fila = $resultado->fetch_array()) {
            $estatusPeriodo->id = $fila['id'];
            $estatusPeriodo->idEmpresa = $fila['id_empresa'];
            $estatusPeriodo->estatusComprobante = $fila['estatus_comprobante'];
            $estatusPeriodo->estatusConstancia = $fila['estatus_constancia'];
            $estatusPeriodo->estatus32d = $fila['estatus_32d'];
            $estatusPeriodo->estatusFinDominio = $fila['estatus_findominio'];
            $estatusPeriodo->estatusTelefono = $fila['estatus_telefono'];
            $estatusPeriodo->estatusCorreo = $fila['estatus_correo'];
            $estatusPeriodo->estatusSitioWeb = $fila['estatus_sitioweb'];
        }
        return $estatusPeriodo;
    }

    public function obtenerEstatusPeriodosPorIdEmpresa($idEmpresa)
    {
        $sql = "SELECT * FROM estatus_periodos WHERE id_empresa = '$idEmpresa'";
        $resultado = $this->ejecutar($sql);
        $estatusPeriodo = new EstatusPeriodos();
        while ($fila = $resultado->fetch_array()) {
            $estatusPeriodo->id = $fila['id'];
            $estatusPeriodo->idEmpresa = $fila['id_empresa'];
            $estatusPeriodo->estatusComprobante = $fila['estatus_comprobante'];
            $estatusPeriodo->estatusConstancia = $fila['estatus_constancia'];
            $estatusPeriodo->estatus32d = $fila['estatus_32d'];
            $estatusPeriodo->estatusFinDominio = $fila['estatus_findominio'];
            $estatusPeriodo->estatusTelefono = $fila['estatus_telefono'];
            $estatusPeriodo->estatusCorreo = $fila['estatus_correo'];
            $estatusPeriodo->estatusSitioWeb = $fila['estatus_sitioweb'];
        }
        return $estatusPeriodo;
    }

    public function obtenerEstatusPeriodoComprobante($id)
    {
        $sql = "SELECT estatus_comprobante FROM estatus_periodos WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        $estatus = "";
        while ($fila = $resultado->fetch_array()) {
            $estatus = $fila['estatus_comprobante'];
        }
        return $estatus;
    }

    public function obtenerEstatusPeriodoConstancia($id)
    {
        $sql = "SELECT estatus_constancia FROM estatus_periodos WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        $estatus = "";
        while ($fila = $resultado->fetch_array()) {
            $estatus = $fila['estatus_constancia'];
        }
        return $estatus;
    }

    public function obtenerEstatusPeriodo32d($id)
    {
        $sql = "SELECT estatus_32d FROM estatus_periodos WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        $estatus = "";
        while ($fila = $resultado->fetch_array()) {
            $estatus = $fila['estatus_32d'];
        }
        return $estatus;
    }

    public function obtenerEstatusPeriodoCveSat($id)
    {
        $sql = "SELECT estatus_32d FROM estatus_periodos WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        $estatus = "";
        while ($fila = $resultado->fetch_array()) {
            $estatus = $fila['estatus_cve_sat'];
        }
        return $estatus;
    }

    public function obtenerEstatusFechaCveSat($id)
    {
        $sql = "SELECT sat FROM estatus_claves WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);

        $estatus = "";
        while ($fila = $resultado->fetch_array()) {
            $estatus = $fila['sat'];
        }
        if ($estatus == "" and $estatus <> "0000-00-00 00:00:00") {
            $sql = "insert into estatus_claves (id_empresa) values ('$id')";
            $sql_e = $this->ejecutar($sql);
            $estatus = "0000-00-00 00:00:00";
        }
        return $estatus;
    }

    public function obtenerEstatusPeriodoCveIOFact($id)
    {
        $sql = "SELECT estatus_32d FROM estatus_periodos WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        $estatus = "";
        while ($fila = $resultado->fetch_array()) {
            $estatus = $fila['estatus_cve_iofact'];
        }
        return $estatus;
    }
    public function obtenerEstatusFechaCveIOFact($id)
    {
        $sql = "SELECT estatus_32d FROM estatus_periodos WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        $estatus = "";
        while ($fila = $resultado->fetch_array()) {
            $estatus = $fila['estatus_cve_iofact'];
        }
        return $estatus;
    }

    public function obtenerEstatusPeriodoCveBancos($id)
    {
        $sql = "SELECT estatus_32d FROM estatus_periodos WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        $estatus = "";
        while ($fila = $resultado->fetch_array()) {
            $estatus = $fila['estatus_cve_bancos'];
        }
        return $estatus;
    }

    public function obtenerEstatusPeriodoSellosSAT($id)
    {
        $sql = "SELECT estatus_32d FROM estatus_periodos WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        $estatus = "";
        while ($fila = $resultado->fetch_array()) {
            $estatus = $fila['estatus_sellos_sat'];
        }
        return $estatus;
    }

    public function obtenerEstatusPeriodoFinDominio($id)
    {
        $sql = "SELECT estatus_findominio FROM estatus_periodos WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        $estatus = "";
        while ($fila = $resultado->fetch_array()) {
            $estatus = $fila['estatus_findominio'];
        }
        return $estatus;
    }

    public function obtenerEstatusPeriodoTelefono($id)
    {
        $sql = "SELECT estatus_telefono FROM estatus_periodos WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        $estatus = "";
        while ($fila = $resultado->fetch_array()) {
            $estatus = $fila['estatus_telefono'];
        }
        return $estatus;
    }

    public function obtenerEstatusPeriodoCorreo($id)
    {
        $sql = "SELECT estatus_correo FROM estatus_periodos WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        $estatus = "";
        while ($fila = $resultado->fetch_array()) {
            $estatus = $fila['estatus_correo'];
        }
        return $estatus;
    }

    public function obtenerEstatusPeriodoSitioWeb($id)
    {
        $sql = "SELECT estatus_sitioweb FROM estatus_periodos WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        $estatus = "";
        while ($fila = $resultado->fetch_array()) {
            $estatus = $fila['estatus_sitioweb'];
        }
        return $estatus;
    }


    // PUT


    public function actualizarEstatusPeriodo($id, $estado, $estatus)
    {
        $sql = "UPDATE estatus_periodos SET $estatus = '$estado' WHERE id_empresa = '$id'";
        return $this->ejecutar($sql);
    }

    public function actualizarEstatusPeriodoComprobante($id, $estatus)
    {
        $sql = "UPDATE estatus_periodos SET estatus_comprobante = '$estatus' WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        return $resultado;
    }

    public function actualizarEstatusPeriodoConstancia($id, $estatus)
    {
        $sql = "UPDATE estatus_periodos SET estatus_constancia = '$estatus' WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        return $resultado;
    }

    public function actualizarEstatusPeriodo32d($id, $estatus)
    {
        $sql = "UPDATE estatus_periodos SET estatus_32d = '$estatus' WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        return $resultado;
    }

    public function actualizarEstatusPeriodoFinDominio($id, $estatus)
    {
        $sql = "UPDATE estatus_periodos SET estatus_findominio = '$estatus' WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        return $resultado;
    }

    public function actualizarEstatusPeriodoTelefono($id, $estatus)
    {
        $sql = "UPDATE estatus_periodos SET estatus_telefono = '$estatus' WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        return $resultado;
    }

    public function actualizarEstatusPeriodoCorreo($id, $estatus)
    {
        $sql = "UPDATE estatus_periodos SET estatus_correo = '$estatus' WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        return $resultado;
    }

    public function actualizarEstatusPeriodoSitioWeb($id, $estatus)
    {
        $sql = "UPDATE estatus_periodos SET estatus_sitioweb = '$estatus' WHERE id_empresa = '$id'";
        $resultado = $this->ejecutar($sql);
        return $resultado;
    }
}
