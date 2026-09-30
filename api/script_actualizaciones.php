<?php

include_once 'conectorBDS14.php';
include_once 'conectorBD.php';
class Aviso
{
    public $estatus;
    public $mensaje;
    public $empresas; // Add this property

    public function __construct()
    {
        $this->estatus = '';
        $this->mensaje = '';
        $this->empresas = array(); // Initialize the property
    }
}

class Empresa
{
    public $id;
    public $razon;
    public $rfc;
    public $calle;
    public $numero;
    public $colonia;
    public $cp;
    public $estado;
    public $logo;
    public $regimen;
    public $passwordR;
    public $usuario;


    public function __construct()
    {
        $this->id = 0;
        $this->razon = '';
        $this->rfc = '';
        $this->calle = '';
        $this->numero = '';
        $this->colonia = '';
        $this->cp = '';
        $this->estado = '';
        $this->logo = '';
        $this->regimen = '';
        $this->passwordR = '';
        $this->usuario = '';
    }
}


class cuentasBanco
{
    public $id;
    public $banco;
    public $cuenta;
    public $clabe;
    public $empresa;
    public $moneda;


    public function __construct()
    {
        $this->id = 0;
        $this->banco = '';
        $this->cuenta = '';
        $this->clabe = '';
        $this->empresa = '';
        $this->moneda = '';
    }
}




class AdminEmpresasS14 extends conectorS14
{
    public function dameEmpresas()
    {
        $sql = "SELECT * FROM empresas WHERE id NOT IN (6, 99, 101)";
        $result = $this->ejecutar($sql);
        $empresas = array();
        while ($row = mysqli_fetch_array($result)) {
            $empresa = new Empresa();
            $empresa->id = $row['id'];
            $empresa->razon = $row['razon'];
            $empresa->rfc = $row['rfc'];
            $empresa->calle = $row['calle'];
            $empresa->numero = $row['numero'];
            $empresa->colonia = $row['colonia'];
            $empresa->cp = $row['cp'];
            $empresa->estado = $row['estado'];
            $empresa->logo = $row['logo'];
            $empresa->regimen = $row['regimen'];
            $empresa->passwordR = $row['password_r'];
            $empresa->usuario = $row['usuario'];
            array_push($empresas, $empresa);
        }
        return $empresas;
    }
}

class AdminEmpresas extends conector
{

    public function agregarEmpresa($razon, $rfc, $calle, $numero, $colonia, $cp, $estado, $logo, $regimen, $passwordR, $usuario)
    {

        // Inserta la empresa en la base de datos
        $sql = "INSERT INTO empresas (razon, rfc, calle, numero, colonia, cp, estado, logo, regimen, password_r, usuario, inicio_dominio, fin_dominio, prioridad, telefono, correo, sitio_web) 
        VALUES ('$razon', '$rfc', '$calle', '$numero', '$colonia', '$cp', '$estado', '$logo', '$regimen', '$passwordR', '$usuario', '', '', '', '', '', '')";
        return $this->ejecutar($sql);
    }


    public function dameEmpresas()
    {
        $sql = "SELECT * FROM empresas";
        $result = $this->ejecutar($sql);
        $empresas = array();
        while ($row = mysqli_fetch_array($result)) {
            $empresa = new Empresa();
            $empresa->id = $row['id'];
            $empresa->razon = $row['razon'];
            $empresa->rfc = $row['rfc'];
            $empresa->calle = $row['calle'];
            $empresa->numero = $row['numero'];
            $empresa->colonia = $row['colonia'];
            $empresa->cp = $row['cp'];
            $empresa->estado = $row['estado'];
            $empresa->logo = $row['logo'];
            $empresa->regimen = $row['regimen'];
            $empresa->passwordR = $row['password_r'];
            $empresa->usuario = $row['usuario'];
            array_push($empresas, $empresa);
        }
        return $empresas;
    }
}


// Crea instancias de AdminEmpresasS14 y AdminEmpresas
$adminEmpresasS14 = new AdminEmpresasS14();
$adminEmpresas = new AdminEmpresas();


// Obtiene los conjuntos de empresas de cada clase
$empresasS14 = $adminEmpresasS14->dameEmpresas();
$empresas = $adminEmpresas->dameEmpresas();

// Arreglo para guardar las empresas que no estan en la base de datos
$nuevasEmpresas = array();


// Compara los dos conjuntos de empresas usando array_udiff()
$diferencias = array_udiff($empresasS14, $empresas, function ($a, $b) {
    return strcmp($a->rfc, $b->rfc);
});


