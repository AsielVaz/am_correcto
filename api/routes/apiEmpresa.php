<?php
include_once('../utils/auth_guard.php');

requireAuth();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include_once('../controllers/adminEmpresas.php');
include_once('../controllers/adminCamposPostergados.php');
// include_once("pdf-reader/PdfToText.phpclass");
include_once('../controllers/adminHistoricoArchivos.php');



$accion = isset($_POST['accion']) ? $_POST['accion'] : null;
if (!$accion) {
    respondWithError(400, 'Acción requerida');
}

$casoAgregar = "agregar";
$casoEliminar = "eliminar";
$casoModificar = "modificar";
$casoModificarPdf = "modificarPdf";
$casoModificarImagen = "modificarImagen";
$casoNuevoCorreo = "correo";
$casoEliminarCorreo = "correoEliminar";
$casoFrecuancias = "frecuencias";
$casoModComprobante = "comprobanteMod";
$casoModConstanciaSf = "constanciaSfMod";
$casoAgregarCuenta = "nuevaCuenta";
$casoEliminarCuenta = "eliminarCuenta";
$casoSubirPdf = "subirPdf";
$casoAgrearContraSat = "agregarContraSat";
$casoEliminarContraSat = "eliminarContraSat";
$casoAgregarCuentaIofact = "agregarCuentaIofact";
$casoEliminarCuentaIofact = "eliminarCuentaIofact";
$casoAgregarContraBanco = "agregarContraBanco";
$casoEliminarContraBanco = "eliminarContraBanco";
$casoAgregarDocumentoFiels = "agregarDocumentoFiel";
$casoEliminarDocumentoFiels = "eliminarDocumentoFiel";
$casoAgregarActaConst = "agregarActaConst";
$casoEliminarActaConst = "eliminarActaConst";
$casoAgregarDocumentoPermanente = "nuevoDocumentoPermanente";
$casoSolicitarDocumentoPermanente = "solicitarDocumentoPermanente";
$casoEliminarDocumentoPermanente = "eliminarDocumentoPermanente";
$casoMarcarUrgenteDocumentoPermanente = "marcarUrgenteDocumentoPermanente";
$casoMarcarArchivoFaltanteDocumentoPermanente = "marcarArchivoFaltanteDocumentoPermanente";
$casoMarcarDocumentoPermanenteRevisado = "marcarDocumentoPermanenteRevisado";
$casoMarcarDocumentoPermanenteRechazado = "marcarDocumentoPermanenteRechazado";
$casoReemplazarDocumentoPermanente = "reemplazarDocumentoPermanente";
$casoAgregarSelloSat = "agregarSelloSat";
$casoEliminarSelloSat = "eliminarSelloSat";
$casoAgregarEstadoDeCuenta = "agregarEstadoDeCuenta";
$casoEliminarEstadoDeCuenta = "eliminarEstadoDeCuenta";
$casoAgregarCaratula = "agregarCaratula";
$casoEliminarCaratula = "eliminarCaratula";
$casoAgregarContraFiel = "agregarContraFiel";
$casoAgregarContraSello = "agregarContraSello";
$casoAgregarImss = "agregarImss";
$casoEliminarImss = "eliminarImss";
$casoAgregarContraImss = "agregarContraImss";


$casoEspModificarNombre = "espModificarNombre";
$casoEspModificarRfc = "espModificarRfc";
$casoEspModificarDireccion = "espModificarDireccion";
$casoEspModificarCodigoPostal = "espModificarCodigoPostal";
$casoEspModificarEstado = "espModificarEstado";
$casoEspModificarRegimen = "espModificarRegimen";
$casoEspModificarFinDominio = "espModificarFinDominio";
$casoEspModificarTelefono = "espModificarTelefono";
$casoEspModificarCorreo = "espModificarCorreo";
$casoEspModificarDescripcion = "espModificarDescripcion";
$casoEspMoificarSitioWeb = "espModificarSitioWeb";

$casoPostergarEmpresa = "postergarEmpresa";

$accionesSoloAdmin = [
    // Agregar aquí acciones exclusivas para administradores cuando sea necesario.
];

$accionesEscritura = [
    $casoAgregar,
    $casoEliminar,
    $casoModificar,
    $casoModificarPdf,
    $casoModificarImagen,
    $casoNuevoCorreo,
    $casoEliminarCorreo,
    $casoFrecuancias,
    $casoModComprobante,
    $casoModConstanciaSf,
    $casoEspModificarNombre,
    $casoEspModificarRfc,
    $casoEspModificarDireccion,
    $casoEspModificarCodigoPostal,
    $casoEspModificarEstado,
    $casoEspModificarRegimen,
    $casoEspModificarFinDominio,
    $casoEspModificarTelefono,
    $casoEspModificarCorreo,
    $casoEspModificarDescripcion,
    $casoEspMoificarSitioWeb,
    $casoAgregarCuenta,
    $casoEliminarCuenta,
    $casoAgrearContraSat,
    $casoEliminarContraSat,
    $casoAgregarCuentaIofact,
    $casoEliminarCuentaIofact,
    $casoAgregarContraBanco,
    $casoEliminarContraBanco,
    $casoAgregarDocumentoFiels,
    $casoEliminarDocumentoFiels,
    $casoAgregarActaConst,
    $casoEliminarActaConst,
    $casoAgregarDocumentoPermanente,
    $casoSolicitarDocumentoPermanente,
    $casoEliminarDocumentoPermanente,
    $casoMarcarUrgenteDocumentoPermanente,
    $casoMarcarArchivoFaltanteDocumentoPermanente,
    $casoMarcarDocumentoPermanenteRevisado,
    $casoMarcarDocumentoPermanenteRechazado,
    $casoReemplazarDocumentoPermanente,
    $casoAgregarSelloSat,
    $casoEliminarSelloSat,
    $casoAgregarEstadoDeCuenta,
    $casoEliminarEstadoDeCuenta,
    $casoAgregarCaratula,
    $casoEliminarCaratula,
    $casoAgregarContraFiel,
    $casoAgregarContraSello,
    $casoAgregarImss,
    $casoEliminarImss,
    $casoAgregarContraImss,
    $casoPostergarEmpresa,
    'nuevaFIEL',
    'eliminarFIEL',
    'eliminarElementoEmpresa'
];

if (in_array($accion, $accionesSoloAdmin, true)) {
    ensureAdmin();
} elseif (in_array($accion, $accionesEscritura, true)) {
    ensureCanWrite();
}

class Aviso
{
    public $estatus;
    public $mensaje;
    public $subMensaje;
	public $id;

    public function __construct()
    {
        $this->estatus = "";
        $this->mensaje = "";
        $this->subMensaje = "";
		$this->id = null;
    }
}

function procesarImagen($id)
{

    $carpetaDestino = '/Imagenes/Empresas/';
    $pesoMaxicoImagen = 2000000;
    $nombreCompuestoImagen = "default.txt";
    $nombre_imagen = basename($_FILES['archivo']['name']);
    $tipo_Imgaen = $_FILES['archivo']['type'];
    $tamanio_imagen = $_FILES['archivo']['size'];

    $casoPng = ".png";
    $casoJpeg = ".jpg";
    //comprovadores de peso y tipo de imagen

    if ($tamanio_imagen < $pesoMaxicoImagen) {
        if ($tipo_Imgaen == "image/jpeg" || $tipo_Imgaen == "image/png") {
            //mueve la imagen a la carpeta seleccionada
            move_uploaded_file($_FILES['archivo']['tmp_name'], APP_ROOT . $carpetaDestino . $nombre_imagen);
            chmod(appFilesystemPath($carpetaDestino . $nombre_imagen), 0640);
            switch ($tipo_Imgaen) {
                case "image/jpeg":
                    $nombreCompuestoImagen = $carpetaDestino . "Imagen" . $id . $casoJpeg;
                    break;
                case "image/png":
                    $nombreCompuestoImagen = $carpetaDestino . "Imagen" . $id .  $casoPng;
                    break;
            }
            rename(APP_ROOT . $carpetaDestino . $nombre_imagen, APP_ROOT . $nombreCompuestoImagen);
            chmod(APP_ROOT . $nombreCompuestoImagen, 0640);
      
            return $nombreCompuestoImagen;
        } else {
            echo json_encode("El formato de la imagen no esta permitido");
        }
    } else {
        echo json_encode("La imagen supera el tamaño establecido");
    }
}

function procesarPdf($id)
{
    $carpetaDestino = APP_ROOT . '/Documentos/Pdf/';
    $nombre_imagen = basename($_FILES['pdf']['name']);
    $tipo_imagen = $_FILES['pdf']['type'];
    // Verificar si el archivo se subió correctamente
    if (!is_uploaded_file($_FILES['pdf']['tmp_name'])) {
        die("Error: El archivo no se subió correctamente.");
    }
    // Mover el archivo a la carpeta destino
    $rutaTemporal = $_FILES['pdf']['tmp_name'];
    $rutaDestino = $carpetaDestino . $nombre_imagen;
    if (!move_uploaded_file($rutaTemporal, $rutaDestino)) {
        die("Error: No se pudo mover el archivo a la carpeta destino.");
    }
    // Renombrar el archivo
    $nombreCompuestoImagen = $carpetaDestino . "Documento-" . $id . "-" . date("Y-m-d-H-i-s") . ".pdf";
    if (!rename($rutaDestino, $nombreCompuestoImagen)) {
        die("Error: No se pudo renombrar el archivo.");
    }
    chmod($nombreCompuestoImagen, 0640);
    // Retornar ruta relativa para la base de datos
    return str_replace(APP_ROOT, '', $nombreCompuestoImagen);
}





