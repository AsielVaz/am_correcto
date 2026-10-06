<?php


include_once('../config/conectorBD.php');
include_once('../utils/encriptador.php');

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
    public $pdf;
    public $inicioDominio;
    public $finDominio;
    public $prioridad;
    public $telefono;
    public $correo;
    public $descripcion;
    public $sitioWeb;
    public $correos;
    public $periodoTel;
    public $periodoCorreo;
    public $periodoSitio;
    public $periodoDireccion;
    public $periodoWeb;
    public $comprobanteDom;
    public $constanciaSf;
    public $cuentas;
    public $ultimoPdf;
    public $ultimaConstancia;
    public $periodoComprobanteDom;
    public $periodoConstancia;
    public $periodo32d;
    public $aplicaImss;

    public function __construct($id, $razon, $rfc, $calle, $numero, $colonia, $cp, $estado, $logo, $regimen, $passwordR, $usuario, $pdf, $inicioDominio, $finDominio, $prioridad, $telefono, $correo, $descripcion, $sitioWeb)
    {
        $this->id = $id;
        $this->razon = $razon;
        $this->rfc = $rfc;
        $this->calle = $calle;
        $this->numero = $numero;
        $this->colonia = $colonia;
        $this->cp = $cp;
        $this->estado = $estado;
        $this->logo = $logo;
        $this->regimen = $regimen;
        $this->passwordR = $passwordR;
        $this->usuario = $usuario;
        $this->pdf = $pdf;
        $this->inicioDominio = $inicioDominio;
        $this->finDominio = $finDominio;
        $this->prioridad = $prioridad;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->descripcion = $descripcion;
        $this->sitioWeb = $sitioWeb;
        $this->cuentas = array();
        $this->ultimoPdf = null;
        $this->ultimaConstancia = null;
        $this->periodoComprobanteDom = null;
        $this->periodoConstancia = null;
        $this->periodo32d = null;
        $this->aplicaImss = 1; // valor por defecto, se sobreescribe en dameEmpresas
    }
}

class Correo
{
    public $correo;
    public $id;
    public $empresa;
    public $fecha;

    public function __construct($id, $correo, $empresa, $fecha)
    {
        $this->correo = $correo;
        $this->id = $id;
        $this->empresa = $empresa;
        $this->fecha = $fecha;
    }
}

class Cuenta
{
    public $id;
    public $banco;
    public $cuenta;
    public $clave;
    public $empresa;
    public $estatus;
    public $moneda;

    public function __construct($id, $banco, $cuenta, $clave, $empresa, $estatus, $moneda)
    {
        $this->id = $id;
        $this->banco = $banco;
        $this->cuenta = $cuenta;
        $this->clave = $clave;
        $this->empresa = $empresa;
        $this->estatus = $estatus;
        $this->moneda = $moneda;
    }
}


class contraSat
{
    public $empresa;
    public $id;
    public $usuario;
    public $contrasena;
    public $rfc;
    public $nombreEmpresa;

    public function __construct($empresa, $id, $usuario, $contrasena, $rfc)
    {
        $this->empresa = $empresa;
        $this->id = $id;
        $this->usuario = $usuario;
        $this->contrasena = $contrasena;
        $this->rfc = $rfc;
    }
}


class contrasIofacturo
{
    public $rfc;
    public $id;
    public $contrasena;
    public $usuario;
    public $empresa;

    public function __construct($rfc, $id, $contrasena, $usuario)
    {
        $this->rfc = $rfc;
        $this->id = $id;
        $this->contrasena = $contrasena;
        $this->usuario = $usuario;
    }
}


class contrasBanco
{
    public $id;
    public $usuario;
    public $contrasena;
    public $claveOp;
    public $nip;
    public $banco;
    public $empresa;


    public function __construct($id, $usuario, $contrasena, $claveOp, $nip, $banco)
    {
        $this->id = $id;
        $this->usuario = $usuario;
        $this->contrasena = $contrasena;
        $this->claveOp = $claveOp;
        $this->nip = $nip;
        $this->banco = $banco;
    }
}

class rppc
{
    public $rppcDoc;
    public $id;
    public $empresa;
}

class Fiel
{
    public $id;
    public $empresa;
    public $documento1;
    public $tipo;
    public $fechaCreacion;
    public $empresaNom;

    public function __construct($id, $empresa, $documento1, $tipo, $fechaCreacion)
    {
        $this->id = $id;
        $this->empresa = $empresa;
        $this->documento1 = $documento1;
        $this->tipo = $tipo;
        $this->fechaCreacion = $fechaCreacion;
    }
}


class Sello_sat
{
    public $id;
    public $empresa;
    public $documento1;
    public $tipo;
    public $fechaCreacion;
    public $empresaNom;

    public function __construct($id, $empresa, $documento1, $tipo, $fechaCreacion)
    {
        $this->id = $id;
        $this->empresa = $empresa;
        $this->documento1 = $documento1;
        $this->tipo = $tipo;
        $this->fechaCreacion = $fechaCreacion;
    }
}

class Estado_cuenta
{
    public $id;
    public $empresa;
    public $documento1;
    public $tipo;
    public $fechaCreacion;
    public $empresaNom;

    public function __construct($id, $empresa, $documento1, $tipo, $fechaCreacion)
    {
        $this->id = $id;
        $this->empresa = $empresa;
        $this->documento1 = $documento1;
        $this->tipo = $tipo;
        $this->fechaCreacion = $fechaCreacion;
    }
}

class Caratulas
{
    public $id;
    public $empresa;
    public $documento1;
    public $tipo;
    public $fechaCreacion;
    public $empresaNom;

    public function __construct($id, $empresa, $documento1, $tipo, $fechaCreacion, $empresaNom)
    {
        $this->id = $id;
        $this->empresa = $empresa;
        $this->documento1 = $documento1;
        $this->tipo = $tipo;
        $this->fechaCreacion = $fechaCreacion;
        $this->empresaNom = $empresaNom;
    }
}


class ActasConstitutivas
{
    public $id;
    public $empresa;
    public $documento;
    public $fechaCreacion;
    public $empresaNom;
    public $contenido;
    public $estadoDocumento;
    public $fechaRevision;
    public $fechaActualizacion;
    public $observaciones;

    public function __construct($id, $empresa, $documento, $fechaCreacion, $estadoDocumento = null)
    {
        $this->id = $id;
        $this->empresa = $empresa;
        $this->documento = $documento;
        $this->fechaCreacion = $fechaCreacion;
        $this->estadoDocumento = $estadoDocumento
            ? strtolower(trim($estadoDocumento))
            : 'completado';
        $this->fechaRevision = null;
        $this->fechaActualizacion = null;
    }
}


class ContraFiel
{
    public $id;
    public $contrasena;
    public $empresa;


    public function __construct($id, $contrasena, $empresa)
    {
        $this->id = $id;
        $this->contrasena = $contrasena;
        $this->empresa = $empresa;
    }
}

class ContraSello
{
    public $id;
    public $contrasena;
    public $empresa;


    public function __construct($id, $contrasena, $empresa)
    {
        $this->id = $id;
        $this->contrasena = $contrasena;
        $this->empresa = $empresa;
    }
}


class Imss
{
    public $id;
    public $documento;
    public $empresa;
    public $fechaCreacion;
    public $tipo;

    public function __construct($id, $documento, $empresa, $fechaCreacion, $tipo)
    {
        $this->id = $id;
        $this->documento = $documento;
        $this->empresa = $empresa;
        $this->fechaCreacion = $fechaCreacion;
        $this->tipo = $tipo;
    }
}


class ContraImss
{
    public $id;
    public $contrasena;
    public $empresa;
    public $tipo;
    public $usuario;


    public function __construct($id, $contrasena, $empresa, $tipo, $usuario)
    {
        $this->id = $id;
        $this->contrasena = $contrasena;
        $this->empresa = $empresa;
        $this->tipo = $tipo;
        $this->usuario = $usuario;
    }
}



class AdministradorEmpresa extends conector
{
    private static $tablaColumnasCache = [];

