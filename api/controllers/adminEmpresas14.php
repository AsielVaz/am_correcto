<?php
include_once('../config/conectorBDS14.php');

class Cuenta14
{
    public $id;
    public $empresa;
    public $banco;
    public $numero_cuenta;
    public $clave_interbancaria;
    public $nombre_corto;
    public $moneda;

    function __construct($id, $empresa, $banco, $numero_cuenta, $clave_interbancaria, $nombre_corto, $moneda)
    {
        $this->id = $id;
        $this->empresa = $empresa;
        $this->banco = $banco;
        $this->numero_cuenta = $numero_cuenta;
        $this->clave_interbancaria = $clave_interbancaria;
        $this->nombre_corto = $nombre_corto;
        $this->moneda = $moneda;
    }
}


class Empresa14
{
    public $id;
    public $razon;
    public $rfc;
    public $cuentas;
    public $infoTotal;

    function __construct($id, $razon, $rfc)
    {
        $this->id = $id;
        $this->razon = $razon;
        $this->rfc = $rfc;
    }
}


class AdminEmpresas14 extends conectorS14
{
    public function dameCuentas()
    {
        $query = "SELECT b.id, b.banco, b.numero_cuenta, b.clabe_interbancaria, b.nombre_corto, b.moneda, e.razon as empresaN  FROM bancos b inner join empresas e on b.razon = e.id where status_banco!= 'BA' order by e.razon;";
        $result = $this->ejecutar($query);
        $cuentas = array();
        while ($row = mysqli_fetch_array($result)) {
            $cuenta = new Cuenta14($row["id"], $row["empresaN"], $row["banco"], $row["numero_cuenta"], $row["clabe_interbancaria"], $row["nombre_corto"], $row["moneda"]);
            array_push($cuentas, $cuenta);
        }
        return $cuentas;
    }

    public function dameCuentasPorEmpresa($empresa)
    {
        $query = "SELECT b.id, b.banco, b.numero_cuenta, b.clabe_interbancaria, b.nombre_corto, b.moneda, e.razon as empresaN  FROM bancos b inner join empresas e on b.razon = e.id where status_banco!= 'BA' and b.razon = $empresa order by e.razon;";
        $result = $this->ejecutar($query);
        $cuentas = array();
        while ($row = mysqli_fetch_array($result)) {
            $cuenta = new Cuenta14($row["id"], $row["empresaN"], $row["banco"], $row["numero_cuenta"], $row["clabe_interbancaria"], $row["nombre_corto"], $row["moneda"]);
            array_push($cuentas, $cuenta);
        }
        return $cuentas;
    }


    public function dameEmpresas()
    {
        $query = "SELECT id, razon, rfc FROM empresas order by razon;";
        $result = $this->ejecutar($query);
        $empresas = array();
        while ($row = mysqli_fetch_array($result)) {
            $empresa = new Empresa14($row["id"], $row["razon"], $row["rfc"]);
            $empresa->cuentas = $this->dameCuentasPorEmpresa($empresa->id);
            $empresa->infoTotal = "";
            foreach ($empresa->cuentas as $cuenta) {
                $empresa->infoTotal .= "EMPRESA: " . $cuenta->empresa . " | BANCO: " . $cuenta->banco . " | CUENTA: " . $cuenta->numero_cuenta . " | CLABE: " . $cuenta->clave_interbancaria . " | MONEDA: " . $cuenta->moneda . " | NOMBRE CORTO: " . $cuenta->nombre_corto . " | ";
            }
            array_push($empresas, $empresa);
        }
        return $empresas;
    }
}