if (count($diferencias) > 0) {
    // Hay diferencias entre los dos conjuntos de empresas
    foreach ($diferencias as $empresa) {
        // Agrega la nueva empresa al conjunto de empresas del sistema y al array de nuevas empresas
        $adminEmpresas->agregarEmpresa($empresa->razon, $empresa->rfc, $empresa->calle, $empresa->numero, $empresa->colonia, $empresa->cp, $empresa->estado, "https://sistema14.com/" . $empresa->logo, $empresa->regimen, $empresa->passwordR, $empresa->usuario, '', '', '', '', '', '', '', '');
        array_push($nuevasEmpresas, $empresa);
    }
    // Crea un aviso con información sobre las nuevas empresas detectadas
    $aviso = new Aviso();
    $aviso->estatus = 'success';
    $aviso->mensaje = 'Se han detectado nuevas empresas desde SISTEMA 14';
    // mostrar las empresas detectadas solo con el nombre y el RFC
    $aviso->empresas = array_map(function ($empresa) {
        return array('nombre' => $empresa->razon, 'rfc' => $empresa->rfc);
    }, $nuevasEmpresas);
    // Imprime el aviso como una cadena JSON
    echo json_encode($aviso);
} else {
    // No hay nuevas empresas
    $aviso = new Aviso();
    $aviso->estatus = 'info';
    $aviso->mensaje = 'No se han detectado nuevas empresas desde SISTEMA 14';
    echo json_encode($aviso);
}


class AdminCuentasBancoS14 extends conectorS14
{
    public function dameCuentasBanco()
    {
        $sql = "SELECT bancos.*, empresas.razon FROM bancos INNER JOIN empresas ON bancos.razon = empresas.id WHERE status_banco = 'AC'";
        $result = $this->ejecutar($sql);
        $cuentasBanco = array();
        while ($row = mysqli_fetch_array($result)) {
            $cuentaBanco = new CuentasBanco();
            $cuentaBanco->id = $row['id'];
            $cuentaBanco->empresa = $row['razon'];
            $cuentaBanco->banco = $row['banco'];
            $cuentaBanco->cuenta = $row['numero_cuenta'];
            $cuentaBanco->clabe = $row['clabe_interbancaria'];
            $cuentaBanco->moneda = $row['moneda'];
            array_push($cuentasBanco, $cuentaBanco);
        }
        return $cuentasBanco;
    }
}


class AdminCuentasBanco extends conector
{
    public function agregarCuentaBanco($empresa, $banco, $cuenta, $clabe, $moneda)
    {
        $sql = "INSERT INTO cuentas_banco (empresa, banco, cuenta, clave, moneda) 
        VALUES ('$empresa', '$banco', '$cuenta', '$clabe', '$moneda')";
        $this->ejecutar($sql);
    }

    public function dameCuentasBanco()
    {
        $sql = "SELECT * FROM cuentas_banco";
        $result = $this->ejecutar($sql);
        $cuentasBanco = array();
        while ($row = mysqli_fetch_array($result)) {
            $cuentaBanco = new CuentasBanco();
            $cuentaBanco->id = $row['id'];
            $cuentaBanco->empresa = $row['empresa'];
            $cuentaBanco->banco = $row['banco'];
            $cuentaBanco->cuenta = $row['cuenta'];
            $cuentaBanco->clabe = $row['clave'];
            $cuentaBanco->moneda = $row['moneda'];
            array_push($cuentasBanco, $cuentaBanco);
        }
        return $cuentasBanco;
    }
}


$adminCuentasBancoS14 = new AdminCuentasBancoS14();
$cuentasBancoS14 = $adminCuentasBancoS14->dameCuentasBanco();

$adminCuentasBanco = new AdminCuentasBanco();
$cuentasBanco = $adminCuentasBanco->dameCuentasBanco();

$nuevasCuentasBanco = array();

if (count($cuentaBanco) != count($cuentasBancoS14)) {
    for ($i = 0; $i < count($cuentasBancoS14); $i++) {
        for ($j = 0; $j < count($cuentasBanco); $j++) {
            if ($cuentasBancoS14[$i]->cuenta === $cuentasBanco[$j]->cuenta) {
                $encontrada = true;
                break;
            }
        }
        if (!$encontrada) {
            $adminCuentasBanco->agregarCuentaBanco($cuentasBancoS14[$i]->empresa, $cuentasBancoS14[$i]->banco, $cuentasBancoS14[$i]->cuenta, $cuentasBancoS14[$i]->clabe, $cuentasBancoS14[$i]->moneda);
            array_push($nuevasCuentasBanco, $cuentasBancoS14[$i]);
        }
        $encontrada = false;
    }

    // $aviso = new Aviso();
    // $aviso->estatus = 'detectado';
    // $aviso->mensaje = 'Se han detectado nuevas cuentas bancarias desde SISTEMA 14';
    // $aviso->cuentasBanco = $nuevasCuentasBanco;
    // echo json_encode($aviso);
}