function procesarConstanciaSf($id)
{
    $carpetaDestino = APP_ROOT . '/Documentos/Constancias/';
    $nombre_imagen = basename($_FILES['CSF']['name']);
    $tipo_imagen = $_FILES['CSF']['type'];
    if (!is_uploaded_file($_FILES['CSF']['tmp_name'])) {
        die("Error: El archivo no se subió correctamente.");
    }
    $rutaTemporal = $_FILES['CSF']['tmp_name'];
    $rutaDestino = $carpetaDestino . $nombre_imagen;
    if (!move_uploaded_file($rutaTemporal, $rutaDestino)) {
        die("Error: No se pudo mover el archivo a la carpeta destino.");
    }
    $nombreCompuestoImagen = $carpetaDestino . "Documento-" . $id . "-" . date("Y-m-d-H-i-s") . ".pdf";
    if (!rename($rutaDestino, $nombreCompuestoImagen)) {
        die("Error: No se pudo renombrar el archivo.");
    }
    chmod($nombreCompuestoImagen, 0640);
    return str_replace(APP_ROOT, '', $nombreCompuestoImagen);
}


function procesarConstanciaPfx($id)
{
    $carpetaDestino = '/Documentos/Constancias/';
    $pesoMaxicoImagen = 2000000;
    $nombreCompuestoImagen = "default.txt";
    $nombre_imagen = basename($_FILES['archivo']['name']);
    $tipo_Imgaen = $_FILES['archivo']['type'];
    $tamanio_imagen = $_FILES['archivo']['size'];
    $randomNum = rand(1, 90000);
    //echo $nombre_imagen;

    $casoPng = ".png";
    $casoJpeg = ".jpg";
    //comprovadores de peso y tipo de imagen
    if ($tamanio_imagen < $pesoMaxicoImagen) {

        //mueve la imagen a la carpeta seleccionada
        move_uploaded_file($_FILES['archivo']['tmp_name'], APP_ROOT . $carpetaDestino . $nombre_imagen);
        chmod(appFilesystemPath($carpetaDestino . $nombre_imagen), 0640);
        $nombreCompuestoImagen = $carpetaDestino . "Documento-" . $id . "-" . $randomNum . ".pfx";
        rename(APP_ROOT . $carpetaDestino . $nombre_imagen, APP_ROOT . $nombreCompuestoImagen);
        chmod(APP_ROOT . $nombreCompuestoImagen, 0640);
          return $nombreCompuestoImagen;
    } else {
        echo json_encode("La imagen supera el tamaño establecido");
    }
}

function procesarArchivo($id)
{
    $carpetaDestino = APP_ROOT . '/Documentos/Comprobantes/';
    $nombre_imagen = basename($_FILES['comprobante']['name']);
    $tipo_imagen = $_FILES['comprobante']['type'];
    //$tamanio_imagen = $_FILES['comprobante']['size'];

    // Verificar si el archivo se subió correctamente
    if (!is_uploaded_file($_FILES['comprobante']['tmp_name'])) {
        die("Error: El archivo no se subió correctamente.");
    }

    // Mover el archivo a la carpeta destino
    $rutaTemporal = $_FILES['comprobante']['tmp_name'];
    $rutaDestino = $carpetaDestino . $nombre_imagen;

    if (!move_uploaded_file($rutaTemporal, $rutaDestino)) {
        die("Error: No se pudo mover el archivo a la carpeta destino.");
    }

    // Renombrar el archivo
    $nombreCompuestoImagen = $carpetaDestino . "Documento-" . $id . "-" . date("Y-m-d-H-i-s") . "." . procesarFormatos($tipo_imagen);

    if (!rename($rutaDestino, $nombreCompuestoImagen)) {
        die("Error: No se pudo renombrar el archivo.");
    }

    // Dar permisos de lectura y escritura
    chmod($nombreCompuestoImagen, 0640);

    // Retornar ruta relativa para la base de datos
    return str_replace(APP_ROOT, '', $nombreCompuestoImagen);
}


function procesarActas($id)
{
    $carpetaDestino = '/Documentos/Actas/';
    $pesoMaxicoImagen = 2000000000;
    $nombreCompuestoImagen = "default.txt";
    $nombre_imagen = basename($_FILES['acta']['name']);
    $tipo_Imgaen = $_FILES['acta']['type'];
    $tamanio_imagen = $_FILES['acta']['size'];
    //echo $nombre_imagen;

    $casoPng = ".png";
    $casoJpeg = ".jpg";
    //comprovadores de peso y tipo de imagen

    if ($tamanio_imagen < $pesoMaxicoImagen) {

        //mueve la imagen a la carpeta seleccionada
        move_uploaded_file($_FILES['acta']['tmp_name'], APP_ROOT . $carpetaDestino . $nombre_imagen);
        // dar permisos de lectura y escritura
        chmod(appFilesystemPath($carpetaDestino . $nombre_imagen), 0640);
        // renombrar el archivo con la id del usuario, la fecha y hora actual y el tipo de archivo
        $nombreCompuestoImagen = $carpetaDestino . "Documento-" . $id . "-" . date("Y-m-d-H-i-s") . "." . procesarFormatos($tipo_Imgaen);
        // rename renombra el archivo
        rename(APP_ROOT . $carpetaDestino . $nombre_imagen, APP_ROOT . $nombreCompuestoImagen);
        // dar permisos de lectura y escritura
        chmod(APP_ROOT . $nombreCompuestoImagen, 0640);
        // unlink elimina el archivo
  
        return $nombreCompuestoImagen;
    } else {
        return "0";
    }
}


function procesarDocumentoPermanente($id)
{
	if (!isset($_FILES['documento']) || !is_array($_FILES['documento'])) {
        return "0";
    }

	$archivo = $_FILES['documento'];
	if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
		return "0";
	}

	$tamanoArchivo = (int) ($archivo['size'] ?? 0);
	$limiteBytes = max(1, (int) env('DOCUMENT_MAX_UPLOAD_MB', 25)) * 1024 * 1024;
	$tmp = (string) ($archivo['tmp_name'] ?? '');
	if ($tamanoArchivo <= 0 || $tamanoArchivo > $limiteBytes || !is_uploaded_file($tmp)) {
		return "0";
	}

	$finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : false;
	$mime = $finfo
		? (string) finfo_file($finfo, $tmp)
		: (function_exists('mime_content_type') ? (string) mime_content_type($tmp) : '');
	if ($finfo) {
		finfo_close($finfo);
	}
	$extensionesPermitidas = [
		'application/pdf' => 'pdf',
		'image/jpeg' => 'jpg',
		'image/png' => 'png',
	];
	if (!isset($extensionesPermitidas[$mime])) {
		return "0";
	}

	$carpetaRelativa = '/Documentos/DocumentosPermanentes/';
	$carpetaDestino = appFilesystemPath($carpetaRelativa);
	if (!is_dir($carpetaDestino) && !@mkdir($carpetaDestino, 0750, true) && !is_dir($carpetaDestino)) {
		return "0";
	}

	try {
		$token = bin2hex(random_bytes(16));
	} catch (Throwable $e) {
		$token = hash('sha256', uniqid((string) $id, true));
	}
	$nombreSeguro = 'documento-' . date('Ymd-His') . '-' . $token . '.' . $extensionesPermitidas[$mime];
	$rutaRelativa = $carpetaRelativa . $nombreSeguro;
	$rutaDestino = appFilesystemPath($rutaRelativa);

	if (!move_uploaded_file($tmp, $rutaDestino)) {
		return "0";
	}
	@chmod($rutaDestino, 0640);
	return $rutaRelativa;
}




function procesarEstadosCuenta($id)
{
    $carpetaDestino = APP_ROOT . '/Documentos/Estados/';
    $pesoMaxicoImagen = 2000000;
    $nombreCompuestoImagen = "default.txt";
    $nombre_imagen = basename($_FILES['estado']['name']); 
    $tipo_Imgaen = $_FILES['estado']['type'];
    $tamanio_imagen = $_FILES['estado']['size'];
    //echo $nombre_imagen;

    $casoPng = ".png";
    $casoJpeg = ".jpg";
    //comprovadores de peso y tipo de imagen

    if ($tamanio_imagen < $pesoMaxicoImagen) {

        //mueve la imagen a la carpeta seleccionada
        move_uploaded_file($_FILES['estado']['tmp_name'], ''. $carpetaDestino . $nombre_imagen);
        // dar permisos de lectura y escritura
        chmod(appFilesystemPath($carpetaDestino . $nombre_imagen), 0640);
        // renombrar el archivo con la id del usuario, la fecha y hora actual y el tipo de archivo
        $nombreCompuestoImagen = $carpetaDestino . "Documento-" . $id . "-" . date("Y-m-d-H-i-s") . "." . procesarFormatos($tipo_Imgaen);
        // rename renombra el archivo
        rename('' . $carpetaDestino . $nombre_imagen, '' . $nombreCompuestoImagen);
        // dar permisos de lectura y escritura
        chmod('' . $nombreCompuestoImagen, 0640);
        // unlink elimina el archivo

        return '/Documentos/Estados/' . "Documento-" . $id . "-" . date("Y-m-d-H-i-s") . "." . procesarFormatos($tipo_Imgaen);
    } else {
        return "0";
    }
}



