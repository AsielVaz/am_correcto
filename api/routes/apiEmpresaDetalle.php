<?php
include_once('../utils/auth_guard.php');

$payload = requireAuth();

header('Content-Type: application/json');
include_once('../controllers/adminEmpresas.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$clave = isset($_GET['clave']) ? $_GET['clave'] : null;

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID requerido']);
    exit;
}

// Obtener razón social de la empresa por ID
$admin = new AdministradorEmpresa();
$empresa = null;
$empresas = $admin->dameEmpresas();
foreach ($empresas as $e) {
    if ($e->id == $id) {
        $empresa = $e;
        break;
    }
}
// Datos secundarios y relaciones
$data = [
    // Cuentas bancarias SOLO de la empresa seleccionada
    'cuentas' => $empresa ? $admin->dameCuentaPorEmpresa($empresa->razon) : [],
    // Correos
    'correos' => $admin->dameCorreosPorEmpresa($id),
    // Documentos y relaciones
    // Mapear documento_1 a documento para carátulas
    'caratulas' => array_map(function ($caratula) {
        if (isset($caratula->documento1) && !isset($caratula->documento)) {
            $caratula->documento = $caratula->documento1;
        }
        return $caratula;
    }, $admin->dameCaratulasPorEmpresa($id)),
    // Mapear documento_1 a documento para estados de cuenta
    'estadosCuenta' => array_map(function ($estado) {
        if (isset($estado->documento1) && !isset($estado->documento)) {
            $estado->documento = $estado->documento1;
        }
        return $estado;
    }, $admin->dameEstadosDeCuentaPorEmpresa($id)),
    'actas' => $admin->dameActasConstitutivasPorEmpresa($id, $clave),
    'poderes' => $admin->dameDocumentoPoderPorEmpresa($id, $clave),
    'ineRep' => $admin->dameIneRepPorEmpresa($id, $clave),
    'ineSocio' => $admin->dameIneSocioPorEmpresa($id, $clave),
    'consSocio' => $admin->dameConsSocioPorEmpresa($id, $clave),
    'consRep' => $admin->dameConsRepPorEmpresa($id, $clave),
    // Documentos permanentes: solo de la empresa seleccionada, mapeando documento y enriqueciendo con nombres
    'documentosPermanentes' => (function () use ($admin, $id, $clave, $payload) {
        $docsObj = array_filter($admin->dameDocumentosPPorEmpresa($id, $clave), function ($doc) use ($id) {
            return isset($doc->empresa) && $doc->empresa == $id;
        });
        $currentUserId = isset($payload['id']) ? (int)$payload['id'] : 0;
        $currentRole = isset($payload['rol']) ? $payload['rol'] : 'Usuario';
        $canRoleReview = in_array($currentRole, ['Capturista', 'Admin'], true);

        // Verificar columnas con SHOW COLUMNS
        $hasUploader = false;
        $hasRevisor = false;
        $rsC1 = $admin->ejecutar("SHOW COLUMNS FROM `actas_const` LIKE 'id_usuario'");
        if ($rsC1 instanceof \mysqli_result && $rsC1->num_rows > 0) {
            $hasUploader = true;
        }
        $rsC2 = $admin->ejecutar("SHOW COLUMNS FROM `actas_const` LIKE 'id_usuario_revisor'");
        if ($rsC2 instanceof \mysqli_result && $rsC2->num_rows > 0) {
            $hasRevisor = true;
        }

        $docs = [];
        if ($hasUploader || $hasRevisor) {
            $idsList = [];
            foreach ($docsObj as $d) {
                if (isset($d->id)) {
                    $idsList[] = (int)$d->id;
                }
            }
            $docUser = [];
            if (!empty($idsList)) {
                $idsStr = implode(',', array_unique($idsList));
                $sql = "SELECT id, id_usuario, id_usuario_revisor FROM actas_const WHERE id IN ($idsStr)";
                $rs = $admin->ejecutar($sql);
                if ($rs) {
                    while ($row = mysqli_fetch_assoc($rs)) {
                        $docUser[(int)$row['id']] = [
                            'uploader' => isset($row['id_usuario']) ? (int)$row['id_usuario'] : null,
                            'revisor' => isset($row['id_usuario_revisor']) ? (int)$row['id_usuario_revisor'] : null,
                        ];
                    }
                }
                $userIds = [];
                foreach ($docUser as $info) {
                    if (!empty($info['uploader'])) $userIds[] = (int)$info['uploader'];
                    if (!empty($info['revisor'])) $userIds[] = (int)$info['revisor'];
                }
                $userIds = array_values(array_unique($userIds));
                $userNameMap = [];
                if (!empty($userIds)) {
                    $userIdsStr = implode(',', $userIds);
                    $sqlU = "SELECT id, nombre, apellido_paterno, apellido_materno FROM usuario WHERE id IN ($userIdsStr)";
                    $rsU = $admin->ejecutar($sqlU);
                    if ($rsU) {
                        while ($u = mysqli_fetch_assoc($rsU)) {
                            $nombre = trim(($u['nombre'] ?? '') . ' ' . ($u['apellido_paterno'] ?? '') . ' ' . ($u['apellido_materno'] ?? ''));
                            $userNameMap[(int)$u['id']] = $nombre ?: ('Usuario #' . (int)$u['id']);
                        }
                    }
                }
                foreach ($docsObj as $d) {
                    $row = [
                        'id' => $d->id,
                        'empresa' => $d->empresa,
                        'documento' => $d->documento,
                        'fechaCreacion' => $d->fechaCreacion,
                        'fechaSubio' => $d->fechaCreacion,
                        'fechaVerificacion' => isset($d->fechaRevision) ? $d->fechaRevision : null,
                        'fechaActualizacion' => isset($d->fechaActualizacion) ? $d->fechaActualizacion : null,
                        'contenido' => isset($d->contenido) ? $d->contenido : null,
                        'estadoDocumento' => isset($d->estadoDocumento) ? $d->estadoDocumento : null,
                        'observaciones' => isset($d->observaciones) ? $d->observaciones : null,
                        'nombreUsuarioSubio' => null,
                        'nombreUsuarioReviso' => null,
                        'idUsuarioSubio' => null,
                        'idUsuarioReviso' => null,
                        'puedeRevisar' => $canRoleReview, // provisional; se ajusta abajo si hay info de uploader
                        'puedeReemplazar' => $canRoleReview, // se ajusta abajo si tú subiste (también permitido)
                    ];
                    $dId = (int)$d->id;
                    if (isset($docUser[$dId])) {
                        $uId = $docUser[$dId]['uploader'] ?? null;
                        $rId = $docUser[$dId]['revisor'] ?? null;
                        if ($uId) {
                            $row['idUsuarioSubio'] = (int)$uId;
                        }
                        if ($rId) {
                            $row['idUsuarioReviso'] = (int)$rId;
                        }
                        if ($uId && isset($userNameMap[$uId])) {
                            $row['nombreUsuarioSubio'] = $userNameMap[$uId];
                        }
                        if ($rId && isset($userNameMap[$rId])) {
                            $row['nombreUsuarioReviso'] = $userNameMap[$rId];
                        }
                        // Regla UI: no puedes revisar si tú subiste
                        if ($uId && (int)$uId === $currentUserId) {
                            $row['puedeRevisar'] = false; // no auto-revisión
                            $row['puedeReemplazar'] = true; // sí puede reemplazar si está rechazado
                        }
                    } else {
                        // Sin info de uploader: mantener puedeRevisar según rol
                        $row['puedeRevisar'] = $canRoleReview;
                        $row['puedeReemplazar'] = $canRoleReview;
                    }
                    $docs[] = $row;
                }
            } else {
                foreach ($docsObj as $d) {
                    $docs[] = [
                        'id' => $d->id,
                        'empresa' => $d->empresa,
                        'documento' => $d->documento,
                        'fechaCreacion' => $d->fechaCreacion,
                        'fechaSubio' => $d->fechaCreacion,
                        'fechaVerificacion' => isset($d->fechaRevision) ? $d->fechaRevision : null,
                        'fechaActualizacion' => isset($d->fechaActualizacion) ? $d->fechaActualizacion : null,
                        'contenido' => isset($d->contenido) ? $d->contenido : null,
                        'estadoDocumento' => isset($d->estadoDocumento) ? $d->estadoDocumento : null,
                        'observaciones' => isset($d->observaciones) ? $d->observaciones : null,
                        'nombreUsuarioSubio' => null,
                        'nombreUsuarioReviso' => null,
                        'idUsuarioSubio' => null,
                        'idUsuarioReviso' => null,
                        'puedeRevisar' => $canRoleReview,
                        'puedeReemplazar' => $canRoleReview,
                    ];
                }
            }
        } else {
            foreach ($docsObj as $d) {
                $docs[] = [
                    'id' => $d->id,
                    'empresa' => $d->empresa,
                    'documento' => $d->documento,
                    'fechaCreacion' => $d->fechaCreacion,
                    'fechaSubio' => $d->fechaCreacion,
                    'fechaVerificacion' => isset($d->fechaRevision) ? $d->fechaRevision : null,
                    'fechaActualizacion' => isset($d->fechaActualizacion) ? $d->fechaActualizacion : null,
                    'contenido' => isset($d->contenido) ? $d->contenido : null,
                    'estadoDocumento' => isset($d->estadoDocumento) ? $d->estadoDocumento : null,
                    'nombreUsuarioSubio' => null,
                    'nombreUsuarioReviso' => null,
                    'idUsuarioSubio' => null,
                    'idUsuarioReviso' => null,
                    'puedeRevisar' => $canRoleReview,
                    'puedeReemplazar' => $canRoleReview,
                ];
            }
        }
        return array_values($docs);
    })(),
    'rppc' => $admin->dameRppcPorEmpresa($id, $clave),
    'asamblea' => $admin->dameAsambleaPorEmpresa($id, $clave),
    // Contraseñas y accesos
    'contrasSat' => $admin->dameContrasSatPorEmpresa($id, $clave),
    'contrasIofacturo' => $admin->dameCuentasIofacturo($id, $clave),
    'contrasBanco' => $admin->dameContrasBanco($id, $clave),
    // FIEL y sellos (normalizar a arrays y mapear documento_1 a documento)
    'fiel' => array_map(function ($fiel) {
        // Preservar todos los campos originales y normalizar 'documento'
        $row = is_object($fiel) ? get_object_vars($fiel) : (array)$fiel;
        if (!isset($row['documento']) && isset($row['documento1'])) {
            $row['documento'] = $row['documento1'];
        }
        return $row;
    }, $admin->dameFielPorEmpresa($id, $clave)),
    'sellosSat' => array_map(function ($sello) {
        // Preservar todos los campos originales y normalizar 'documento'
        $row = is_object($sello) ? get_object_vars($sello) : (array)$sello;
        if (!isset($row['documento']) && isset($row['documento1'])) {
            $row['documento'] = $row['documento1'];
        }
        return $row;
    }, $admin->dameSelosSatPorEmpresa($id, $clave)),
    'contraFiel' => $admin->dameContraFiel($id),
    'contraSelloSat' => $admin->dameContraSelloSat($id),
    // IMSS
    // Mapear documento a documento para IMSS (ya viene como documento, pero aseguramos)
    'imss' => array_map(function ($imss) {
        if (isset($imss->documento)) {
            $imss->documento = $imss->documento;
        }
        return $imss;
    }, $admin->dameImssPorempresa($id)),
    'contraImss' => $admin->dameContraImssEmpresa($id),
];

$response = [
    'success' => true,
    'data' => $data
];
echo json_encode($response);
