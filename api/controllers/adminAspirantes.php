<?php
include_once('../config/conectorBD.php');

class Aspirante
{
    public $id;
    public $idEmpleado;
    public $llave;
    public $correo;

    function __construct()
    {
        $this->id = 0;
    }
}


class AdministradorAspirantes extends conector
{
    public function GUIDv4($trim = true)
    {
        // Windows
        if (function_exists('com_create_guid') === true) {
            if ($trim === true)
                return trim(com_create_guid(), '{}');
            else
                return com_create_guid();
        }

        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        // Fallback (PHP 4.2+)
        mt_srand((float)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                  // "-"
        $lbrace = $trim ? "" : chr(123);    // "{"
        $rbrace = $trim ? "" : chr(125);    // "}"
        $guidv4 = $lbrace .
            substr($charid,  0,  8) . $hyphen .
            substr($charid,  8,  4) . $hyphen .
            substr($charid, 12,  4) . $hyphen .
            substr($charid, 16,  4) . $hyphen .
            substr($charid, 20, 12) .
            $rbrace;
        return $guidv4;
    }

    public function agregarAspirante($idEmpleado, $llave, $correo)
    {
        $sql = "INSERT INTO `aspirante`(`llave`, `correo`, `id_empleado`) VALUES ('$llave', '$correo', '$idEmpleado')";
        $this->ejecutar($sql);
    }

    public function dameAspirantePorLlave($llave)
    {
        $sql = "SELECT * FROM `aspirante` WHERE `llave` = '$llave'";
        $resultado = $this->ejecutar($sql);
        $row = $resultado->fetch_assoc();
        $aspirante = new Aspirante();
        $aspirante->id = $row['id'];
        $aspirante->idEmpleado = $row['id_empleado'];
        $aspirante->llave = $row['llave'];
        $aspirante->correo = $row['correo'];
        return $aspirante;
    }

    public function eliminarPorCorreo($correo)
    {
        $sql = "DELETE FROM `aspirante` WHERE `correo` = '$correo'";
        $this->ejecutar($sql);
    }
    
    public function dameAspirante($id)
    {
        $sql = "SELECT * FROM `aspirante` WHERE `id` = '$id'";
        $resultado = $this->ejecutar($sql);
        $aspirante = $resultado->fetch_assoc();
        $aspirante = new Aspirante();
        $aspirante->id = $aspirante['id'];
        $aspirante->idEmpleado = $aspirante['id_empleado'];
        $aspirante->llave = $aspirante['llave'];
        $aspirante->correo = $aspirante['correo'];
        return $aspirante;
    }
}