function procesarCaratulas($id)
{
    $carpetaDestino = '/Documentos/Caratulas/';
    $pesoMaxicoImagen = 2000000;
    $nombreCompuestoImagen = "default.txt";
    $nombre_imagen = basename($_FILES['caratula']['name']);
    $tipo_Imgaen = $_FILES['caratula']['type'];
    $tamanio_imagen = $_FILES['caratula']['size'];
    //echo $nombre_imagen;

    $casoPng = ".png";
    $casoJpeg = ".jpg";
    //comprovadores de peso y tipo de imagen

    if ($tamanio_imagen < $pesoMaxicoImagen) {

        //mueve la imagen a la carpeta seleccionada
        move_uploaded_file($_FILES['caratula']['tmp_name'], APP_ROOT . $carpetaDestino . $nombre_imagen);
        // dar permisos de lectura y escritura
        chmod(appFilesystemPath($carpetaDestino . $nombre_imagen), 0640);
        // renombrar el archivo con la id del usuario, la fecha y hora actual y el tipo de archivo
        $nombreCompuestoImagen = $carpetaDestino . "Documento-" . $id . "-" . date("Y-m-d-H-i-s") . "." . procesarFormatos($tipo_Imgaen);
        // rename renombra el archivo
        rename(APP_ROOT . $carpetaDestino . $nombre_imagen, APP_ROOT . $nombreCompuestoImagen);
        // dar permisos de lectura y escritura
        chmod(APP_ROOT . $nombreCompuestoImagen, 0640);
        // unlink elimina el archivo
  
        return $nombreCompuestoImagen;
    } else {
        return "0";
    }
}



function procesarArchivoKey($id)
{
    $carpetaDestino = '/Documentos/Key/';
    $pesoMaxicoImagen = 2000000;
    $nombreCompuestoImagen = "default.txt";
    $nombre_imagen = basename($_FILES['comprobante']['name']);
    $tipo_Imgaen = $_FILES['comprobante']['type'];
    $tamanio_imagen = $_FILES['comprobante']['size'];
    //echo $nombre_imagen;

    $casoPng = ".png";
    $casoJpeg = ".jpg";
    //comprovadores de peso y tipo de imagen

    if ($tamanio_imagen < $pesoMaxicoImagen) {

        //mueve la imagen a la carpeta seleccionada
        move_uploaded_file($_FILES['comprobante']['tmp_name'], APP_ROOT . $carpetaDestino . $nombre_imagen);
        // dar permisos de lectura y escritura
        chmod(appFilesystemPath($carpetaDestino . $nombre_imagen), 0640);
        // renombrar el archivo con la id del usuario, la fecha y hora actual y el tipo de archivo
        $nombreCompuestoImagen = $carpetaDestino . "Documento-" . $id . "-" . date("Y-m-d-H-i-s") . "." . "key";
        // rename renombra el archivo
        rename(APP_ROOT . $carpetaDestino . $nombre_imagen, APP_ROOT . $nombreCompuestoImagen);
        // dar permisos de lectura y escritura
        chmod(APP_ROOT . $nombreCompuestoImagen, 0640);
        // unlink elimina el archivo
  
        return $nombreCompuestoImagen;
    } else {
        return "0";
    }
}

function procesarArchivoSdg($id)
{
    $carpetaDestino = '/Documentos/Sdg/';
    $pesoMaxicoImagen = 2000000;
    $nombreCompuestoImagen = "default.txt";
    $nombre_imagen = basename($_FILES['comprobante']['name']);
    $tipo_Imgaen = $_FILES['comprobante']['type'];
    $tamanio_imagen = $_FILES['comprobante']['size'];
    //echo $nombre_imagen;

    $casoPng = ".png";
    $casoJpeg = ".jpg";
    //comprovadores de peso y tipo de imagen

    if ($tamanio_imagen < $pesoMaxicoImagen) {

        //mueve la imagen a la carpeta seleccionada
        move_uploaded_file($_FILES['comprobante']['tmp_name'], APP_ROOT . $carpetaDestino . $nombre_imagen);
        // dar permisos de lectura y escritura
        chmod(appFilesystemPath($carpetaDestino . $nombre_imagen), 0640);
        // renombrar el archivo con la id del usuario, la fecha y hora actual y el tipo de archivo
        $nombreCompuestoImagen = $carpetaDestino . "Documento-" . $id . "-" . date("Y-m-d-H-i-s") . "." . "sdg";
        // rename renombra el archivo
        rename(APP_ROOT . $carpetaDestino . $nombre_imagen, APP_ROOT . $nombreCompuestoImagen);
        // dar permisos de lectura y escritura
        chmod(APP_ROOT . $nombreCompuestoImagen, 0640);
        // unlink elimina el archivo
  
        return $nombreCompuestoImagen;
    } else {
        return "0";
    }
}

function procesarArchivoCer($id)
{
    $carpetaDestino = '/Documentos/Cer/';
    $pesoMaxicoImagen = 2000000;
    $nombreCompuestoImagen = "default.txt";
    $nombre_imagen = basename($_FILES['comprobante']['name']);
    $tipo_Imgaen = $_FILES['comprobante']['type'];
    $tamanio_imagen = $_FILES['comprobante']['size'];
    //echo $nombre_imagen;

    $casoPng = ".png";
    $casoJpeg = ".jpg";
    //comprovadores de peso y tipo de imagen

    if ($tamanio_imagen < $pesoMaxicoImagen) {

        //mueve la imagen a la carpeta seleccionada
        move_uploaded_file($_FILES['comprobante']['tmp_name'], APP_ROOT . $carpetaDestino . $nombre_imagen);
        // dar permisos de lectura y escritura
        chmod(appFilesystemPath($carpetaDestino . $nombre_imagen), 0640);
        // renombrar el archivo con la id del usuario, la fecha y hora actual y el tipo de archivo
        $nombreCompuestoImagen = $carpetaDestino . "Documento-" . $id . "-" . date("Y-m-d-H-i-s") . "." . "cer";
        // rename renombra el archivo
        rename(APP_ROOT . $carpetaDestino . $nombre_imagen, APP_ROOT . $nombreCompuestoImagen);
        // dar permisos de lectura y escritura
        chmod(APP_ROOT . $nombreCompuestoImagen, 0640);
        // unlink elimina el archivo
  
        return $nombreCompuestoImagen;
    } else {
        return "0";
    }
}