    private function tablaTieneColumna($tabla, $columna)
    {
        $cacheKey = $tabla . '.' . $columna;
        if (array_key_exists($cacheKey, self::$tablaColumnasCache)) {
            return self::$tablaColumnasCache[$cacheKey];
        }

        $tablaEscapada = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $tabla);
        $columnaEscapada = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $columna);

        if (!$tablaEscapada || !$columnaEscapada) {
            self::$tablaColumnasCache[$cacheKey] = false;
            return false;
        }

        $sql = "SHOW COLUMNS FROM `{$tablaEscapada}` LIKE '{$columnaEscapada}'";
        $resultado = $this->ejecutar($sql);
        $existe = ($resultado instanceof \mysqli_result) ? $resultado->num_rows > 0 : false;
        self::$tablaColumnasCache[$cacheKey] = $existe;
        return $existe;
    }

    private function normalizarEstadoDocumento($estado)
    {
        $estadoNormalizado = strtolower(trim((string) $estado));

        switch ($estadoNormalizado) {
            case 'archivo_faltante':
            case 'urgente':
                return 'archivo_faltante';
            case 'revision':
                return 'revision';
            case 'rechazado':
                return 'rechazado';
            case 'completado':
            case 'normal':
                return 'completado';
            case 'sin_datos':
                return 'sin_datos';
            default:
                return 'completado';
        }
    }

    public function agregaContraImss($contrasena, $empresa, $tipo, $usuario)
    {
        $sql = "INSERT INTO contras_imss (contrasena, id_empresa, tipo, usuario, fec_creacion) 
        VALUES ('$contrasena', '$empresa', '$tipo', '$usuario', now())";
        $this->ejecutar($sql);
    }

    public function eliminarContraImss($id)
    {
        $sql = "DELETE FROM contras_imss WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function dameContraImssPorEmpresa($empresa, $tipo)
    {
        $sql = "SELECT * FROM contras_imss WHERE id_empresa = '$empresa' and tipo = '$tipo' order by fec_creacion desc limit 1";
        $resultado = $this->ejecutar($sql);
        $contraImss = array();
        while ($fila = $resultado->fetch_assoc()) {
            $contraImss[] = new ContraImss($fila['id'], $fila['contrasena'], $fila['empresa'], $fila['tipo'], $fila['usuario']);
        }
        return $contraImss[0]->contrasena;
    }

    public function dameContraImssEmpresa($empresa)
    {
        $sql = "SELECT * FROM contras_imss WHERE id_empresa = '$empresa' order by fec_creacion desc";
        $resultado = $this->ejecutar($sql);
        $contraImss = array();
        while ($fila = $resultado->fetch_assoc()) {
            $contraImss[] = new ContraImss($fila['id'], $fila['contrasena'], $fila['empresa'], $fila['tipo'], $fila['usuario']);
        }
        return $contraImss;
    }

    public function agregarImms($documento, $empresa, $tipo)
    {
        $sql = "INSERT INTO imss (documento, id_empresa, tipo, fec_creacion) 
        VALUES ('$documento', '$empresa', '$tipo', now() )";
        $this->ejecutar($sql);
    }

    public function eliminarImms($id)
    {
        $sql = "DELETE FROM imss WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function dameImssPorempresa($empresa)
    {
        $sql = "SELECT * FROM imss WHERE id_empresa = '$empresa' order by fec_creacion desc";
        $resultado = $this->ejecutar($sql);
        $imss = array();
        while ($fila = $resultado->fetch_assoc()) {
            $imss[] = new Imss($fila['id'], $fila['documento'], $fila['empresa'], $fila['fec_creacion'], $fila['tipo']);
        }
        return $imss;
    }

    public function dameImss()
    {
        $sql = "SELECT * FROM imss order by fec_creacion desc";
        $resultado = $this->ejecutar($sql);
        $imss = array();
        while ($fila = $resultado->fetch_assoc()) {
            $imss[] = new Imss($fila['id'], $fila['documento'], $fila['empresa'], $fila['fec_creacion'], $fila['tipo']);
        }
        return $imss;
    }

    public function agregarCaratula($empresa, $documento, $tipo)
    {
        $encriptador = new Encriptador();
        $documento = $encriptador->encriptar($documento);
        $sql = "INSERT INTO caratulas (id_empresa, documento_1, tipo, fec_creacion) 
        VALUES ('$empresa', '$documento', '$tipo', now())";
        $this->ejecutar($sql);
    }

    public function dameRppcAsamblea($empresa)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT * FROM actas_const WHERE id_empresa = '$empresa' and contenido like '%rppca%' order by fec_creacion desc limit 1";
        $resultado = $this->ejecutar($sql);
        $actas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $actas[] = new ActasConstitutivas($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento']), $fila['fec_creacion']);
        }
        return $actas;
    }

    public function eliminarCaratula($id)
    {
        $sql = "DELETE FROM caratulas WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function dameCaratulas()
    {
        $encriptador = new Encriptador();
        $sql = "SELECT c.id, c.id_empresa, c.tipo, c.fec_creacion, c.documento_1, em.razon FROM caratulas c inner join empresas em on c.id_empresa = em.id";
        $resultado = $this->ejecutar($sql);
        $caratulas = array();
        while ($fila = $resultado->fetch_assoc()) {
            $documento = $encriptador->desencriptar($fila['documento_1']);
            $caratula = new Caratulas($fila['id'], $fila['id_empresa'], $documento, $fila['tipo'], $fila['fec_creacion'], $fila['razon']);
            array_push($caratulas, $caratula);
        }
        return $caratulas;
    }

    public function dameCaratulasPorEmpresa($empresa)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT c.id, c.id_empresa, c.tipo, c.fec_creacion, c.documento_1, em.razon FROM caratulas c inner join empresas em on c.id_empresa = em.id where c.id_empresa = '$empresa' and c.fec_creacion >" . date("Y") . "-" . date("m") . "-01";

        $resultado = $this->ejecutar($sql);
        $caratulas = array();

        while ($fila = $resultado->fetch_assoc()) {
            $documento = $encriptador->desencriptar($fila['documento_1']);
            $caratula = new Caratulas($fila['id'], $fila['id_empresa'], $documento, $fila['tipo'], $fila['fec_creacion'], $fila['razon']);
            array_push($caratulas, $caratula);
        }
        return $caratulas;
    }

    public function dameUltiaCaratula($empresa)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT c.id, c.id_empresa, c.tipo, c.fec_creacion, c.documento_1, em.razon FROM caratulas c inner join empresas em on c.id_empresa = em.id where c.id_empresa = '$empresa' order by c.fec_creacion desc limit 1";
        $resultado = $this->ejecutar($sql);

        $caratula = null;
        while ($fila = $resultado->fetch_assoc()) {
            $documento = $encriptador->desencriptar($fila['documento_1']);
            $caratula = new Caratulas($fila['id'], $fila['id_empresa'], $documento, $fila['tipo'], $fila['fec_creacion'], $fila['razon']);
        }
        return $caratula;
    }

    public function agregarEstadoCUenta($empresa, $documento, $tipo)
    {
        $encriptador = new Encriptador();
        $documento = $encriptador->encriptar($documento);
        $sql = "INSERT INTO estados_cuenta (id_empresa, documento_1, tipo, fec_creacion) 
        VALUES ('$empresa', '$documento', '$tipo', now())";
        $this->ejecutar($sql);
    }

    public function eliminarEstadoCuenta($id)
    {
        $sql = "DELETE FROM estados_cuenta WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function dameEstadosDeCuenta()
    {
        $sql = "SELECT e.id, e.id_empresa, e.tipo, e.fec_creacion, em.razon FROM estados_cuenta e inner join empresas em on estados_cuenta.id_empresa = empresas.id";
        $resultado = $this->ejecutar($sql);
        $estados = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $encriptador = new Encriptador();
            $documento = $encriptador->desencriptar($fila['documento_1']);
            $estado = new Estado_cuenta($fila['id'], $fila['id_empresa'], $documento, $fila['tipo'], $fila['fec_creacion']);
            array_push($estados, $estado);
        }
        return $estados;
    }

    public function dameEstadosDeCuentaPorEmpresa($empresa)
    {
        $sql = "SELECT * FROM estados_cuenta WHERE id_empresa = '$empresa' and fec_creacion >" . date("Y") . "-" . date("m") . "-01";;
        $resultado = $this->ejecutar($sql);
        $estados = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $encriptador = new Encriptador();
            $documento = $encriptador->desencriptar($fila['documento_1']);
            $estado = new Estado_cuenta($fila['id'], $fila['id_empresa'], $documento, $fila['tipo'], $fila['fec_creacion']);
            array_push($estados, $estado);
        }
        return $estados;
    }

    public function dameUltimoEstadoDeCuenta($empresa)
    {
        $sql = "SELECT * FROM estados_cuenta WHERE id_empresa = '$empresa' order by fec_creacion desc limit 1";
        $resultado = $this->ejecutar($sql);
        $estado = null;
        while ($fila = mysqli_fetch_array($resultado)) {
            $encriptador = new Encriptador();
            $documento = $encriptador->desencriptar($fila['documento_1']);
            $estado = new Estado_cuenta($fila['id'], $fila['id_empresa'], $documento, $fila['tipo'], $fila['fec_creacion']);
        }
        return $estado;
    }

    public function agregarActaConst($empresa, $documento, $contenido, $estadoDocumento = 'normal', $idUsuario = null)
    {
		$empresa = (int) $empresa;
		if ($empresa <= 0 || trim((string) $documento) === '') {
			return null;
		}

        $encriptador = new Encriptador();
        $documento = $encriptador->encriptar($documento);
        $estadoDocumentoNormalizado = $this->normalizarEstadoDocumento($estadoDocumento);

        // Construir inserción dinámicamente considerando columnas opcionales
        $cols = ['id_empresa', 'documento', 'contenido'];
		$vals = ['?', '?', '?'];
		$params = [$empresa, $documento, (string) $contenido];
		$types = 'iss';

        if ($this->tablaTieneColumna('actas_const', 'id_usuario') && $idUsuario !== null) {
            $cols[] = 'id_usuario';
			$vals[] = '?';
			$params[] = (int) $idUsuario;
			$types .= 'i';
        }

        if ($this->tablaTieneColumna('actas_const', 'estado_documento')) {
            $cols[] = 'estado_documento';
			$vals[] = '?';
			$params[] = $estadoDocumentoNormalizado;
			$types .= 's';
        }

        if ($this->tablaTieneColumna('actas_const', 'fec_revision')) {
            $cols[] = 'fec_revision';
            $vals[] = 'NULL';
        }

        $cols[] = 'fec_creacion';
        $vals[] = 'now()';

        if ($this->tablaTieneColumna('actas_const', 'fec_actualizacion')) {
            $cols[] = 'fec_actualizacion';
            $vals[] = 'now()';
        }

        $colsStr = implode(', ', $cols);
        $valsStr = implode(', ', $vals);
        $sql = "INSERT INTO actas_const ($colsStr) VALUES ($valsStr)";
		$this->preparar($sql, $params, $types);
		$idInsertado = $this->ultimoId();
		return $idInsertado > 0 ? $idInsertado : null;
    }

    public function agregarDocumentoPermanente($empresa, $documento, $descripcion = 'DocumentoPermanente', $idUsuario = null)
    {
        // Si la descripción es constancia de situación fiscal RL o socio, guardar como 'completado', si no como 'revision'
        $descLower = mb_strtolower($descripcion);
        $estado = 'revision';
        $now = new DateTime();
        $periodoMap = [
            'constancia' => 'periodoConstancia',
            'comprobante' => 'periodoComprobanteDom',
            '32d' => 'periodo32d',
        ];
        $periodoField = null;
        foreach ($periodoMap as $clave => $campo) {
            if (strpos($descLower, $clave) !== false) {
                $periodoField = $campo;
                break;
            }
        }
        if (strpos($descLower, 'constancia') !== false) {
            $estado = 'completado';
        }
		// Insertar y devolver el ID del nuevo registro
		$idDocumento = $this->agregarActaConst($empresa, $documento, $descripcion, $estado, $idUsuario);
        // Actualizar el periodo si corresponde
		if ($idDocumento && $periodoField) {
            $periodoValue = (clone $now)->modify('+3 months')->format('Y-m-d');
            $this->actualizarPeriodoEmpresa($empresa, $periodoField, $periodoValue);
        }
		return $idDocumento;
    }

    // Actualiza el campo de periodo en la tabla empresa
    public function actualizarPeriodoEmpresa($empresaId, $campo, $fechaVencimiento)
    {
        $empresaId = intval($empresaId);
        $permitidos = ['periodoConstancia', 'periodoComprobanteDom', 'periodo32d'];
        if (!in_array($campo, $permitidos, true)) return;
        $fecha = date('Y-m-d', strtotime($fechaVencimiento));
        $sql = "UPDATE empresas SET $campo = '$fecha' WHERE id = $empresaId";
        $this->ejecutar($sql);
    }

    public function eliminarActaConst($id)
    {
        $sql = "DELETE FROM actas_const WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function eliminarDocumentoPermanente($id)
    {
			$id = (int) $id;
			if ($id <= 0) {
				return false;
			}

			$this->iniciarTransaccion();
			try {
				foreach (['analisis_nombres', 'analisis_fechas', 'analisis_numeros'] as $tablaAnalisis) {
					$this->preparar("DELETE FROM `{$tablaAnalisis}` WHERE id_documento = ?", [$id], 'i');
				}

				$this->preparar('DELETE FROM actas_const WHERE id = ?', [$id], 'i');
				$verificacion = $this->preparar('SELECT id FROM actas_const WHERE id = ? LIMIT 1', [$id], 'i');
				if ($verificacion instanceof mysqli_result && $verificacion->num_rows > 0) {
					$this->revertirTransaccion();
					return false;
				}

				$this->confirmarTransaccion();
				return true;
			} catch (Throwable $e) {
				$this->revertirTransaccion();
				throw $e;
			}
		}

	public function obtenerRutaDocumentoPermanente($id): ?string
	{
		$id = (int) $id;
		if ($id <= 0) {
			return null;
		}
		$resultado = $this->preparar('SELECT documento FROM actas_const WHERE id = ? LIMIT 1', [$id], 'i');
		if (!$resultado instanceof mysqli_result || !($fila = $resultado->fetch_assoc())) {
			return null;
		}
		$rutaCifrada = (string) ($fila['documento'] ?? '');
		if ($rutaCifrada === '') {
			return '';
		}
		$encriptador = new Encriptador();
		return (string) $encriptador->desencriptar($rutaCifrada);
    }

    public function actualizarEstadoDocumentoPermanente($idDocumento, $estado, $idRevisor = null, $observaciones = null)
    {
        $id = (int) $idDocumento;
        if ($id <= 0) {
            return false;
        }

        if (!$this->tablaTieneColumna('actas_const', 'estado_documento')) {
            return false;
        }

        $estadoNormalizado = $this->normalizarEstadoDocumento($estado);
        $sets = ["estado_documento = '{$estadoNormalizado}'"];
        if ($observaciones !== null && $estadoNormalizado === 'rechazado' && $this->tablaTieneColumna('actas_const', 'observaciones')) {
            $observaciones = mb_substr($observaciones, 0, 500);
            $observacionesEscapadas = addslashes($observaciones);
            $sets[] = "observaciones = '" . $observacionesEscapadas . "'";
        } else if ($estadoNormalizado !== 'rechazado' && $this->tablaTieneColumna('actas_const', 'observaciones')) {
            $sets[] = "observaciones = NULL";
        }
        if ($idRevisor !== null && $this->tablaTieneColumna('actas_const', 'id_usuario_revisor')) {
            $sets[] = 'id_usuario_revisor = ' . ((int) $idRevisor);
        }
        if ($this->tablaTieneColumna('actas_const', 'fec_actualizacion')) {
            $sets[] = 'fec_actualizacion = NOW()';
        }
        if ($this->tablaTieneColumna('actas_const', 'fec_revision')) {
            if ($estadoNormalizado === 'completado' || $estadoNormalizado === 'rechazado') {
                $sets[] = 'fec_revision = NOW()';
            } else {
                $sets[] = 'fec_revision = NULL';
            }
        }
        $sql = 'UPDATE actas_const SET ' . implode(', ', $sets) . " WHERE id = '{$id}'";
        $resultado = $this->ejecutar($sql);
        return (bool) $resultado;
    }

    public function reemplazarDocumentoPermanente($idDocumento, $rutaDocumento, $idUsuario = null)
    {
        $id = (int) $idDocumento;
        if ($id <= 0 || !$rutaDocumento) {
            return false;
        }
        $encriptador = new Encriptador();
        $docEnc = $encriptador->encriptar($rutaDocumento);

        $sets = ["documento = '{$docEnc}'", "fec_creacion = NOW()"];
        if ($this->tablaTieneColumna('actas_const', 'estado_documento')) {
            $sets[] = "estado_documento = 'revision'";
        }
        if ($this->tablaTieneColumna('actas_const', 'id_usuario') && $idUsuario !== null) {
            $sets[] = "id_usuario = " . ((int) $idUsuario);
        }
        if ($this->tablaTieneColumna('actas_const', 'id_usuario_revisor')) {
            $sets[] = 'id_usuario_revisor = NULL';
        }
        if ($this->tablaTieneColumna('actas_const', 'fec_revision')) {
            $sets[] = 'fec_revision = NULL';
        }
        if ($this->tablaTieneColumna('actas_const', 'fec_actualizacion')) {
            $sets[] = 'fec_actualizacion = NOW()';
        }
        $sql = "UPDATE actas_const SET " . implode(', ', $sets) . " WHERE id = '{$id}'";
        $resultado = $this->ejecutar($sql);

        // Obtener datos del documento para actualizar el periodo
        $sqlGet = "SELECT id_empresa, contenido FROM actas_const WHERE id = '{$id}' LIMIT 1";
        $resGet = $this->ejecutar($sqlGet);
        if ($fila = $resGet->fetch_assoc()) {
            $empresaId = $fila['id_empresa'];
            $descripcion = mb_strtolower($fila['contenido']);
            $now = new DateTime();
            $periodoMap = [
                'constancia' => 'periodoConstancia',
                'comprobante' => 'periodoComprobanteDom',
                '32d' => 'periodo32d',
            ];
            $periodoField = null;
            foreach ($periodoMap as $clave => $campo) {
                if (strpos($descripcion, $clave) !== false) {
                    $periodoField = $campo;
                    break;
                }
            }
            if ($periodoField) {
                $periodoValue = $now->modify('+3 months')->format('Y-m-d');
                $this->actualizarPeriodoEmpresa($empresaId, $periodoField, $periodoValue);
            }
        }
        return (bool) $resultado;
    }

    /**
     * Obtiene el id del usuario que subió o reemplazó por última vez un documento permanente.
     * Devuelve null si la columna no existe o si no hay valor registrado.
     */
    public function obtenerIdUsuarioDocumentoPermanente($idDocumento)
    {
        $id = (int) $idDocumento;
        if ($id <= 0) {
            return null;
        }
        // Si la tabla no tiene la columna, no hay nada que validar
        if (!$this->tablaTieneColumna('actas_const', 'id_usuario')) {
            return null;
        }
        $sql = "SELECT id_usuario FROM actas_const WHERE id = '{$id}' LIMIT 1";
        $resultado = $this->ejecutar($sql);
        if ($resultado) {
            $fila = mysqli_fetch_assoc($resultado);
            if ($fila && isset($fila['id_usuario']) && $fila['id_usuario'] !== null) {
                return (int) $fila['id_usuario'];
            }
        }
        return null;
    }

    public function dameActasConstitutivasPorEmpresa($empresa, $clave)
    {
        $encriptador = new Encriptador();

        $sql = "SELECT * FROM actas_const WHERE id_empresa = '$empresa' and contenido like '%ActaCost%' order by fec_creacion desc limit 1";
        $resultado = $this->ejecutar($sql);
        $actas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $actas[] = new ActasConstitutivas($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento'], $clave), $fila['fec_creacion']);
        }
        return $actas;
    }

    public function dameDocumentoPoderPorEmpresa($empresa, $clave)
    {
        $encriptador = new Encriptador();

        $sql = "SELECT * FROM actas_const WHERE id_empresa = '$empresa' and contenido like '%Poder%' order by fec_creacion desc limit 1";
        $resultado = $this->ejecutar($sql);
        $actas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $actas[] = new ActasConstitutivas($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento'], $clave), $fila['fec_creacion']);
        }
        return $actas;
    }

    public function dameIneRepPorEmpresa($empresa, $clave)
    {
        $encriptador = new Encriptador();

        $sql = "SELECT * FROM actas_const WHERE id_empresa = '$empresa' and contenido like '%IneR%' order by fec_creacion desc limit 1";
        $resultado = $this->ejecutar($sql);
        $actas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $actas[] = new ActasConstitutivas($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento'], $clave), $fila['fec_creacion']);
        }
        return $actas;
    }

    public function dameIneSocioPorEmpresa($empresa, $clave)
    {
        $encriptador = new Encriptador();

        $sql = "SELECT * FROM actas_const WHERE id_empresa = '$empresa' and contenido like '%IneS%' order by fec_creacion desc limit 1";
        $resultado = $this->ejecutar($sql);
        $actas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $actas[] = new ActasConstitutivas($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento'], $clave), $fila['fec_creacion']);
        }
        return $actas;
    }

    public function dameInesSocioPorEmpresa($empresa)
    {
        $encriptador = new Encriptador();

        $sql = "SELECT * FROM actas_const WHERE id_empresa = '$empresa' and contenido like '%IneS%' order by fec_creacion desc";
        $resultado = $this->ejecutar($sql);
        $actas = array();

        while ($fila = mysqli_fetch_array($resultado)) {
            $actas[] = new ActasConstitutivas($fila['id'], $fila['id_empresa'], $encriptador->desencriptar($fila['documento']), $fila['fec_creacion']);
        }
        return $actas;
    }

    public function dameConsSocioPorEmpresa($empresa, $clave)
    {
        $encriptador = new Encriptador();

        $sql = "SELECT * FROM actas_const WHERE id_empresa = '$empresa' and contenido like '%CoSo%' order by fec_creacion desc limit 1";
        $resultado = $this->ejecutar($sql);
        $actas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $actas[] = new ActasConstitutivas($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento'], $clave), $fila['fec_creacion']);
        }
        return $actas;
    }

    public function dameConsSociosPorEmpresa($empresa)
    {
        $encriptador = new Encriptador();

        $sql = "SELECT * FROM actas_const WHERE id_empresa = '$empresa' and contenido like '%CoSo%' order by fec_creacion desc";
        $resultado = $this->ejecutar($sql);
        $actas = array();

        while ($fila = mysqli_fetch_array($resultado)) {
            $actas[] = new ActasConstitutivas($fila['id'], $fila['id_empresa'], $encriptador->desencriptar($fila['documento']), $fila['fec_creacion']);
        }
        return $actas;
    }

    public function dameConsRepPorEmpresa($empresa, $clave)
    {
        $encriptador = new Encriptador();

        $sql = "SELECT * FROM actas_const WHERE id_empresa = '$empresa' and contenido like '%CoRe%' order by fec_creacion desc limit 1";
        $resultado = $this->ejecutar($sql);
        $actas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $actas[] = new ActasConstitutivas($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento'], $clave), $fila['fec_creacion']);
        }
        return $actas;
    }

    public function dameDocumentosPPorEmpresa($empresa, $clave)
    {
        $encriptador = new Encriptador();

        $sql = "SELECT * FROM actas_const WHERE id_empresa = '$empresa' order by fec_creacion desc";
        $resultado = $this->ejecutar($sql);
        $actas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $estadoDocumento = isset($fila['estado_documento']) ? $fila['estado_documento'] : (isset($fila['estadoDocumento']) ? $fila['estadoDocumento'] : null);
            $estadoDocumento = $this->normalizarEstadoDocumento($estadoDocumento);
            $acta = new ActasConstitutivas($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento'], $clave), $fila['fec_creacion'], $estadoDocumento);
            $acta->contenido = $fila['contenido'];
            $acta->fechaRevision = $fila['fec_revision']
                ?? $fila['fecha_revision']
                ?? $fila['fechaRevision']
                ?? null;
            $acta->fechaActualizacion = $fila['fec_actualizacion']
                ?? $fila['fecha_actualizacion']
                ?? $fila['fechaActualizacion']
                ?? null;
            // Agregar observaciones si existe la columna
            $acta->observaciones = isset($fila['observaciones']) ? $fila['observaciones'] : null;
            $actas[] = $acta;
        }
        return $actas;
    }

    public function dameRppcPorEmpresa($empresa, $clave)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT * FROM actas_const WHERE id_empresa = '$empresa' and contenido like '%Rppd%' order by fec_creacion desc limit 1";
        $resultado = $this->ejecutar($sql);
        $actas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $actas[] = new ActasConstitutivas($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento'], $clave), $fila['fec_creacion']);
        }
        return $actas;
    }

    public function dameAsambleaPorEmpresa($empresa, $clave)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT * FROM actas_const WHERE id_empresa = '$empresa' and contenido like '%Asamblea%' order by fec_creacion desc limit 1";
        $resultado = $this->ejecutar($sql);
        $actas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $actas[] = new ActasConstitutivas($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento'], $clave), $fila['fec_creacion']);
        }
        return $actas;
    }

    public function dameActasConstitutivas($clave)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT act.id, act.documento, act.fec_creacion, act.id_empresa, emp.razon, act.contenido FROM actas_const act 
        inner join 
        empresas emp 
        on emp.id = act.id_empresa where act.contenido like '%ActaCost%'";
        $resultado = $this->ejecutar($sql);
        $actas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $acta = new ActasConstitutivas($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento'], $clave), $fila['fec_creacion']);
            $acta->empresaNom = $fila['razon'];
            $acta->contenido = $fila['contenido'];
            $actas[] = $acta;
        }
        return $actas;
    }

    public function dameRppd($clave)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT act.id, act.documento, act.fec_creacion, act.id_empresa, emp.razon , act.contenido FROM actas_const act 
        inner join 
        empresas emp 
        on emp.id = act.id_empresa where act.contenido like '%Rppd%'";
        $resultado = $this->ejecutar($sql);
        $actas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $acta = new ActasConstitutivas($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento'], $clave), $fila['fec_creacion']);
            $acta->empresaNom = $fila['razon'];
            $acta->contenido = $fila['contenido'];
            $actas[] = $acta;
        }
        return $actas;
    }

    public function dameAsamblea($clave)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT act.id, act.documento, act.fec_creacion, act.id_empresa, emp.razon , act.contenido FROM actas_const act 
        inner join 
        empresas emp 
        on emp.id = act.id_empresa where act.contenido like '%Asamblea%'";
        $resultado = $this->ejecutar($sql);
        $actas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $acta = new ActasConstitutivas($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento'], $clave), $fila['fec_creacion']);
            $acta->empresaNom = $fila['razon'];
            $acta->contenido = $fila['contenido'];
            $actas[] = $acta;
        }
        return $actas;
    }


    public function agregarFiel($empresa, $documento1, $tipo)
    {
        $encriptador = new Encriptador();
        $documento1 = $encriptador->encriptar($documento1);
        $sql = "INSERT INTO fiel (id_empresa, documento_1, tipo, fec_creacion) 
        VALUES ('$empresa', '$documento1', '$tipo', now())";
        $this->ejecutar($sql);
    }

    public function agregaContraFiel($empresa, $contrasena)
    {
        $encriptador = new Encriptador();
        $contrasena = $encriptador->encriptar($contrasena);
        $sql = "INSERT INTO `contras_fiel`(`contrasena`, `id_empresa`,`fecha_inserta` ) VALUES ('$contrasena','$empresa',now())";
        $this->ejecutar($sql);
    }

    public function dameContraFiel($empresa)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT * FROM contras_fiel WHERE id_empresa = '$empresa' order by id desc limit 1";
        $resultado = $this->ejecutar($sql);
        $contrasena = "";
        while ($fila = mysqli_fetch_array($resultado)) {
            $contrasena = $encriptador->desencriptar($fila['contrasena']);
        }
        return $contrasena;
    }

    public function agregarSelloSat($empresa, $documento1, $tipo)
    {
        $encriptador = new Encriptador();
        $documento1 = $encriptador->encriptar($documento1);
        $sql = "INSERT INTO sellos_sat (id_empresa, documento_1, tipo, fec_creacion) 
        VALUES ('$empresa', '$documento1', '$tipo', now() )";
        $this->ejecutar($sql);
    }

    public function eliminarSellosSat($id)
    {
        $sql = "DELETE FROM sellos_sat WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function agregaContraSelloSat($contra, $empresa)
    {
        $encriptador = new Encriptador();
        $contra = $encriptador->encriptar($contra);
        $sql = "INSERT INTO `contras_sellos`(`contrasena`, `id_empresa`, `fecha_inserta`) VALUES ('$contra','$empresa', now() )";
        $this->ejecutar($sql);
    }

    public function dameContraSelloSat($emrpesa)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT * FROM contras_sellos WHERE id_empresa = '$emrpesa' order by id desc limit 1";
        $resultado = $this->ejecutar($sql);
        $contrasena = "";
        while ($fila = mysqli_fetch_array($resultado)) {
            $contrasena = $encriptador->desencriptar($fila['contrasena']);
        }
        return $contrasena;
    }

    public function dameSellosSat($clave)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT sel.id, sel.documento_1, sel.fec_creacion, sel.id_empresa, emp.razon, sel.tipo FROM sellos_sat sel 
        inner join 
        empresas emp 
        on emp.id = sel.id_empresa order by emp.razon";
        $resultado = $this->ejecutar($sql);
        $sellos = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $sello = new Sello_sat($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento_1'], $clave), $fila['tipo'], $fila['fec_creacion']);
            $sello->empresaNom = $fila['razon'];
            $sellos[] = $sello;
        }
        return $sellos;
    }

    public function dameSelosSatPorEmpresa($empresa, $clave)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT * FROM sellos_sat WHERE id_empresa = '$empresa'";
        $resultado = $this->ejecutar($sql);
        $sellos = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $sellos[] = new Sello_sat($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento_1'], $clave), $fila['tipo'], $fila['fec_creacion']);
        }
        return $sellos;
    }

    public function eliminarFiel($id)
    {
        $sql = "DELETE FROM fiel WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function dameFielPorEmpresa($empresa, $clave)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT * FROM fiel WHERE id_empresa = '$empresa'";
        $resultado = $this->ejecutar($sql);
        $fiel = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $fiel[] = new Fiel($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento_1'], $clave), $fila['tipo'], $fila['fec_creacion']);
        }
        return $fiel;
    }

    public function dameFiels($clave)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT fiel.id, fiel.documento_1, fiel.fec_creacion, fiel.id_empresa, emp.razon, fiel.tipo FROM fiel fiel 
        inner join 
        empresas emp 
        on emp.id = fiel.id_empresa order by emp.razon ";
        $resultado = $this->ejecutar($sql);
        $fiels = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $fiel = new Fiel($fila['id'],  $fila['id_empresa'], $encriptador->desencriptar($fila['documento_1'], $clave), $fila['tipo'], $fila['fec_creacion']);
            $fiel->empresaNom = $fila['razon'];
            $fiels[] = $fiel;
        }
        return $fiels;
    }

    public function agregarEmpresa($razon, $rfc, $calle, $numero, $colonia, $cp, $estado, $logo, $regimen, $passwordR, $usuario, $pdf, $inicioDominio, $finDominio, $prioridad, $telefono, $correo, $descripcion, $sitioWeb)
    {
        $sql = "INSERT INTO empresas (razon, rfc, calle, numero, colonia, cp, estado, logo, regimen, password_r, usuario, opinion_de_cumplimiento, inicio_dominio, fin_dominio, prioridad, telefono, correo, descripcion, sitio_web) 
        VALUES ('$razon', '$rfc', '$calle', '$numero', '$colonia', '$cp', '$estado', '$logo', '$regimen', '$passwordR', '$usuario', '$pdf', '$inicioDominio', '$finDominio', '$prioridad', '$telefono', '$correo', '$descripcion', '$sitioWeb')";
        $this->ejecutar($sql);
    }

    public function agregarContraSat($empresa, $usuario, $contrasena, $rfc)
    {

        $encriptador = new Encriptador();
        $contrasena = $encriptador->encriptar($contrasena);
        $rfc = $encriptador->encriptar($rfc);
        $sql = "INSERT INTO contras_sat (id_empresa, usuario, contrasena, rfc, fec_creacion, fec_actualisacion) 
    VALUES ('$empresa', '$usuario', '$contrasena', '$rfc', now(), now() )";
        $this->ejecutar($sql);
    }


    public function agregaContraIofacturo($rfc, $contrasena, $usuario, $empresa)
    {
        $encriptador = new Encriptador();
        $contrasena = $encriptador->encriptar($contrasena);
        $rfc = $encriptador->encriptar($rfc);
        $sql = "INSERT INTO contras_iofac (rfc, contrasena, usuario, id_empresa, fec_creacion, fec_actualizacion) 
        VALUES ('$rfc', '$contrasena', '$usuario', '$empresa', now(), now())";
        $this->ejecutar($sql);
    }

    public function agregarContraBanco($usuario, $contrasena, $claveOp, $nip, $empresa, $banco)
    {
        $encriptador = new Encriptador();
        $contrasena = $encriptador->encriptar($contrasena);
        $claveOp = $encriptador->encriptar($claveOp);
        $nip = $encriptador->encriptar($nip);
        $sql = "INSERT INTO contras_banco (usuario, contrasena, clave_op, nip, id_empresa, banco, fec_creacion, fec_actualizacion) 
        VALUES ('$usuario', '$contrasena', '$claveOp', '$nip', '$empresa', '$banco', now(), now())";
        $this->ejecutar($sql);
    }

    public function agregarRppc($rppcDoc, $empresa)
    {
        $sql = "INSERT INTO rppc (rppc_doc, id_empresa, fec_actualizacion, fec_creacion) 
        VALUES ('$rppcDoc', '$empresa', now(), now() )";
        $this->ejecutar($sql);
    }

    public function dameCuentasIofacturo($empresa, $clave)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT * FROM contras_iofac WHERE id_empresa = '$empresa'";
        $resultado = $this->ejecutar($sql);
        $cuentas = array();
        while ($fila = $resultado->fetch_assoc()) {
            $cuentas[] = new contrasIofacturo($encriptador->desencriptar($fila['rfc'], $clave), $fila['id'], $encriptador->desencriptar($fila['contrasena'], $clave), $fila['usuario']);
        }
        return $cuentas;
    }

    public function dameCuentasIofact($clave)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT iof.id, iof.rfc, iof.contrasena, iof.usuario, emp.razon FROM contras_iofac iof inner join empresas emp on emp.id = iof.id_empresa";
        $resultado = $this->ejecutar($sql);
        $cuentas = array();
        while ($fila = $resultado->fetch_assoc()) {
            $cuenta = new contrasIofacturo($encriptador->desencriptar($fila['rfc'], $clave), $fila['id'], $encriptador->desencriptar($fila['contrasena'], $clave), $fila['usuario']);
            $cuenta->empresa = $fila['razon'];
            $cuentas[] = $cuenta;
        }
        return $cuentas;
    }

    public function dameContrasBanco($empresa, $clave)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT * FROM contras_banco WHERE id_empresa = '$empresa'";
        $resultado = $this->ejecutar($sql);
        $cuentas = array();
        while ($fila = $resultado->fetch_assoc()) {
            $cuentas[] = new contrasBanco($fila['id'], $fila['usuario'], $encriptador->desencriptar($fila['contrasena'], $clave), $encriptador->desencriptar($fila['clave_op'], $clave), $encriptador->desencriptar($fila['nip'], $clave), $fila['banco']);
        }
        return $cuentas;
    }



    public function eliminarContraBanco($id)
    {
        $sql = "DELETE FROM contras_banco WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function eliminarContasIofacturo($id)
    {
        $sql = "DELETE FROM contras_iofac WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function eliminarRppc($id)
    {
        $sql = "DELETE FROM rppc WHERE id = '$id'";
        $this->ejecutar($sql);
    }


    public function eliminarContraSat($id)
    {
        $sql = "DELETE FROM contras_sat WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function dameContrasSatPorEmpresa($empresa, $clave)
    {
        $encriptador = new Encriptador();

        $sql = "SELECT * FROM contras_sat WHERE id_empresa = '$empresa'";
        $resultado = $this->ejecutar($sql);
        $contraSat = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $contra = new contraSat($fila['id_empresa'], $fila['id'], $fila['usuario'], $encriptador->desencriptar($fila['contrasena'], $clave), $encriptador->desencriptar($fila['rfc'], $clave));
            array_push($contraSat, $contra);
        }
        return $contraSat;
    }

    public function dameContrasSat($clave)
    {
        $encriptador = new Encriptador();

        $sql = "SELECT c.id_empresa, c.id, c.usuario, c.contrasena, c.rfc, e.razon FROM contras_sat c inner join empresas e on e.id = c.id_empresa order by e.razon asc";
        $resultado = $this->ejecutar($sql);
        $contraSat = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $contra = new contraSat($fila['id_empresa'], $fila['id'], $fila['usuario'], $encriptador->desencriptar($fila['contrasena'], $clave), $encriptador->desencriptar($fila['rfc'], $clave));
            $contra->nombreEmpresa = $fila['razon'];
            array_push($contraSat, $contra);
        }
        return $contraSat;
    }

    public function agregarCuenta($banco, $cuenta, $clave, $empresa, $estatus, $moneda)
    {
        $sql = "INSERT INTO cuentas_banco (banco, cuenta, clave, empresa, estatus, moneda) 
        VALUES ('$banco', '$cuenta', '$clave', '$empresa', '$estatus', '$moneda')";
        $this->ejecutar($sql);
    }

    public function eliminarCuentaBanco($id)
    {
        $sql = "DELETE FROM cuentas_banco WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function dameCuentasBanco()
    {
        $sql = "SELECT * FROM cuentas_banco inner join empresas on empresas.razon = cuentas_banco.empresa order by empresa asc ";
        $resultado = $this->ejecutar($sql);
        $cuentas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $cuenta = new Cuenta($fila['id'], $fila['banco'], $fila['cuenta'], $fila['clave'], $fila['empresa'], $fila['estatus'], $fila['moneda']);
            array_push($cuentas, $cuenta);
        }
        return $cuentas;
    }

    public function dameContrasenasBanco($clave)
    {
        $encriptador = new Encriptador();
        $sql = "SELECT contras_banco.id, contras_banco.usuario, contrasena, clave_op, nip, banco, empresas.razon FROM contras_banco left join empresas on contras_banco.id_empresa = empresas.id order by empresas.razon asc";
        $resultado = $this->ejecutar($sql);
        $cuentas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $cuenta = new contrasBanco($fila['id'], $fila['usuario'], $encriptador->desencriptar($fila['contrasena'], $clave), $encriptador->desencriptar($fila['clave_op'], $clave), $encriptador->desencriptar($fila['nip'], $clave), $fila['banco']);
            $cuenta->empresa = $fila['razon'];
            array_push($cuentas, $cuenta);
        }
        return $cuentas;
    }

    public function dameCuentaPorEmpresa($razon)
    {
        $sql = "SELECT * FROM cuentas_banco WHERE empresa = '$razon' ORDER BY estatus DESC";
        $resultado = $this->ejecutar($sql);
        $cuentas = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            $cuenta = new Cuenta($fila['id'], $fila['banco'], $fila['cuenta'], $fila['clave'], $fila['empresa'], $fila['estatus'], $fila['moneda']);
            array_push($cuentas, $cuenta);
        }
        return $cuentas;
    }

    public function dameBancoPorEmpresa($razon)
    {
        $sql = "SELECT banco FROM cuentas_banco WHERE empresa = '$razon'";
        // mostrar solo los bancos de la empresa
        $resultado = $this->ejecutar($sql);
        $bancos = array();
        while ($fila = mysqli_fetch_array($resultado)) {
            array_push($bancos, $fila['banco']);
        }
        return $bancos;
    }

    public function agregarCorreo($correo, $empresa)
    {
        $sql = "INSERT INTO correos (correo, empresa, fecha) 
        VALUES ('$correo', '$empresa', now() )";
        $this->ejecutar($sql);
    }

    public function eliminarCorreo($id)
    {
        $sql = "DELETE FROM correos WHERE id = '$id'";
        $this->ejecutar($sql);
    }
    public function modificarEmpresa($id, $razon, $rfc, $calle, $numero, $colonia, $cp, $estado, $regimen, $passwordR, $usuario, $inicioDominio, $finDominio, $prioridad, $telefono, $correo, $descripcion, $sitioWeb)
    {
        $sql = "UPDATE empresas SET razon = '$razon', rfc = '$rfc', calle = '$calle', numero = '$numero', colonia = '$colonia', cp = '$cp', estado = '$estado', regimen = '$regimen', password_r = '$passwordR', usuario = '$usuario', inicio_dominio = '$inicioDominio', fin_dominio = '$finDominio', prioridad = '$prioridad', telefono = '$telefono', correo = '$correo', descripcion = '$descripcion', sitio_web = '$sitioWeb' WHERE id = '$id'";
        $this->ejecutar($sql);
    }
    public function modificarPdf($id, $pdf, $fecha)
    {
        $sql = "UPDATE empresas SET opinion_de_cumplimiento = '$pdf' , ultimo_pdf = '$fecha', periodo_32d = '" . date('Y-m-d', strtotime('+3 months')) . "' WHERE id = '$id'";
        error_log("SQL modificarPdf: " . $sql);
        $resultado = $this->ejecutar($sql);
        error_log("Resultado ejecutar: " . ($resultado ? "true" : "false"));
        return $resultado;
    }

    public function modificarComprobanteDomicilio($id, $comprobanteDom)
    {
        $sql = "UPDATE empresas SET comprobante_dom = '$comprobanteDom', periodo_comprobanteDom = '" . date('Y-m-d', strtotime('+3 months')) . "' WHERE id = '$id'";
        return $this->ejecutar($sql);
    }
    public function modificarImagen($id, $logo)
    {
        $sql = "UPDATE empresas SET logo = '$logo' WHERE id = '$id'";
        $this->ejecutar($sql);
    }
    public function eliminarEmpresa($id)
    {
        $sql = "DELETE FROM empresas WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function modificarFrecuencia($id, $frecuenciaTelefono, $frecuenciaCorreo, $frecuenciaDireccion, $frecuenciaWeb)
    {
        $sql = "UPDATE empresas SET periodo_telefono = '$frecuenciaTelefono', periodo_sitiow = '$frecuenciaWeb', periodo_correo = '$frecuenciaCorreo', periodo_direccion = '$frecuenciaDireccion' WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function modificarConstancia($id, $constancia, $fecha)
    {
        // Obtener el regimen de la empresa y normalizarlo
        $sql = "UPDATE empresas SET constancia_sf = '$constancia', ultima_constancia = '$fecha', periodo_constancia = '" . date('Y-m-d', strtotime('+3 months')) . "' WHERE id = '$id'";
        error_log("[modificarConstancia] Ejecutando SQL: $sql");
        $resultado = $this->ejecutar($sql);
        error_log("[modificarConstancia] Resultado ejecutar: " . ($resultado ? "true" : "false"));
        return $resultado;
    }

    public function dameEmpreasConPdfAtrasado()
    {
        $mesActual = date("m");
        $anioActual = date("Y");

        $sql = "SELECT * FROM `empresas` WHERE MONTH(ultimo_pdf)< $mesActual and YEAR(ultimo_pdf) <= $anioActual";
        $resultado = $this->ejecutar($sql);
        $empresas = array();
        while ($fila = $resultado->fetch_assoc()) {
            $empresas[] = new Empresa($fila['id'], $fila['razon'], $fila['rfc'], $fila['calle'], $fila['numero'], $fila['colonia'], $fila['cp'], $fila['estado'], $fila['logo'], $fila['regimen'], $fila['password_r'], $fila['usuario'], $fila['opinion_de_cumplimiento'], $fila['inicio_dominio'], $fila['fin_dominio'], $fila['prioridad'], $fila['telefono'], $fila['correo'], $fila['descripcion'], $fila['sitio_web']);
        }
        return $empresas;
    }

    public function dameEmpresa($id)
    {
        $sql = "SELECT * FROM empresas WHERE id = '$id'";
        $resultado = $this->ejecutar($sql);
        $fila = $resultado->fetch_assoc();
        $empresa = new Empresa($fila['id'], $fila['razon'], $fila['rfc'], $fila['calle'], $fila['numero'], $fila['colonia'], $fila['cp'], $fila['estado'], $fila['logo'], $fila['regimen'], $fila['password_r'], $fila['usuario'], $fila['opinion_de_cumplimiento'], $fila['inicio_dominio'], $fila['fin_dominio'], $fila['prioridad'], $fila['telefono'], $fila['correo'], $fila['descripcion'], $fila['sitio_web']);
        $empresa->periodoTel = $fila['periodo_telefono'];
        $empresa->periodoCorreo = $fila['periodo_correo'];
        $empresa->periodoDireccion = $fila['periodo_direccion'];
        $empresa->periodoWeb = $fila['periodo_sitiow'];
        $empresa->comprobanteDom = $fila['comprobante_dom'];
        $empresa->constanciaSf = $fila['constancia_sf'];
        $empresa->cuentas = $this->dameCuentaPorEmpresa($fila['razon']);
        $empresa->correos = $this->dameCorreosPorEmpresa($id);

        return $empresa;
    }
    public function dameEmpresas()
    {
        // seleccionar todas las empresas ordenadas por razon social
        $sql = "SELECT * FROM empresas ORDER BY razon";
        $resultado = $this->ejecutar($sql);
        $empresas = array();
        while ($fila = $resultado->fetch_assoc()) {
            $empresa = new Empresa($fila['id'], $fila['razon'], $fila['rfc'], $fila['calle'], $fila['numero'], $fila['colonia'], $fila['cp'], $fila['estado'], $fila['logo'], $fila['regimen'], $fila['password_r'], $fila['usuario'], $fila['opinion_de_cumplimiento'], $fila['inicio_dominio'], $fila['fin_dominio'], $fila['prioridad'], $fila['telefono'], $fila['correo'], $fila['descripcion'], $fila['sitio_web']);
            $empresa->periodoTel = $fila['periodo_telefono'];
            $empresa->periodoCorreo = $fila['periodo_correo'];
            $empresa->periodoDireccion = $fila['periodo_direccion'];
            $empresa->comprobanteDom = $fila['comprobante_dom'];
            $empresa->constanciaSf = $fila['constancia_sf'];
            $empresa->ultimoPdf = isset($fila['ultimo_pdf']) ? $fila['ultimo_pdf'] : null;
            $empresa->ultimaConstancia = isset($fila['ultima_constancia']) ? $fila['ultima_constancia'] : null;
            $empresa->periodoComprobanteDom = isset($fila['periodo_comprobanteDom']) ? $fila['periodo_comprobanteDom'] : null;
            $empresa->periodoConstancia = isset($fila['periodo_constancia']) ? $fila['periodo_constancia'] : null;
            $empresa->periodo32d = isset($fila['periodo_32d']) ? $fila['periodo_32d'] : null;
            $empresa->cuentas = $this->dameCuentaPorEmpresa($fila['razon']);
            $empresa->correos = $this->dameCorreosPorEmpresa($fila['id']);
            $empresa->aplicaImss = isset($fila['aplica_imss']) ? (int)$fila['aplica_imss'] : 1;
            $empresas[] = $empresa;
        }
        return $empresas;
    }

    public function dameEmpresasLite()
    {

        // seleccionar todas las empresas ordenadas por razon social
        $sql = "SELECT * FROM empresas ORDER BY razon";
        $resultado = $this->ejecutar($sql);
        $empresas = array();
        while ($fila = $resultado->fetch_assoc()) {
            $empresa = new Empresa($fila['id'], $fila['razon'], $fila['rfc'], $fila['calle'], $fila['numero'], $fila['colonia'], $fila['cp'], $fila['estado'], $fila['logo'], $fila['regimen'], $fila['password_r'], $fila['usuario'], $fila['opinion_de_cumplimiento'], $fila['inicio_dominio'], $fila['fin_dominio'], $fila['prioridad'], $fila['telefono'], $fila['correo'], $fila['descripcion'], $fila['sitio_web']);
            $empresa->ultimoPdf = isset($fila['ultimo_pdf']) ? $fila['ultimo_pdf'] : null;
            $empresa->ultimaConstancia = isset($fila['ultima_constancia']) ? $fila['ultima_constancia'] : null;
            $empresa->periodoComprobanteDom = isset($fila['periodo_comprobanteDom']) ? $fila['periodo_comprobanteDom'] : null;
            $empresa->periodoConstancia = isset($fila['periodo_constancia']) ? $fila['periodo_constancia'] : null;
            $empresa->periodo32d = isset($fila['periodo_32d']) ? $fila['periodo_32d'] : null;
            $empresas[] = $empresa;
        }
        return $empresas;
    }

    public function dameEmpresasPorFecha($fecha)
    {
        $sql = "SELECT * FROM empresas WHERE fin_dominio = '$fecha'";
        $resultado = $this->ejecutar($sql);
        $empresas = array();
        while ($fila = $resultado->fetch_assoc()) {
            $empresa = new Empresa($fila['id'], $fila['razon'], $fila['rfc'], $fila['calle'], $fila['numero'], $fila['colonia'], $fila['cp'], $fila['estado'], $fila['logo'], $fila['regimen'], $fila['password_r'], $fila['usuario'], $fila['opinion_de_cumplimiento'], $fila['inicio_dominio'], $fila['fin_dominio'], $fila['prioridad'], $fila['telefono'], $fila['correo'], $fila['descripcion'], $fila['sitio_web']);
            $empresa->correos = $this->dameCorreosPorEmpresa($fila['id']);
            $empresas[] = $empresa;
        }
        return $empresas;
    }

    public function dameCorreosPorEmpresa($empresa)
    {
        $sql = "SELECT * FROM correos WHERE empresa = '$empresa'";
        $resultado = $this->ejecutar($sql);
        $correos = array();
        while ($fila = $resultado->fetch_assoc()) {

            $correo = new Correo($fila['id'], $fila['correo'], $fila['empresa'], $fila['fecha']);
            $correos[] = $correo;
        }
        return $correos;
    }

    public function damePeriodoDeComprobanteDom($id)
    {
        $sql = "SELECT periodo_comprobanteDom FROM empresas WHERE id = '$id'";
        $resultado = $this->ejecutar($sql);
        $fila = $resultado->fetch_assoc();
        return $fila['periodo_comprobanteDom'];
    }

    public function damePeriodoDeConstanciaSf($id)
    {
        $sql = "SELECT periodo_constancia FROM empresas WHERE id = '$id'";
        $resultado = $this->ejecutar($sql);
        $fila = $resultado->fetch_assoc();
        return $fila['periodo_constancia'];
    }

    public function damePeriodoDe32d($id)
    {
        $sql = "SELECT periodo_32d FROM empresas WHERE id = '$id'";
        $resultado = $this->ejecutar($sql);
        $fila = $resultado->fetch_assoc();
        return $fila['periodo_32d'];
    }

    public function modificarNombre($id, $nombre)
    {
        $sql = "UPDATE empresas SET razon = '$nombre' WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function modificarRfc($id, $rfc)
    {
        $sql = "UPDATE empresas SET rfc = '$rfc' WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function modificarCalle($id, $calle)
    {
        $sql = "UPDATE empresas SET calle = '$calle' WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function modificarNumero($id, $numero)
    {
        $sql = "UPDATE empresas SET numero = '$numero' WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function modificarColonia($id, $colonia)
    {
        $sql = "UPDATE empresas SET colonia = '$colonia' WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function modificarCp($id, $cp)
    {
        $sql = "UPDATE empresas SET cp = '$cp' WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function modificarEstado($id, $estado)
    {
        $sql = "UPDATE empresas SET estado = '$estado' WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function modificarRegimen($id, $regimen)
    {
        $sql = "UPDATE empresas SET regimen = '$regimen' WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function modificarFinDominio($id, $finDominio)
    {
        $sql = "UPDATE empresas SET fin_dominio = '$finDominio' WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function modificarTelefono($id, $telefono)
    {
        $sql = "UPDATE empresas SET telefono = '$telefono' WHERE id = '$id'";
        $this->ejecutar($sql);
        // Actualizar periodo_telefono a 1 año desde hoy
        $fecha = date('Y-m-d', strtotime('+1 year'));
        $sqlPeriodo = "UPDATE empresas SET periodo_telefono = '$fecha' WHERE id = '$id'";
        $this->ejecutar($sqlPeriodo);
    }

    public function modificarCorreo($id, $correo)
    {
        $sql = "UPDATE empresas SET correo = '$correo' WHERE id = '$id'";
        $this->ejecutar($sql);
        // Actualizar periodo_correo a 1 año desde hoy
        $fecha = date('Y-m-d', strtotime('+1 year'));
        $sqlPeriodo = "UPDATE empresas SET periodo_correo = '$fecha' WHERE id = '$id'";
        $this->ejecutar($sqlPeriodo);
    }

    public function modificarDescripcion($id, $descripcion)
    {
        $sql = "UPDATE empresas SET descripcion = '$descripcion' WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function modificarSitioWeb($id, $sitioWeb)
    {
        $sql = "UPDATE empresas SET sitio_web = '$sitioWeb' WHERE id = '$id'";
        $this->ejecutar($sql);
        // Actualizar periodo_sitiow a 1 año desde hoy
        $fecha = date('Y-m-d', strtotime('+1 year'));
        $sqlPeriodo = "UPDATE empresas SET periodo_sitiow = '$fecha' WHERE id = '$id'";
        $this->ejecutar($sqlPeriodo);
    }

    public function modificarFrecuenciaDireccion($id, $frecuencia)
    {
        $sql = "UPDATE empresas SET periodo_direccion = '$frecuencia' WHERE id = '$id'";
        $this->ejecutar($sql);
    }

    public function damePostergacionesPorEmpresas($empresaIds)
    {
        if (empty($empresaIds) || !is_array($empresaIds)) {
            return [];
        }

        $ids = array_unique(array_map('intval', $empresaIds));
        $ids = array_values(array_filter($ids, function ($id) {
            return $id > 0;
        }));

        if (empty($ids)) {
            return [];
        }

        $resultado = [];
        foreach ($ids as $id) {
            $resultado[$id] = [];
        }

        $idList = implode(',', $ids);

        $sqlTotales = "SELECT id_empresa, campo_a_postergar, COUNT(*) AS total FROM campos_postergados WHERE id_empresa IN ($idList) GROUP BY id_empresa, campo_a_postergar";
        $totales = $this->ejecutar($sqlTotales);
        if ($totales) {
            while ($fila = $totales->fetch_assoc()) {
                $empresaId = isset($fila['id_empresa']) ? (int) $fila['id_empresa'] : 0;
                $campo = isset($fila['campo_a_postergar']) ? $fila['campo_a_postergar'] : null;
                if (!$empresaId || !$campo || !isset($resultado[$empresaId])) {
                    continue;
                }
                if (!isset($resultado[$empresaId][$campo])) {
                    $resultado[$empresaId][$campo] = ['total' => 0, 'status' => null];
                }
                $resultado[$empresaId][$campo]['total'] = isset($fila['total']) ? (int) $fila['total'] : 0;
            }
        }

        $sqlUltimoEstatus = "SELECT cp.id_empresa, cp.campo_a_postergar, cp.estatus_postergado FROM campos_postergados cp WHERE cp.id_empresa IN ($idList) AND cp.id = (SELECT cp2.id FROM campos_postergados cp2 WHERE cp2.id_empresa = cp.id_empresa AND cp2.campo_a_postergar = cp.campo_a_postergar ORDER BY cp2.fecha_postergacion DESC, cp2.id DESC LIMIT 1)";
        $estatus = $this->ejecutar($sqlUltimoEstatus);
        if ($estatus) {
            while ($fila = $estatus->fetch_assoc()) {
                $empresaId = isset($fila['id_empresa']) ? (int) $fila['id_empresa'] : 0;
                $campo = isset($fila['campo_a_postergar']) ? $fila['campo_a_postergar'] : null;
                if (!$empresaId || !$campo || !isset($resultado[$empresaId])) {
                    continue;
                }
                if (!isset($resultado[$empresaId][$campo])) {
                    $resultado[$empresaId][$campo] = ['total' => 0, 'status' => null];
                }
                $resultado[$empresaId][$campo]['status'] = isset($fila['estatus_postergado']) ? (int) $fila['estatus_postergado'] : null;
            }
        }

        return $resultado;
    }

    private function aplicarConteoResumen(&$resumen, $section, $sql, $column = 'id_empresa', $dateColumn = null, $urgenciaMeses = null)
    {
        $resultado = $this->ejecutar($sql);
        if (!$resultado) {
            return;
        }

        while ($fila = $resultado->fetch_assoc()) {
            $empresaId = isset($fila[$column]) ? (int) $fila[$column] : 0;
            if (!$empresaId || !isset($resumen[$empresaId])) {
                continue;
            }

            $total = isset($fila['total']) ? (int) $fila['total'] : 0;
            if ($total > 0) {
                $estado = 'completado';
                if ($dateColumn && isset($fila[$dateColumn]) && $fila[$dateColumn] && $urgenciaMeses !== null) {
                    if ($this->fechaExcedeMeses($fila[$dateColumn], $urgenciaMeses)) {
                        $estado = 'urgente';
                    }
                }
                $resumen[$empresaId][$section] = $estado;
            }
        }
    }

    private function fechaExcedeMeses($fechaCadena, $meses)
    {
        if (!$fechaCadena || !$meses) {
            return false;
        }

        if ($fechaCadena === '0000-00-00') {
            return false;
        }

        try {
            $fecha = new \DateTime($fechaCadena);
        } catch (\Exception $e) {
            return false;
        }

        $limite = clone $fecha;
        $limite->modify('+' . (int) $meses . ' months');
        $hoy = new \DateTime('now');
        return $limite <= $hoy;
    }

    public function dameResumenDetalleEmpresas($empresaIds)
    {
        if (empty($empresaIds) || !is_array($empresaIds)) {
            return [];
        }

        $ids = array_unique(array_map('intval', $empresaIds));
        $ids = array_values(array_filter($ids, function ($id) {
            return $id > 0;
        }));

        if (empty($ids)) {
            return [];
        }

        $defaultSections = [
            'cuentas_bancarias' => 'sin_datos',
            'contrasena_sat' => 'sin_datos',
            'sellos_sat' => 'sin_datos',
            'fiel' => 'sin_datos',
            'contrasena_iofacturo' => 'sin_datos',
            'contrasenas_bancos' => 'sin_datos',
            'documentos_permanentes' => 'sin_datos',
            'estados_cuenta' => 'sin_datos',
            'caratulas_bancarias' => 'sin_datos',
            'imss' => 'sin_datos',
        ];

        $resumen = [];
        foreach ($ids as $id) {
            $resumen[$id] = $defaultSections;
        }

        $idList = implode(',', $ids);

        $sqlCuentas = "SELECT e.id AS empresa_id, COUNT(cb.id) AS total FROM empresas e LEFT JOIN cuentas_banco cb ON cb.empresa = e.razon WHERE e.id IN ($idList) GROUP BY e.id";
        $this->aplicarConteoResumen($resumen, 'cuentas_bancarias', $sqlCuentas, 'empresa_id');

        $this->aplicarConteoResumen($resumen, 'contrasena_sat', "SELECT id_empresa, COUNT(*) AS total FROM contras_sat WHERE id_empresa IN ($idList) GROUP BY id_empresa");
        $this->aplicarConteoResumen($resumen, 'sellos_sat', "SELECT id_empresa, COUNT(*) AS total FROM sellos_sat WHERE id_empresa IN ($idList) GROUP BY id_empresa");
        $this->aplicarConteoResumen($resumen, 'fiel', "SELECT id_empresa, COUNT(*) AS total FROM fiel WHERE id_empresa IN ($idList) GROUP BY id_empresa");
        $this->aplicarConteoResumen($resumen, 'contrasena_iofacturo', "SELECT id_empresa, COUNT(*) AS total FROM contras_iofac WHERE id_empresa IN ($idList) GROUP BY id_empresa");
        $this->aplicarConteoResumen($resumen, 'contrasenas_bancos', "SELECT id_empresa, COUNT(*) AS total FROM contras_banco WHERE id_empresa IN ($idList) GROUP BY id_empresa");
        $this->aplicarConteoResumen($resumen, 'documentos_permanentes', "SELECT id_empresa, COUNT(*) AS total FROM actas_const WHERE id_empresa IN ($idList) GROUP BY id_empresa");
        $this->aplicarConteoResumen($resumen, 'estados_cuenta', "SELECT id_empresa, COUNT(*) AS total, MAX(fec_creacion) AS max_date FROM estados_cuenta WHERE id_empresa IN ($idList) GROUP BY id_empresa", 'id_empresa', 'max_date', 1);
        $this->aplicarConteoResumen($resumen, 'caratulas_bancarias', "SELECT id_empresa, COUNT(*) AS total, MAX(fec_creacion) AS max_date FROM caratulas WHERE id_empresa IN ($idList) GROUP BY id_empresa", 'id_empresa', 'max_date', 1);
        $this->aplicarConteoResumen($resumen, 'imss', "SELECT id_empresa, COUNT(*) AS total FROM imss WHERE id_empresa IN ($idList) GROUP BY id_empresa");

        if ($this->tablaTieneColumna('actas_const', 'estado_documento')) {
            $prioridades = [
                'archivo_faltante' => 3,
                'rechazado' => 3,
                'revision' => 2,
                'completado' => 1,
                'sin_datos' => 0,
            ];

            $sqlEstados = "SELECT id_empresa, estado_documento FROM actas_const WHERE id_empresa IN ($idList)";
            $estados = $this->ejecutar($sqlEstados);
            if ($estados instanceof \mysqli_result) {
                while ($fila = $estados->fetch_assoc()) {
                    $empresaId = isset($fila['id_empresa']) ? (int) $fila['id_empresa'] : 0;
                    if (!$empresaId || !isset($resumen[$empresaId])) {
                        continue;
                    }

                    $estadoNormalizado = $this->normalizarEstadoDocumento($fila['estado_documento'] ?? null);
                    $estadoActual = $resumen[$empresaId]['documentos_permanentes'] ?? 'sin_datos';

                    $prioridadActual = $prioridades[$estadoActual] ?? 0;
                    $prioridadNueva = $prioridades[$estadoNormalizado] ?? 0;

                    if ($prioridadNueva > $prioridadActual) {
                        $resumen[$empresaId]['documentos_permanentes'] = $estadoNormalizado;
                    }
                }
            }
        }

        return $resumen;
    }
}
