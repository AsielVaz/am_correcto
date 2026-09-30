<?php
include_once('../config/conectorBD.php');

class Postergada

{
    public $id;
    public $razonSocial;
    public $idEmpresa;
    public $estatus;
    public $campoAPostergar;
    public $fechaPostergacion;


    public function __construct()
    {
        $this->id = 0;
        $this->idEmpresa = 0;
        $this->estatus = '';
        $this->campoAPostergar = '';
        $this->fechaPostergacion = '';
    }
}


class AdministradorCamposPostergados extends conector
{
    private static $fechaPostergacionVerificada = false;

    private function asegurarPrecisionFechaPostergacion()
    {
        if (self::$fechaPostergacionVerificada) {
            return;
        }

        $consulta = "SHOW COLUMNS FROM campos_postergados LIKE 'fecha_postergacion'";
        $resultado = $this->ejecutar($consulta);
        if ($resultado && $columna = $resultado->fetch_assoc()) {
            $tipo = isset($columna['Type']) ? strtolower($columna['Type']) : '';
            if (strpos($tipo, 'datetime') === false && strpos($tipo, 'timestamp') === false) {
                $alter = "ALTER TABLE campos_postergados MODIFY COLUMN fecha_postergacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP";
                $this->ejecutar($alter);
            }
        }

        self::$fechaPostergacionVerificada = true;
    }