function procesarFormatos($formato)
{
    $formato = strtolower(trim($formato));
    // Normalizar variantes comunes
    if ($formato === 'application/pdf' || $formato === 'application/x-pdf') return 'pdf';
    if ($formato === 'image/jpeg' || $formato === 'image/jpg' || $formato === 'image/pjpeg') return 'jpg';
    if ($formato === 'image/png' || $formato === 'image/x-png') return 'png';
    // Word
    if ($formato === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') return 'docx';
    if ($formato === 'application/msword') return 'doc';
    // Excel
    if ($formato === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') return 'xlsx';
    if ($formato === 'application/vnd.ms-excel') return 'xls';
    // PowerPoint
    if ($formato === 'application/vnd.openxmlformats-officedocument.presentationml.presentation') return 'pptx';
    if ($formato === 'application/vnd.ms-powerpoint') return 'ppt';
    // Otros
    if ($formato === 'application/zip') return 'zip';
    if ($formato === 'application/x-rar-compressed') return 'rar';
    if ($formato === 'text/plain') return 'txt';
    if ($formato === 'application/vnd.oasis.opendocument.text') return 'odt';
    if ($formato === 'application/vnd.oasis.opendocument.spreadsheet') return 'ods';
    if ($formato === 'application/vnd.oasis.opendocument.presentation') return 'odp';
    if ($formato === 'application/vnd.oasis.opendocument.graphics') return 'odg';
    if ($formato === 'application/vnd.oasis.opendocument.chart') return 'odc';
    if ($formato === 'keynote') return 'key';
    if ($formato === 'sdg') return 'sdg';

    // Fallback: si el tipo es imagen pero no está mapeado, usar extensión por inspección
    if (strpos($formato, 'image/') === 0) {
        if (strpos($formato, 'jpeg') !== false || strpos($formato, 'jpg') !== false) return 'jpg';
        if (strpos($formato, 'png') !== false) return 'png';
    }
    // Fallback: si el tipo es pdf
    if (strpos($formato, 'pdf') !== false) return 'pdf';

    return 'bin';
}


function agregarEmpresa()
{

    $logo = procesarImagen(rand(0, 40000));
    $razon = $_POST['razon'];
    $rfc = $_POST['rfc'];
    $calle = $_POST['calle'];
    $cp = $_POST['cp'];
    $estado = $_POST['estado'];
    $regimen = $_POST['regimen'];
    $finDominio = $_POST['finDominio'];
    $prioridad = $_POST['prioridad'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $sitioWeb = $_POST['sitioWeb'];
    $descripcion = $_POST['descripcion'];
    // $numero = $_POST['numero'];
    // $colonia = $_POST['colonia'];
    // $passwordR = $_POST['passwordR'];
    // $usuario = $_POST['usuario'];
    // $inicioDominio = $_POST['inicioDominio'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->agregarEmpresa($razon, $rfc, $calle, '', '', $cp, $estado, $logo, $regimen, '', '', '-', '', $finDominio, $prioridad, $telefono, $correo, $descripcion, $sitioWeb);
    echo "1";
}

function eliminarEmpresa()
{
    $id = $_POST['id'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->eliminarEmpresa($id);
    echo "1";
}

function modificarEmpresa()
{
    $id = $_POST['id'];
    $razon = $_POST['razon'];
    $rfc = $_POST['rfc'];
    $calle = $_POST['calle'];
    $numero = $_POST['numero'];
    $colonia = $_POST['colonia'];
    $cp = $_POST['cp'];
    $estado = $_POST['estado'];
    $regimen = $_POST['regimen'];
    $passwordR = $_POST['passwordR'];
    $usuario = $_POST['usuario'];
    $inicioDominio = $_POST['inicioDominio'];
    $finDominio = $_POST['finDominio'];
    $prioridad = $_POST['prioridad'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $descripcion = $_POST['descripcion'];
    $sitioWeb = $_POST['sitioWeb'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->modificarEmpresa($id, $razon, $rfc, $calle, $numero, $colonia, $cp, $estado, $regimen, $passwordR, $usuario, $inicioDominio, $finDominio, $prioridad, $telefono, $correo, $descripcion, $sitioWeb);
    echo "1";
}

function buscarPalabra(string $texto, string $palabra): bool
{
    return strpos(strtolower($texto), strtolower($palabra)) !== false;
}


function determinaFechaDia(string $texto): string
{
    $meses = [
        'enero' => '01',
        'febrero' => '02',
        'marzo' => '03',
        'abril' => '04',
        'mayo' => '05',
        'junio' => '06',
        'julio' => '07',
        'agosto' => '08',
        'septiembre' => '09',
        'octubre' => '10',
        'noviembre' => '11',
        'diciembre' => '12',
    ];

    $anios = [
        '2019' => '2019',
        '2020' => '2020',
        '2021' => '2021',
        '2022' => '2022',
        '2023' => '2023',
        '2024' => '2024',
        '2025' => '2025',
        '2026' => '2026',
        '2027' => '2027',
        '2028' => '2028',
        '2029' => '2029',
        '2030' => '2030',
        '2031' => '2031',
        '2032' => '2032',
        '2033' => '2033',
        '2034' => '2034',
        '2035' => '2035',
        '2036' => '2036',
        '2037' => '2037',
        '2038' => '2038',
        '2039' => '2039',
    ];

    $fecha = [
        'mes' => '',
        'anio' => '',
        'dia' => '',
    ];

    // Buscar el mes en el diccionario de meses
    foreach ($meses as $palabra => $numero) {
        if (stripos($texto, $palabra) !== false) {
            $fecha['mes'] = $numero;
            break;
        }
    }

    // Buscar el año en el diccionario de años
    foreach ($anios as $palabra) {
        if (stripos($texto, $palabra) !== false) {
            $fecha['anio'] = $palabra;
            break;
        }
    }

    // Buscar el día utilizando una expresión regular
    $pattern = '/\b(\d{1,2})\b/';
    preg_match($pattern, $texto, $matches);
    if (isset($matches[1])) {
        $fecha['dia'] = $matches[1];
    }

    return $fecha['anio'] . "-" . $fecha['mes'] . "-" . $fecha['dia'];
}



function determinaFecha($texto)
{
    $mes = "";
    $anio = "";
    if (buscarPalabra($texto, "enero")) {
        $mes = "01";
    } else if (buscarPalabra($texto, "febrero")) {
        $mes = "02";
    } else if (buscarPalabra($texto, "marzo")) {
        $mes = "03";
    } else if (buscarPalabra($texto, "abril")) {
        $mes = "04";
    } else if (buscarPalabra($texto, "mayo")) {
        $mes = "05";
    } else if (buscarPalabra($texto, "junio")) {
        $mes = "06";
    } else if (buscarPalabra($texto, "julio")) {
        $mes = "07";
    } else if (buscarPalabra($texto, "agosto")) {
        $mes = "08";
    } else if (buscarPalabra($texto, "septiembre")) {
        $mes = "09";
    } else if (buscarPalabra($texto, "octubre")) {
        $mes = "10";
    } else if (buscarPalabra($texto, "noviembre")) {
        $mes = "11";
    } else if (buscarPalabra($texto, "diciembre")) {
        $mes = "12";
    }


    if (buscarPalabra($texto, "2019")) {
        $anio = "2019";
    } else if (buscarPalabra($texto, "2020")) {
        $anio = "2020";
    } else if (buscarPalabra($texto, "2021")) {
        $anio = "2021";
    } else if (buscarPalabra($texto, "2022")) {
        $anio = "2022";
    } else if (buscarPalabra($texto, "2023")) {
        $anio = "2023";
    } else if (buscarPalabra($texto, "2024")) {
        $anio = "2024";
    } else if (buscarPalabra($texto, "2025")) {
        $anio = "2025";
    } else if (buscarPalabra($texto, "2026")) {
        $anio = "2026";
    } else if (buscarPalabra($texto, "2027")) {
        $anio = "2027";
    } else if (buscarPalabra($texto, "2028")) {
        $anio = "2028";
    } else if (buscarPalabra($texto, "2029")) {
        $anio = "2029";
    } else if (buscarPalabra($texto, "2030")) {
        $anio = "2030";
    } else if (buscarPalabra($texto, "2031")) {
        $anio = "2031";
    } else if (buscarPalabra($texto, "2032")) {
        $anio = "2032";
    } else if (buscarPalabra($texto, "2033")) {
        $anio = "2033";
    } else if (buscarPalabra($texto, "2034")) {
        $anio = "2034";
    } else if (buscarPalabra($texto, "2035")) {
        $anio = "2035";
    } else if (buscarPalabra($texto, "2036")) {
        $anio = "2036";
    } else if (buscarPalabra($texto, "2037")) {
        $anio = "2037";
    } else if (buscarPalabra($texto, "2038")) {
        $anio = "2038";
    } else if (buscarPalabra($texto, "2039")) {
        $anio = "2039";
    }
    return $anio . "-" . $mes . "-01";
}



function formatearAcento($texto)
{
    $texto = str_replace("á", "a", $texto);
    $texto = str_replace("é", "e", $texto);
    $texto = str_replace("í", "i", $texto);
    $texto = str_replace("ó", "o", $texto);
    $texto = str_replace("ú", "u", $texto);
    $texto = str_replace("Á", "A", $texto);
    $texto = str_replace("É", "E", $texto);
    $texto = str_replace("Í", "I", $texto);
    $texto = str_replace("Ó", "O", $texto);
    $texto = str_replace("Ú", "U", $texto);
    $texto = str_replace("ñ", "n", $texto);
    $texto = str_replace("Ñ", "N", $texto);
    return $texto;
}

function modificarConstanciaSf()
{

    $adminEmpresa = new AdministradorEmpresa();
    $id = $_POST['id'];
    $constancia = procesarConstanciaSf($id);
    $constancia = str_replace(APP_ROOT, '', $constancia);
    $aviso = new Aviso();
    if ($adminEmpresa->modificarConstancia($id, $constancia, date("Y-m-d"))) {
        $aviso->estatus = "Exito";
        $aviso->mensaje = "La constancia se ha modificado correctamente";
        $aviso->subMensaje = "Documento de la empresa ";
        $adminCamposPostergados = new AdministradorCamposPostergados();
        $adminCamposPostergados->resetPostergacionesCampo($id, 'constanciaSituacionFiscal');
        $adminHistoricoArchivos = new AdministradorHistoricoArchivos();
        if ($adminHistoricoArchivos->agregarHistoricoArchivos("Constancia de situación fiscal", $id, $constancia)) {
            $nombreArchivo = $adminHistoricoArchivos->obtenerElTercerRegistroMasAntiguo("Constancia de situación fiscal", $id);
            if ($nombreArchivo != "") {
                if (file_exists(APP_ROOT . $nombreArchivo)) {
                    if (unlink(APP_ROOT . $nombreArchivo)) {
                        $adminHistoricoArchivos->eliminarHistoricoArchivo("Constancia de situación fiscal", $id, $nombreArchivo);
                    }
                }
            }
        }
    } else {
        $aviso->estatus = "Error";
        $aviso->mensaje = "No se pudo modificar la constancia";
    }
    echo json_encode($aviso);
}



function modificarPdf()
{
    $adminEmpresa = new AdministradorEmpresa();
    $id = $_POST['id'];
    $pdf = procesarPdf($id);

    $aviso = new Aviso();
    $fecha = date("Y-m-d");

    // Debug: Verificar que la ruta sea relativa
    error_log("PDF Path para guardar: " . $pdf);
    error_log("ID Empresa: " . $id);
    error_log("Fecha: " . $fecha);

    $resultado = $adminEmpresa->modificarPdf($id, $pdf, $fecha);

    error_log("Resultado de modificarPdf: " . ($resultado ? "true" : "false"));

    if ($resultado) {
        $aviso->estatus = "Exito";
        $aviso->mensaje = "El documento 32D se ha modificado correctamente";
        $aviso->subMensaje = "Documento de la empresa";
        $adminCamposPostergados = new AdministradorCamposPostergados();
        $adminCamposPostergados->resetPostergacionesCampo($id, '32D');

        $adminHistoricoArchivos = new AdministradorHistoricoArchivos();
        if ($adminHistoricoArchivos->agregarHistoricoArchivos("32D", $id, $pdf)) {
            $nombreArchivo = $adminHistoricoArchivos->obtenerElTercerRegistroMasAntiguo("32D", $id);
            if ($nombreArchivo != "") {
                if (file_exists(APP_ROOT . $nombreArchivo)) {
                    if (unlink(APP_ROOT . $nombreArchivo)) {
                        $adminHistoricoArchivos->eliminarHistoricoArchivo("32D", $id, $nombreArchivo);
                    }
                }
            }
        }
    } else {
        $aviso->estatus = "Error";
        $aviso->mensaje = "No se pudo modificar el documento 32D en la base de datos";
    }

    echo json_encode($aviso);
}



function modificarFrecuencias()
{
    $adminEmpresa = new AdministradorEmpresa();
    $id = $_POST['id'];
    $frecTel = $_POST['frecTel'];
    $frecCorreo = $_POST['frecCorreo'];
    $frecDireccion = $_POST['frecDireccion'];
    $frecWeb = $_POST['frecWeb'];
    $empresa = $adminEmpresa->dameEmpresa($id);
    $adminEmpresa->modificarFrecuencia($id, $frecTel, $frecCorreo, $frecDireccion, $frecWeb);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Las frecuencias se han modificado correctamente";
    $aviso->subMensaje = "Frecuencias de la empresa " . $empresa->razon;
    echo json_encode($aviso);
}

function modificarImagen()
{
    $id = $_POST['id'];
    $logo = procesarImagen($id);
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->modificarImagen($id, $logo);
    echo "1";
}

function nuevoCorreo()
{
    $id = $_POST['id'];
    $correo = $_POST['correo'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->agregarCorreo($correo, $id);
    echo "1";
}

function eliminarCorreo()
{
    $id = $_POST['id'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->eliminarCorreo($id);
    echo "1";
}


function modificarComprobante()
{
    $id = $_POST['id'];
    $comprobante = procesarArchivo($id);

    if ($comprobante != "0") {
        $adminEmpresa = new AdministradorEmpresa();
        if ($adminEmpresa->modificarComprobanteDomicilio($id, $comprobante)) {
            echo json_encode(array("status" => "success", "message" => "El comprobante se ha modificado correctamente"));
            $adminCamposPostergados = new AdministradorCamposPostergados();
            $adminCamposPostergados->resetPostergacionesCampo($id, 'comprobanteDomicilio');
            $adminHistoricoArchivos = new AdministradorHistoricoArchivos();
            if ($adminHistoricoArchivos->agregarHistoricoArchivos("Comprobante de domicilio", $id, $comprobante)) {
                $nombreArchivo = $adminHistoricoArchivos->obtenerElTercerRegistroMasAntiguo("Comprobante de domicilio", $id);
                if ($nombreArchivo != "") {
                    if (file_exists(APP_ROOT . $nombreArchivo)) {
                        if (unlink(APP_ROOT . $nombreArchivo)) {
                            $adminHistoricoArchivos->eliminarHistoricoArchivo("Comprobante de domicilio", $id, $nombreArchivo);
                        }
                    }
                }
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "El comprobante no se ha podido modificar"));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "El archivo no es valido"));
    }
}




function modificarNombre()
{
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->modificarNombre($id, $nombre);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha modificado correctamente el nombre: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function modificarRazonSocial()
{
    $id = $_POST['id'];
    $razonSocial = $_POST['razonSocial'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->modificarNombre($id, $razonSocial);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha modificado correctamente la razon social: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function modificarRfc()
{
    $id = $_POST['id'];
    $rfc = $_POST['rfc'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->modificarRfc($id, $rfc);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha modificado correctamente el rfc: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function modificarDireccion()
{
    $id = $_POST['id'];
    $direccion = $_POST['direccion'];
    $colonia = $_POST['colonia'];
    $numero = $_POST['numero'];
    $frecuencia = $_POST['frecuencia'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->modificarCalle($id, $direccion);
    $adminEmpresa->modificarColonia($id, $colonia);
    $adminEmpresa->modificarNumero($id, $numero);
    $adminEmpresa->modificarFrecuenciaDireccion($id, $frecuencia);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha modificado correctamente la direccion: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function modificarTelefono()
{
    $id = $_POST['id'];
    $telefono = $_POST['telefono'];
    $frecuencia = "cadaMes-" . date("Y-m-d");
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->modificarTelefono($id, $telefono, $frecuencia);
    $adminCamposPostergados = new AdministradorCamposPostergados();
    $adminCamposPostergados->resetPostergacionesCampo($id, 'telefono');
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha modificado correctamente el telefono: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function modificarSitioWeb()
{
    $id = $_POST['id'];
    $web = $_POST['web'];
    $frecuencia = "cadaMes-" . date("Y-m-d");
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->modificarSitioWeb($id, $web, $frecuencia);
    $adminCamposPostergados = new AdministradorCamposPostergados();
    $adminCamposPostergados->resetPostergacionesCampo($id, 'sitioWeb');
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha modificado correctamente la web: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function modificarDescripcion()
{
    $id = $_POST['id'];
    $descripcion = $_POST['descripcion'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->modificarDescripcion($id, $descripcion);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha modificado correctamente la descripcion: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}


function modificarCodigoPostal()
{
    $id = $_POST['id'];
    $codigoPostal = $_POST['codigoPostal'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->modificarCp($id, $codigoPostal);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha modificado correctamente el codigo postal: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function modificarEstado()
{
    $id = $_POST['id'];
    $estado = $_POST['estado'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->modificarEstado($id, $estado);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha modificado correctamente el estado: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function modificarRegimen()
{
    $id = $_POST['id'];
    $regimen = $_POST['regimen'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->modificarRegimen($id, $regimen);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha modificado correctamente el regimen: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function modificarFinDominio()
{
    $id = $_POST['id'];
    $finDominio = $_POST['finDominio'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->modificarFinDominio($id, $finDominio);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha modificado correctamente el fin de dominio: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}


function modificarCorreo()
{
    $id = $_POST['id'];
    $correo = $_POST['correo'];
    $frecuencia = $_POST['frecuencia'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->modificarCorreo($id, $correo, $frecuencia);
    $adminCamposPostergados = new AdministradorCamposPostergados();
    $adminCamposPostergados->resetPostergacionesCampo($id, 'correoContacto');
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha modificado correctamente el correo: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function agregarCuenta()
{
    $banco = $_POST['banco'];
    $clave = $_POST['clave'];
    $numero = $_POST['numero'];
    $estatus = $_POST['estatus'];
    $idEmpresa = $_POST['id'];
    $moneda = $_POST['moneda'];

    $adminEmpresa = new AdministradorEmpresa();
    // Obtener razón social por id
    $razonSocial = '';
    $empresas = $adminEmpresa->dameEmpresas();
    foreach ($empresas as $e) {
        if ($e->id == $idEmpresa) {
            $razonSocial = $e->razon;
            break;
        }
    }
    $adminEmpresa->agregarCuenta($banco, $numero, $clave, $razonSocial, $estatus, $moneda);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha agregado correctamente la cuenta: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function eliminarCuenta()
{
    header('Content-Type: application/json');
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $aviso = new Aviso();
    if (!$id) {
        $aviso->estatus = "Error";
        $aviso->mensaje = "ID de cuenta no proporcionado";
        $aviso->subMensaje = "";
        echo json_encode($aviso);
        return;
    }
    try {
        $adminEmpresa = new AdministradorEmpresa();
        $adminEmpresa->eliminarCuentaBanco($id);
        $aviso->estatus = "Exito";
        $aviso->mensaje = "Se ha eliminado correctamente la cuenta";
        $aviso->subMensaje = "Documento de la empresa ";
    } catch (Exception $e) {
        $aviso->estatus = "Error";
        $aviso->mensaje = "Error al eliminar la cuenta: " . $e->getMessage();
        $aviso->subMensaje = "";
    }
    echo json_encode($aviso);
}

function agregarContraSat()
{
    $id = $_POST['id'];
    $contrasena = $_POST['contrasena'];
    $usuario = $_POST['usuario'];
    $rfc = $_POST['rfc'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->agregarContraSat($id, $usuario, $contrasena, $rfc);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha agregado correctamente la contraseña de sat: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function eliminarContraSat()
{
    $id = $_POST['id'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->eliminarContraSat($id);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha eliminado correctamente la contraseña de sat: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function agregarCuentaIofact()
{
    $id = $_POST['id'];
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $rfc = $_POST['rfc'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->agregaContraIofacturo($rfc, $contrasena, $usuario, $id);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha agregado correctamente la cuenta de iofact: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function eliminarCuentaIofact()
{
    $id = $_POST['id'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->eliminarContasIofacturo($id);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha eliminado correctamente la cuenta de iofact id: $id";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function agregarContraBanco()
{
    $id = $_POST['id'];
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $claveOpeerativa = $_POST['claveOp'];
    $nip = $_POST['nip'];
    $banco = $_POST['banco'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->agregarContraBanco($usuario, $contrasena, $claveOpeerativa, $nip, $id, $banco);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha agregado correctamente la cuenta de banco: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function eliminarContraBanco()
{
    $id = $_POST['id'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->eliminarContraBanco($id);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha eliminado correctamente la cuenta de banco: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function agregarDocumentoFiels()
{

    $adminEmpresa = new AdministradorEmpresa();
    $empresa = $_POST['id'];
    $tipo = $_POST['tipo'];
    if ($tipo == "KEY") {
        $documento = procesarArchivoKey(random_int(0, 1000000));
        $adminEmpresa->agregarFiel($empresa, $documento, $tipo);
        $aviso = new Aviso();
        $aviso->estatus = "Exito";
        $aviso->mensaje = "Se ha agregado correctamente el documento: KEY $empresa";
        $aviso->subMensaje = "Documento de la empresa ";
        echo json_encode($aviso);
    } else if ($tipo == "CSD") {
        $documento = procesarArchivoCer(random_int(0, 1000000));
        $adminEmpresa->agregarFiel($empresa, $documento, "CER");
        $aviso = new Aviso();
        $aviso->estatus = "Exito";
        $aviso->mensaje = "Se ha agregado correctamente el documento: Sdg $empresa";
        $aviso->subMensaje = "Documento de la empresa ";
        echo json_encode($aviso);
    } else {
        $aviso = new Aviso();
        $aviso->estatus = "Error";
        $aviso->mensaje = "No se ha podido agregar el documento";
        $aviso->subMensaje = "No se ha podido agregar el documento archivo incompatible";
        echo json_encode($aviso);
        return;
    }
}

function agregarSelloSat()
{

    $adminEmpresa = new AdministradorEmpresa();
    $empresa = $_POST['id'];
    $tipo = $_POST['tipo'];
    if ($tipo == "KEY") {
        $documento = procesarArchivoKey(random_int(0, 1000000));
        $adminEmpresa->agregarSelloSat($empresa, $documento, $tipo);
        $aviso = new Aviso();
        $aviso->estatus = "Exito";
        $aviso->mensaje = "Se ha agregado correctamente el documento: KEY $empresa";
        $aviso->subMensaje = "Documento de la empresa ";
        echo json_encode($aviso);
    } else if ($tipo == "CSD") {
        $documento = procesarArchivoCer(random_int(0, 1000000));
        $adminEmpresa->agregarSelloSat($empresa, $documento, "CER");
        $aviso = new Aviso();
        $aviso->estatus = "Exito";
        $aviso->mensaje = "Se ha agregado correctamente el documento: Sdg $empresa";
        $aviso->subMensaje = "Documento de la empresa ";
        echo json_encode($aviso);
    } else {
        $aviso = new Aviso();
        $aviso->estatus = "Error";
        $aviso->mensaje = "No se ha podido agregar el documento";
        $aviso->subMensaje = "No se ha podido agregar el documento archivo incompatible";
        echo json_encode($aviso);
        return;
    }
}

function eliminarSellosSat()
{
    $id = $_POST['id'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->eliminarSellosSat($id);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha eliminado correctamente el documento: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}


function eliminarDocumentoFiels()
{
    $id = $_POST['id'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->eliminarFiel($id);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha eliminado correctamente el documento: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function agregarActaConst()
{
    $adminEmpresa = new AdministradorEmpresa();
    $empresa = $_POST['id'];
    $documento = procesarActas(random_int(0, 1000000));
    $contenido = "";
    if (isset($_POST['ActaCost'])) {
        $contenido = $contenido .  $_POST['ActaCost'] . ",";
    }
    if (isset($_POST['Rppd'])) {
        $contenido = $contenido .  $_POST['Rppd'] . ",";
    }
    if (isset($_POST['Asamblea'])) {
        $contenido = $contenido .  $_POST['Asamblea'] . ",";
    }
    if (isset($_POST['Poder'])) {
        $contenido = $contenido .  $_POST['Poder'] . ",";
    }
    if (isset($_POST['IneR'])) {
        $contenido = $contenido .  $_POST['IneR'] . ",";
    }
    if (isset($_POST['IneS'])) {
        $contenido = $contenido .  $_POST['IneS'] . ",";
    }
    if (isset($_POST['CoSo'])) {
        $contenido = $contenido .  $_POST['CoSo'] . ",";
    }
    if (isset($_POST['CoRe'])) {
        $contenido = $contenido .  $_POST['CoRe'] . ",";
    }
    if (isset($_POST['rppca'])) {
        $contenido = $contenido .  $_POST['rppca'] . ",";
    }


    $contenido = $contenido . "Documento";
    $adminEmpresa->agregarActaConst($empresa, $documento, $contenido);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha agregado correctamente el documento: Acta $empresa";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function eliminarActaConst()
{
    $id = $_POST['id'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->eliminarActaConst($id);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha eliminado correctamente el documento: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function agregarDocumentoPermanente()
{
    if (!isset($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'ID de empresa requerido']);
        return;
    }

    if (!isset($_FILES['documento'])) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'Documento requerido']);
        return;
    }

	$empresa = filter_var($_POST['id'], FILTER_VALIDATE_INT);
	if (!$empresa || $empresa <= 0) {
		http_response_code(422);
		echo json_encode(['estatus' => 'Error', 'mensaje' => 'ID de empresa inválido']);
		return;
	}
    // Aceptar 'descripcion' como string o arreglo (cuando viene de checkboxes: descripcion[])
    $descripcion = 'DocumentoPermanente';
    if (isset($_POST['descripcion'])) {
        if (is_array($_POST['descripcion'])) {
            $parts = array_filter(array_map('trim', $_POST['descripcion']));
            $descripcion = count($parts) ? implode('; ', $parts) : 'DocumentoPermanente';
        } else {
            $tmp = trim((string)$_POST['descripcion']);
            $descripcion = $tmp !== '' ? $tmp : 'DocumentoPermanente';
        }
    }
    $documento = procesarDocumentoPermanente(random_int(0, 1000000));

    if ($documento === "0") {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'No se pudo procesar el documento permanente']);
        return;
    }

	$adminEmpresa = new AdministradorEmpresa();
	// Obtener id de usuario autenticado (payload JWT)
	$payload = requireAuth();
	$userId = isset($payload['id']) ? intval($payload['id']) : null;
	try {
		$idDocumento = $adminEmpresa->agregarDocumentoPermanente($empresa, $documento, $descripcion, $userId);
		if (!$idDocumento) {
			throw new RuntimeException('No se creó el registro del documento.');
		}
	} catch (Throwable $e) {
		$rutaGuardada = appFilesystemPath($documento);
		if (is_file($rutaGuardada)) {
			@unlink($rutaGuardada);
		}
		error_log('Permanent document insert failed: ' . $e->getMessage());
		http_response_code(500);
		echo json_encode(['estatus' => 'Error', 'mensaje' => 'No fue posible registrar el documento.']);
		return;
	}

    $resolverId = isset($_POST['resolverArchivoFaltanteId']) ? intval($_POST['resolverArchivoFaltanteId']) : 0;
    $alertaResuelta = false;
    if ($resolverId > 0) {
        $alertaResuelta = $adminEmpresa->actualizarEstadoDocumentoPermanente($resolverId, 'completado');
    }

    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Documento permanente agregado y en revisión.";
    $aviso->subMensaje = "Documento: $descripcion";
	$aviso->id = $idDocumento;
    if ($resolverId > 0) {
        $aviso->subMensaje .= $alertaResuelta
            ? ". Alerta de archivo faltante actualizada."
            : ". No se pudo actualizar la alerta seleccionada.";
    }
    echo json_encode($aviso);
}

function eliminarDocumentoPermanente()
{
    if (!isset($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'ID de documento requerido']);
        return;
    }

	$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
	if (!$id || $id <= 0) {
		http_response_code(422);
		echo json_encode(['estatus' => 'Error', 'mensaje' => 'ID de documento inválido']);
		return;
	}

    $adminEmpresa = new AdministradorEmpresa();
	try {
		$rutaDocumento = $adminEmpresa->obtenerRutaDocumentoPermanente($id);
		if ($rutaDocumento === null) {
			http_response_code(404);
			echo json_encode(['estatus' => 'Error', 'mensaje' => 'El documento ya no existe.']);
			return;
		}
		if (!$adminEmpresa->eliminarDocumentoPermanente($id)) {
			throw new RuntimeException('La base de datos no confirmó la eliminación.');
		}
	} catch (Throwable $e) {
		error_log('Permanent document delete failed: ' . $e->getMessage());
		http_response_code(500);
		echo json_encode(['estatus' => 'Error', 'mensaje' => 'No fue posible eliminar el documento.']);
		return;
	}

	$archivoEliminado = true;
	if ($rutaDocumento !== '') {
		$baseDocumentos = realpath(appFilesystemPath('/Documentos/DocumentosPermanentes/'));
		$rutaFisica = realpath(appFilesystemPath($rutaDocumento));
		$baseNormalizada = $baseDocumentos ? strtolower(str_replace('\\', '/', $baseDocumentos)) . '/' : '';
		$rutaNormalizada = $rutaFisica ? strtolower(str_replace('\\', '/', $rutaFisica)) : '';
		if ($rutaFisica && $baseNormalizada !== '' && str_starts_with($rutaNormalizada, $baseNormalizada)) {
			$archivoEliminado = @unlink($rutaFisica);
		}
	}
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha eliminado correctamente el documento permanente.";
	$aviso->subMensaje = $archivoEliminado
		? "Registro y archivo eliminados"
		: "Registro eliminado; el archivo físico quedó pendiente de limpieza";
    echo json_encode($aviso);
}

function solicitarDocumentoPermanente()
{
    // Crear un registro de documento permanente en estado 'archivo_faltante' sin archivo
    if (!isset($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'ID de empresa requerido']);
        return;
    }

    $empresa = trim((string)$_POST['id']);
    if ($empresa === '') {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'ID de empresa inválido']);
        return;
    }

    $descripcion = 'DocumentoPermanente';
    if (isset($_POST['descripcion'])) {
        $tmp = trim((string)$_POST['descripcion']);
        if ($tmp !== '') {
            $descripcion = $tmp;
        }
    }

    try {
        $adminEmpresa = new AdministradorEmpresa();
        $payload = requireAuth();
        $userId = isset($payload['id']) ? intval($payload['id']) : null;
        // Insertar placeholder sin archivo, con estado archivo_faltante
        $adminEmpresa->agregarActaConst($empresa, '', $descripcion, 'archivo_faltante', $userId);

        $aviso = new Aviso();
        $aviso->estatus = "Exito";
        $aviso->mensaje = "Solicitud registrada: documento pendiente.";
        $aviso->subMensaje = "Documento: $descripcion";
        echo json_encode($aviso);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'No se pudo registrar la solicitud']);
    }
}

function marcarArchivoFaltanteDocumentoPermanente()
{
    if (!isset($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'ID de documento requerido']);
        return;
    }

    $id = intval($_POST['id']);
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'ID de documento inválido']);
        return;
    }

    $adminEmpresa = new AdministradorEmpresa();
    $actualizado = $adminEmpresa->actualizarEstadoDocumentoPermanente($id, 'archivo_faltante');

    if (!$actualizado) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'No se pudo actualizar el estado del documento permanente. Verifica que la columna estado_documento exista.']);
        return;
    }

    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Documento permanente marcado como archivo faltante.";
    $aviso->subMensaje = "Se notificó la ausencia del documento.";
    echo json_encode($aviso);
}

function marcarDocumentoPermanenteRevisado()
{
    if (!isset($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'ID de documento requerido']);
        return;
    }

    ensureRole([ROLE_CAPTURISTA, ROLE_ADMIN]);

    $id = intval($_POST['id']);
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'ID de documento inválido']);
        return;
    }

    // Regla: no puedes aprobar si tú subiste (aplica igual para Capturista y Admin)
    $payload = requireAuth();
    $currentUserId = isset($payload['id']) ? intval($payload['id']) : 0;
    $currentRole = isset($payload['rol']) ? $payload['rol'] : ROLE_USUARIO;
    $adminEmpresa = new AdministradorEmpresa();
    $uploaderId = $adminEmpresa->obtenerIdUsuarioDocumentoPermanente($id);
    if ($uploaderId !== null && $uploaderId > 0 && $uploaderId === $currentUserId) {
        http_response_code(403);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'No puedes aprobar un documento que tú mismo subiste o reemplazaste. Debe hacerlo otro usuario con rol Capturista o Administrador.']);
        return;
    }
    $actualizado = $adminEmpresa->actualizarEstadoDocumentoPermanente($id, 'completado', $currentUserId);

    if (!$actualizado) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'No se pudo marcar el documento como completado.']);
        return;
    }

    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Revisión de documento completada.";
    $aviso->subMensaje = "El documento permanece actualizado.";
    echo json_encode($aviso);
}

function marcarUrgenteDocumentoPermanente()
{
    marcarArchivoFaltanteDocumentoPermanente();
}

function marcarDocumentoPermanenteRechazado()
{
    if (!isset($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'ID de documento requerido']);
        return;
    }

    ensureRole([ROLE_CAPTURISTA, ROLE_ADMIN]);

    $id = intval($_POST['id']);
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'ID de documento inválido']);
        return;
    }

    // Regla: no puedes rechazar si tú subiste (aplica igual para Capturista y Admin)
    $payload = requireAuth();
    $currentUserId = isset($payload['id']) ? intval($payload['id']) : 0;
    $currentRole = isset($payload['rol']) ? $payload['rol'] : ROLE_USUARIO;
    $adminEmpresa = new AdministradorEmpresa();
    $uploaderId = $adminEmpresa->obtenerIdUsuarioDocumentoPermanente($id);
    if ($uploaderId !== null && $uploaderId > 0 && $uploaderId === $currentUserId) {
        http_response_code(403);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'No puedes rechazar un documento que tú mismo subiste o reemplazaste. Debe hacerlo otro usuario con rol Capturista o Administrador.']);
        return;
    }
    $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : null;
    if ($observaciones === '') {
        $observaciones = null;
    } else if (mb_strlen($observaciones) > 500) {
        $observaciones = mb_substr($observaciones, 0, 500);
    }
    $actualizado = $adminEmpresa->actualizarEstadoDocumentoPermanente($id, 'rechazado', $currentUserId, $observaciones);

    if (!$actualizado) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'No se pudo marcar el documento como rechazado.']);
        return;
    }

    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Documento marcado como rechazado.";
    $aviso->subMensaje = "Se requiere reemplazar el archivo.";
    echo json_encode($aviso);
}

function reemplazarDocumentoPermanente()
{
    ensureRole([ROLE_CAPTURISTA, ROLE_ADMIN]);

    if (!isset($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'ID de documento requerido']);
        return;
    }
    if (!isset($_FILES['documento'])) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'Documento requerido']);
        return;
    }

    $id = intval($_POST['id']);
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'ID de documento inválido']);
        return;
    }

    $nuevoDocumento = procesarDocumentoPermanente(random_int(0, 1000000));
    if ($nuevoDocumento === "0") {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'No se pudo procesar el documento a reemplazar']);
        return;
    }

    $adminEmpresa = new AdministradorEmpresa();
    $payload = requireAuth();
    $userId = isset($payload['id']) ? intval($payload['id']) : null;
    $ok = $adminEmpresa->reemplazarDocumentoPermanente($id, $nuevoDocumento, $userId);

    if (!$ok) {
        http_response_code(400);
        echo json_encode(['estatus' => 'Error', 'mensaje' => 'No se pudo reemplazar el documento']);
        return;
    }

    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Documento reemplazado y puesto en revisión.";
    $aviso->subMensaje = "Ahora puedes completar la validación.";
    echo json_encode($aviso);
}

