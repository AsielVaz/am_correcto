<?php
include_once('../config/conectorBD.php');


class Correo
{
    public $id;
    public $empresa;
    public $correo;
    public $password;

    public function __construct($id, $empresa, $correo, $password)
    {
        $this->id = $id;
        $this->empresa = $empresa;
        $this->correo = $correo;
        $this->password = $password;
    }
}

class adminCorreo extends conector{

    public function agregaCorreo($empresa,$correo,$password){
        $sql="INSERT INTO empresas_correo (empresa, correo, password) VALUES ('$empresa','$correo'.'$password')";
        $result = $this->ejecutar($sql);
        return $result;
    }

    public function dame_correo($id){
        $sql = "SELECT id, empresa, correo, password FROM empresas_correo WHERE id =  '$id' ";
        $result = $this->ejecutar($sql);
        $dato = $result->fetch_assoc();
        $correo = new  Correo($dato['id'], $dato['empresa'], $dato['correo'], $dato['password']);
        return $correo;
    }

    public function dameCorreos(){
        $sql = "SELECT id, empresa, correo, password FROM empresas_correo ";
        $result = $this->ejecutar($sql);
        $dato = $result->fetch_assoc();
        $correos = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $correo = new  Correo($row['id'], $row['empresa'], $row['correo'], $row['password']);
            array_push($correos, $correo);
        }
        return $correos;
    }

    public function modificaCorreo($id,$empresa,$correo){
        $sql="UPDATE empresas_correo SET empresa = '$empresa' , correo = '$correo' where id='$id'; ";
        $result = $this->ejecutar($sql);
        return $result;
    }

    public function modificaCorreoPassword($id,$password){
        $sql="UPDATE empresas_correo SET (password) VALUES ('$password') where id='$id'; ";
        $result = $this->ejecutar($sql);
        return $result;
    }

    public function bajaCorreo($id){
        $sql = "DELETE FROM empresas_correo where id='$id';";
        $result= $this->ejecutar($sql);
        return $result;
    }
}

?>