    public function agregarCampoPostergado($idEmpresa, $campoAPostergar, $observaciones)
    {
        $this->asegurarPrecisionFechaPostergacion();

        $idEmpresa = (int) $idEmpresa;
        $campoAPostergar = trim($campoAPostergar);
        $observaciones = is_string($observaciones) ? $observaciones : '';
        if (mb_strlen($observaciones) > 500) {
            $observaciones = mb_substr($observaciones, 0, 500);
        }
        $observacionesEscapadas = addslashes($observaciones);

        $sql = "INSERT INTO campos_postergados (id_empresa, campo_a_postergar, estatus_postergado, observaciones, fecha_postergacion) VALUES ($idEmpresa, '$campoAPostergar', 1, '$observacionesEscapadas', now())";
        $this->ejecutar($sql);
        // verificar si se inserto
        $sql = "SELECT * FROM campos_postergados WHERE id_empresa = $idEmpresa AND campo_a_postergar = '$campoAPostergar'";
        $resultado = $this->ejecutar($sql);
        if ($resultado->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function dameCamposPostergados($idEmpresa)
    {
        $sql = "SELECT * FROM campos_postergados WHERE id_empresa = $idEmpresa;";
        $result = $this->ejecutar($sql);
        $camposPostergados = array();
        while ($row = $result->fetch_assoc()) {
            $campoPostergado = new Postergada();
            $campoPostergado->id = $row['id'];
            $campoPostergado->idEmpresa = $row['id_empresa'];
            $campoPostergado->estatus = $row['estatus_postergado'];
            $campoPostergado->campoAPostergar = $row['campo_a_postergar'];
            $campoPostergado->fechaPostergacion = $row['fecha_postergacion'];
            array_push($camposPostergados, $campoPostergado);
        }
        return $camposPostergados;
    }

    public function estadoPostergado($id, $campo, $estatus)
    {
        $sql = "SELECT * FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = '$campo' AND estatus_postergado = $estatus";
        $result = $this->ejecutar($sql);
        $row = $result->fetch_assoc();
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    public function eliminarCampoPostergado($id)
    {
        $sql = "DELETE FROM campos_postergados WHERE id = $id;";
        $this->ejecutar($sql);
    }

    public function actualizarEstatusPostergado($id, $estatus)
    {
        $sql = "UPDATE campos_postergados SET estatus_postergado = $estatus WHERE id = $id;";
        $this->ejecutar($sql);
    }

    // comprobar si hay mas de 3 veces el mismo campo postergado
    public function comprobarPostergado($id, $campo)
    {
        $sql = "SELECT * FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = '$campo';";
        $result = $this->ejecutar($sql);
        $contador = 0;
        while ($row = $result->fetch_assoc()) {
            $contador++;
        }
        if ($contador >= 3) {
            return true;
        } else {
            return false;
        }
    }

    public function dameEmpresa($id)
    {
        $sql = "SELECT * FROM empresas WHERE id = $id;";
        $result = $this->ejecutar($sql);
        $row = $result->fetch_assoc();
        // retornar la razon social y la imagen de la empresa
        return $row;

        // inner join con la tabla de empresas a tabla de campos postergados
        $sql = "SELECT * FROM campos_postergados INNER JOIN empresas ON campos_postergados.id_empresa = empresas.id WHERE campos_postergados.id_empresa = $id;";
    }

    public function completarTarea($id, $campo)
    {
        // si el id y el campo no estan en la tabla de campos postergados entonces insertarlos
        $sql = "SELECT * FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = '$campo';";
        $result = $this->ejecutar($sql);
        $row = $result->fetch_assoc();
        if (!$row) {
            $this->asegurarPrecisionFechaPostergacion();
            $sql = "INSERT INTO campos_postergados (id_empresa, campo_a_postergar, estatus_postergado, fecha_postergacion) VALUES ($id, '$campo', 2, now());";
            $this->ejecutar($sql);
        } else {
            // si el id y el campo estan en la tabla de campos postergados entonces actualizar el estatus
            $sql = "UPDATE campos_postergados SET estatus_postergado = 2 WHERE id_empresa = $id AND campo_a_postergar = '$campo';";
            $this->ejecutar($sql);
        }
        // retornar true si se inserto o actualizo
        $sql = "SELECT * FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = '$campo' AND estatus_postergado = 2;";
        $result = $this->ejecutar($sql);
        $row = $result->fetch_assoc();
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    public function cambiarEstadoPorExcesoDePostergacion($id, $campo)
    {
        $sql = "UPDATE campos_postergados SET estatus_postergado = 3 WHERE id_empresa = $id AND campo_a_postergar = '$campo';";
        $this->ejecutar($sql);
        // si se actualizo el estatus entonces retornar true
        $sql = "SELECT * FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = '$campo' AND estatus_postergado = 3;";
        $result = $this->ejecutar($sql);
        $row = $result->fetch_assoc();
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    public function dameObservaciones($id, $campo)
    {
        // retornar todas las observaciones de un campo postergado mientras que observaciones sea diferente de un string vacio
        $sql = "SELECT * FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = '$campo' AND observaciones != '';";
        $result = $this->ejecutar($sql);
        $observaciones = array();
        while ($row = $result->fetch_assoc()) {
            array_push($observaciones, $row['observaciones']);
        }
        return $observaciones;
    }

    public function contarPostergaciones($idEmpresa, $campo)
    {
        $idEmpresa = (int) $idEmpresa;
        $campo = trim($campo);
        $sql = "SELECT COUNT(*) AS total FROM campos_postergados WHERE id_empresa = $idEmpresa AND campo_a_postergar = '$campo'";
        $result = $this->ejecutar($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return isset($row['total']) ? (int) $row['total'] : 0;
        }
        return 0;
    }

    public function dameHistorialPostergacion($idEmpresa, $campo)
    {
        $idEmpresa = (int) $idEmpresa;
        $campo = trim($campo);
        $sql = "SELECT id, observaciones, estatus_postergado, fecha_postergacion FROM campos_postergados WHERE id_empresa = $idEmpresa AND campo_a_postergar = '$campo' ORDER BY fecha_postergacion DESC, id DESC";
        $result = $this->ejecutar($sql);
        $historial = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $historial[] = [
                    'id' => isset($row['id']) ? (int) $row['id'] : 0,
                    'observaciones' => isset($row['observaciones']) ? $row['observaciones'] : '',
                    'estatus' => isset($row['estatus_postergado']) ? (int) $row['estatus_postergado'] : null,
                    'fecha' => isset($row['fecha_postergacion']) ? $row['fecha_postergacion'] : null,
                ];
            }
        }
        return $historial;
    }

    public function resetPostergacionesCampo($idEmpresa, $campo)
    {
        $idEmpresa = (int) $idEmpresa;
        $campo = trim($campo);
        if ($idEmpresa <= 0 || $campo === '') {
            return false;
        }

        $sql = "DELETE FROM campos_postergados WHERE id_empresa = $idEmpresa AND campo_a_postergar = '$campo'";
        return $this->ejecutar($sql) !== false;
    }
}