function agregarEstadoDeCuenta()
{
    $adminEmpresa = new AdministradorEmpresa();
    $empresa = $_POST['id'];
    $tipo = $_POST['tipo'];
    $documento = procesarEstadosCuenta(random_int(0, 1000000));
    $adminEmpresa->agregarEstadoCUenta($empresa, $documento, $tipo);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha agregado correctamente el documento: Estado de cuenta $empresa";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function eliminarEstadoDeCuenta()
{
    $id = $_POST['id'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->eliminarEstadoCuenta($id);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha eliminado correctamente el documento: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function agregarCaratula()
{
    $adminEmpresa = new AdministradorEmpresa();
    $empresa = $_POST['id'];
    $tipo = $_POST['tipo'];
    $documento = procesarCaratulas(random_int(0, 1000000));
    $adminEmpresa->agregarCaratula($empresa, $documento, $tipo);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha agregado correctamente el documento: Caratula $empresa";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function eliminarCaratula()
{
    $id = $_POST['id'];
    $adminEmpresa = new AdministradorEmpresa();
    $adminEmpresa->eliminarCaratula($id);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha eliminado correctamente el documento: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}
function agregarContraFiels()
{
    $adminEmpresa = new AdministradorEmpresa();
    $empresa = $_POST['id'];
    $contrasena = $_POST['contra'];
    $adminEmpresa->agregaContraFiel($empresa, $contrasena);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha agregado correctamente el documento: Contrafiel $empresa";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}


function agregarContraSello()
{
    $adminEmpresa = new AdministradorEmpresa();
    $empresa = $_POST['id'];
    $contrasena = $_POST['contra'];
    $adminEmpresa->agregaContraSelloSat($contrasena, $empresa);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha agregado correctamente el documento: ContraSello $empresa";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function agregarImms()
{
    $adminEmpresa = new AdministradorEmpresa();
    $empresa = $_POST['id'];
    $tipo = $_POST['tipo'];
    if ($tipo == "KEY") {
        $documento = procesarArchivoKey(random_int(0, 1000000));
        $adminEmpresa->agregarImms($documento, $empresa, $tipo);
        $aviso = new Aviso();
        $aviso->estatus = "Exito";
        $aviso->mensaje = "Se ha agregado correctamente el documento: KEY $empresa";
        $aviso->subMensaje = "Documento de la empresa ";
        echo json_encode($aviso);
    } else if ($tipo == "CSD") {
        $documento = procesarConstanciaPfx(random_int(0, 1000000));
        $adminEmpresa->agregarImms($documento, $empresa, "CER");
        $aviso = new Aviso();
        $aviso->estatus = "Exito";
        $aviso->mensaje = "Se ha agregado correctamente el documento: Sdg $empresa";
        $aviso->subMensaje = "Documento de la empresa ";
        echo json_encode($aviso);
    } else {
        $aviso = new Aviso();
        $aviso->estatus = "Error";
        $aviso->mensaje = "No se ha podido agregar el documento";
        $aviso->subMensaje = "No se ha podido agregar el documento archivo incompatible";
        echo json_encode($aviso);
        return;
    }
}

function eliminarImms()
{
    $adminEmpresa = new AdministradorEmpresa();
    $id = $_POST['id'];
    $adminEmpresa->eliminarImms($id);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha eliminado correctamente el documento: ";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function agregarContraImss()
{
    $adminEmpresa = new AdministradorEmpresa();
    $empresa = $_POST['id'];
    $contrasena = $_POST['contra'];
    $usuario = $_POST['usuario'];
    $tipo = "";
    if (isset($_POST['sipare'])) {
        $tipo .= $_POST['sipare'] . ",";
    }
    if (isset($_POST['idse'])) {
        $tipo .= $_POST['idse'] . ",";
    }
    if (isset($_POST['dos'])) {
        $tipo .= $_POST['dos'] . ",";
    }
    $tipo .= "pass";
    $adminEmpresa->agregaContraImss($contrasena, $empresa, $tipo, $usuario);
    $aviso = new Aviso();
    $aviso->estatus = "Exito";
    $aviso->mensaje = "Se ha agregado correctamente la contraseña para  $empresa";
    $aviso->subMensaje = "Documento de la empresa ";
    echo json_encode($aviso);
}

function postergarCampoEmpresa()
{
    $empresaId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $campo = isset($_POST['campo']) ? trim($_POST['campo']) : '';
    $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';

    if ($empresaId <= 0 || $campo === '') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Parámetros inválidos'
        ]);
        return;
    }

    if (mb_strlen($observaciones) > 500) {
        $observaciones = mb_substr($observaciones, 0, 500);
    }

    $camposPermitidos = [
        'comprobanteDomicilio',
        'constanciaSituacionFiscal',
        '32D',
        'telefono',
        'correoContacto',
        'sitioWeb',
    ];

    if (!in_array($campo, $camposPermitidos, true)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Campo no permitido'
        ]);
        return;
    }

    try {
        $adminPostergados = new AdministradorCamposPostergados();
        $totalActual = $adminPostergados->contarPostergaciones($empresaId, $campo);
        if ($totalActual >= 3) {
            $historial = $adminPostergados->dameHistorialPostergacion($empresaId, $campo);
            echo json_encode([
                'success' => false,
                'message' => 'Se alcanzó el número máximo de postergaciones para este campo.',
                'data' => [
                    'campo' => $campo,
                    'postergaciones' => [
                        'total' => $totalActual,
                        'maximo' => 3,
                        'restante' => 0,
                        'historial' => $historial,
                    ],
                ],
            ]);
            return;
        }

        $insertado = $adminPostergados->agregarCampoPostergado($empresaId, $campo, $observaciones);
        if (!$insertado) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo registrar la postergación.'
            ]);
            return;
        }

        $totalDespues = $adminPostergados->contarPostergaciones($empresaId, $campo);
        $historial = $adminPostergados->dameHistorialPostergacion($empresaId, $campo);

        echo json_encode([
            'success' => true,
            'message' => 'Campo postergado correctamente.',
            'data' => [
                'campo' => $campo,
                'postergaciones' => [
                    'total' => $totalDespues,
                    'maximo' => 3,
                    'restante' => max(0, 3 - $totalDespues),
                    'historial' => $historial,
                ],
            ],
        ]);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al registrar la postergación.'
        ]);
    }
}

