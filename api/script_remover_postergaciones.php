<?php

include_once("conectorBD.php");

class postergaciones
{
    public $id;
    public $estatus;
    public $fechaPostergacion;


    public function __construct()
    {
        $this->id = 0;
        $this->estatus = '';
        $this->fechaPostergacion = '';
    }
}


class  ScriptRemoverPostergaciones extends conector
{

    public function dameCamposPostergados()
    {
        $sql = "SELECT * FROM campos_postergados WHERE estatus_postergado = 1 ";
        $result = $this->ejecutar($sql);
        $camposPostergados = array();
        while ($row = $result->fetch_assoc()) {
            $campoPostergado = new postergaciones();
            $campoPostergado->id = $row['id'];
            $campoPostergado->estatus = $row['estatus_postergado'];
            $campoPostergado->fechaPostergacion = $row['fecha_postergacion'];
            array_push($camposPostergados, $campoPostergado);
        }
        return $camposPostergados;
    }
}


$script = new ScriptRemoverPostergaciones();
$camposPostergados = $script->dameCamposPostergados();

// actualizar estatus_postergado a 0
foreach ($camposPostergados as $campoPostergado) {
    $id = $campoPostergado->id;
    $sql = "UPDATE campos_postergados SET estatus_postergado = 0 WHERE id = $id";
    $script->ejecutar($sql);
}