switch ($accion) {
    case $casoAgregar:
        agregarEmpresa();
        break;
    case $casoEliminar:
        eliminarEmpresa();
        break;
    case $casoModificar:
        modificarEmpresa();
        break;
    case $casoModificarPdf:
        modificarPdf();
        break;
    case $casoSubirPdf:
        modificarPdf();
        break;
    case $casoModificarImagen:
        modificarImagen();
        break;

    case $casoNuevoCorreo:
        nuevoCorreo();
        break;
    case $casoEliminarCorreo:
        eliminarCorreo();
        break;
    case $casoFrecuancias:
        modificarFrecuencias();
        break;
    case $casoModComprobante:
        modificarComprobante();
        break;
    case $casoModConstanciaSf:
        modificarConstanciaSf();
        break;
    case $casoEspModificarNombre:
        modificarNombre();
        break;
    case $casoEspModificarRfc:
        modificarRfc();
        break;
    case $casoEspModificarDireccion:
        modificarDireccion();
        break;
    case $casoEspModificarCodigoPostal:
        modificarCodigoPostal();
        break;
    case $casoEspModificarEstado:
        modificarEstado();
        break;
    case $casoEspModificarRegimen:
        modificarRegimen();
        break;
    case $casoEspModificarFinDominio:
        modificarFinDominio();
        break;
    case $casoEspModificarTelefono:
        modificarTelefono();
        break;
    case $casoEspModificarCorreo:
        modificarCorreo();
        break;
    case $casoEspModificarDescripcion:
        modificarDescripcion();
        break;
    case $casoEspMoificarSitioWeb:
        modificarSitioWeb();
        break;
    case $casoAgregarCuenta:
        agregarCuenta();
        break;
    case $casoEliminarCuenta:
        eliminarCuenta();
        break;

    case $casoAgrearContraSat:
        agregarContraSat();
        break;
    case $casoEliminarContraSat:
        eliminarContraSat();
        break;

    case $casoAgregarCuentaIofact:
        agregarCuentaIofact();
        break;

    case $casoEliminarCuentaIofact:
        eliminarCuentaIofact();
        break;
    case $casoAgregarContraBanco:
        agregarContraBanco();
        break;

    case $casoEliminarContraBanco:
        eliminarContraBanco();
        break;
    case $casoAgregarDocumentoFiels:
        agregarDocumentoFiels();
        break;
    case $casoEliminarDocumentoFiels:
        eliminarDocumentoFiels();
        break;
    case $casoAgregarActaConst:
        agregarActaConst();
        break;
    case $casoEliminarActaConst:
        eliminarActaConst();
        break;
    case $casoAgregarDocumentoPermanente:
        agregarDocumentoPermanente();
        break;
    case $casoSolicitarDocumentoPermanente:
        solicitarDocumentoPermanente();
        break;
    case $casoEliminarDocumentoPermanente:
        eliminarDocumentoPermanente();
        break;
    case $casoMarcarUrgenteDocumentoPermanente:
    case $casoMarcarArchivoFaltanteDocumentoPermanente:
        marcarArchivoFaltanteDocumentoPermanente();
        break;
    case $casoMarcarDocumentoPermanenteRevisado:
        marcarDocumentoPermanenteRevisado();
        break;
    case $casoMarcarDocumentoPermanenteRechazado:
        marcarDocumentoPermanenteRechazado();
        break;
    case $casoReemplazarDocumentoPermanente:
        reemplazarDocumentoPermanente();
        break;
    case $casoAgregarSelloSat:
        agregarSelloSat();
        break;
    case $casoEliminarSelloSat:
        eliminarSellosSat();
        break;
    case $casoAgregarEstadoDeCuenta:
        agregarEstadoDeCuenta();
        break;
    case $casoEliminarEstadoDeCuenta:
        eliminarEstadoDeCuenta();
        break;
    case $casoAgregarCaratula:
        agregarCaratula();
        break;
    case $casoEliminarCaratula:
        eliminarCaratula();
        break;
    case $casoAgregarContraFiel:
        agregarContraFiels();
        break;
    case $casoAgregarContraSello:
        agregarContraSello();
        break;
    case $casoAgregarImss:
        agregarImms();
        break;
    case $casoEliminarImss:
        eliminarImms();
        break;
    case $casoAgregarContraImss:
        agregarContraImss();
        break;
    case $casoPostergarEmpresa:
        postergarCampoEmpresa();
        break;
    case 'nuevaFIEL':
        $adminEmpresa = new AdministradorEmpresa();
        $empresa = $_POST['id'];
        $tipo = $_POST['tipo'];
        // Procesar archivo subido
        $documento = '';
        $carpetaDestino = APP_ROOT . '/Documentos/Fiel/';
        if (!is_dir($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }
        if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = basename($_FILES['documento']['name']);
            $rutaDestino = $carpetaDestino . uniqid() . '_' . $nombreArchivo;
            if (move_uploaded_file($_FILES['documento']['tmp_name'], $rutaDestino)) {
                chmod($rutaDestino, 0640);
                // Guardar ruta relativa para la base de datos
                $documento = str_replace(APP_ROOT, '', $rutaDestino);
            } else {
                echo json_encode(['estatus' => 'Error', 'mensaje' => 'No se pudo guardar el archivo']);
                exit;
            }
        } else {
            echo json_encode(['estatus' => 'Error', 'mensaje' => 'Archivo no recibido']);
            exit;
        }
        $adminEmpresa->agregarFiel($empresa, $documento, $tipo);
        $aviso = new Aviso();
        $aviso->estatus = "Exito";
        $aviso->mensaje = "Se ha agregado correctamente el documento FIEL";
        $aviso->subMensaje = "Documento de la empresa ";
        echo json_encode($aviso);
        break;
    case 'eliminarFIEL':
        $id = $_POST['id'];
        $adminEmpresa = new AdministradorEmpresa();
        $adminEmpresa->eliminarFiel($id);
        $aviso = new Aviso();
        $aviso->estatus = "Exito";
        $aviso->mensaje = "Se ha eliminado correctamente el documento FIEL";
        $aviso->subMensaje = "Documento de la empresa ";
        echo json_encode($aviso);
        break;
}
