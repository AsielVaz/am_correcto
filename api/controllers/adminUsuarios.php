<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conectorBD.php';

class Usuario
{
    public int $id = 0;
    public ?string $nombre = null;
    public ?string $apellidoPaterno = null;
    public ?string $apellidoMaterno = null;
    public ?string $email = null;
    public ?string $constrasena = null;
    public ?string $telefono = null;
    public ?string $calle = null;
    public ?string $ciudad = null;
    public ?string $pais = null;
    public ?string $direccion = null;
    public int $tipoUsuario = 0;
    public string $rol = 'Usuario';
    public ?string $departamento = null;
    public ?string $imagen = null;
    public string|array|null $permisos = null;
    public int $activo = 1;
}

class Recuperacion
{
    public int $id = 0;
    public ?int $idUsuario = null;
    public ?string $token = null;
    public ?string $fecha = null;
}

class AdministradorUsuario extends conector
{
    private const SELECT_COLUMNS = 'id, nombre, apellido_paterno, apellido_materno, email, contrasena, telefono, calle, ciudad, pais, direccion, tipo_usuario, rol, departamento, imagen, permiso, activo';

    public function insertaUsuario($nombre, $apellidoPaterno, $apellidoMaterno, $email, $contrasena, $telefono, $calle, $ciudad, $pais, $direccion, $tipoUsuario, $rol, $departamento, $empleado, $guid, $permisos): void
    {
        $sql = 'INSERT INTO usuario (nombre, apellido_paterno, apellido_materno, email, contrasena, telefono, calle, ciudad, pais, direccion, tipo_usuario, rol, departamento, empleado, token_administrador, permiso) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $this->preparar($sql, [
            (string) $nombre, (string) $apellidoPaterno, (string) $apellidoMaterno,
            strtolower(trim((string) $email)), password_hash((string) $contrasena, PASSWORD_DEFAULT),
            (string) $telefono, (string) $calle, (string) $ciudad, (string) $pais, (string) $direccion,
            (int) $tipoUsuario, $this->normalizarRol((string) $rol), $departamento ?: null,
            (int) $empleado, (string) $guid, (string) $permisos,
        ]);
    }

    public function insertaRecuperacion($idUsuario, $token, $fecha): void
    {
        $this->preparar('INSERT INTO recuperacion (id_usuario, token, fecha) VALUES (?, ?, ?)', [(int) $idUsuario, (string) $token, (string) $fecha], 'iss');
    }

    public function dameRecuperacion($token): Recuperacion
    {
        $item = new Recuperacion();
        $result = $this->preparar('SELECT id, id_usuario, token, fecha FROM recuperacion WHERE token = ? ORDER BY fecha DESC LIMIT 1', [(string) $token], 's');
        if ($result instanceof mysqli_result && ($row = $result->fetch_assoc())) {
            $item->id = (int) $row['id'];
            $item->idUsuario = (int) $row['id_usuario'];
            $item->token = $row['token'];
            $item->fecha = $row['fecha'];
        }
        return $item;
    }

    public function dameUsuario($email, $pass): Usuario
    {
        $result = $this->preparar('SELECT ' . self::SELECT_COLUMNS . ' FROM usuario WHERE email = ? LIMIT 1', [strtolower(trim((string) $email))], 's');
        if (!$result instanceof mysqli_result || !($row = $result->fetch_assoc())) {
            return new Usuario();
        }

        $hash = (string) ($row['contrasena'] ?? '');
        $legacyValid = strlen($hash) === 40 && hash_equals($hash, sha1((string) $pass));
        $modernValid = password_get_info($hash)['algo'] !== null && password_verify((string) $pass, $hash);
        if (!$legacyValid && !$modernValid) {
            return new Usuario();
        }
        if ((int) ($row['activo'] ?? 1) === 0) {
            $inactive = new Usuario();
            $inactive->id = -1;
            return $inactive;
        }
        if ($legacyValid || password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            $this->preparar('UPDATE usuario SET contrasena = ? WHERE id = ?', [password_hash((string) $pass, PASSWORD_DEFAULT), (int) $row['id']], 'si');
        }
        return $this->hidratar($row);
    }

    public function existeUsuario($email): int|false
    {
        $result = $this->preparar('SELECT id FROM usuario WHERE email = ? LIMIT 1', [strtolower(trim((string) $email))], 's');
        $row = $result instanceof mysqli_result ? $result->fetch_assoc() : null;
        return $row ? (int) $row['id'] : false;
    }

    public function mataToken($token): void
    {
        $this->preparar('DELETE FROM recuperacion WHERE token = ?', [(string) $token], 's');
    }

    public function dameUsuarioId($id): Usuario
    {
        $result = $this->preparar('SELECT ' . self::SELECT_COLUMNS . ' FROM usuario WHERE id = ? LIMIT 1', [(int) $id], 'i');
        $row = $result instanceof mysqli_result ? $result->fetch_assoc() : null;
        return $row ? $this->hidratar($row) : new Usuario();
    }

    public function eliminaUsuario($id): void
    {
        $this->preparar('DELETE FROM usuario WHERE id = ?', [(int) $id], 'i');
    }

    public function deshabilitaUsuario($id): void
    {
        $this->preparar('UPDATE usuario SET activo = 0 WHERE id = ?', [(int) $id], 'i');
    }

    public function buscaUsuario($buscar): array
    {
        $term = '%' . trim((string) $buscar) . '%';
        $result = $this->preparar('SELECT ' . self::SELECT_COLUMNS . ' FROM usuario WHERE nombre LIKE ? OR apellido_materno LIKE ? OR apellido_paterno LIKE ? OR telefono LIKE ? OR email LIKE ? ORDER BY id DESC', [$term, $term, $term, $term, $term], 'sssss');
        return $this->hidratarLista($result);
    }

    public function dameUsuarios(): array
    {
        return $this->hidratarLista($this->ejecutar('SELECT ' . self::SELECT_COLUMNS . ' FROM usuario ORDER BY id DESC'), true);
    }

    public function actualizarUsuario($id, $nombre, $apellidoPaterno, $apellidoMaterno, $email, $telefono, $calle, $ciudad, $pais, $direccion, $permisos, $rol, $departamento, $activo = 1): void
    {
        $this->preparar('UPDATE usuario SET nombre = ?, apellido_paterno = ?, apellido_materno = ?, email = ?, permiso = ?, rol = ?, departamento = ?, activo = ? WHERE id = ?', [(string) $nombre, (string) $apellidoPaterno, (string) $apellidoMaterno, strtolower(trim((string) $email)), (string) $permisos, $this->normalizarRol((string) $rol), $departamento ?: null, (int) (bool) $activo, (int) $id]);
    }

    public function actualizarContrasena($id, $contrasena): void
    {
        $this->preparar('UPDATE usuario SET contrasena = ? WHERE id = ?', [password_hash((string) $contrasena, PASSWORD_DEFAULT), (int) $id], 'si');
    }

    public function actualizarDireccion($id, $email, $calle, $ciudad, $pais, $direccion): void
    {
        $this->preparar('UPDATE usuario SET calle = ?, email = ?, ciudad = ?, pais = ?, direccion = ? WHERE id = ?', [(string) $calle, strtolower(trim((string) $email)), (string) $ciudad, (string) $pais, (string) $direccion, (int) $id], 'sssssi');
    }

    public function asignarImagen($id, $imagen): void
    {
        $this->preparar('UPDATE usuario SET imagen = ? WHERE id = ?', [(string) $imagen, (int) $id], 'si');
    }

    public function acenderUsuario($id): void
    {
        $this->preparar('UPDATE usuario SET tipo_usuario = 100 WHERE id = ?', [(int) $id], 'i');
    }

    public function desenderUsuario($id): void
    {
        $this->preparar('UPDATE usuario SET tipo_usuario = 0 WHERE id = ?', [(int) $id], 'i');
    }

    public function modificaProducto(): void
    {
    }

    private function hidratarLista(mysqli_result|bool $result, bool $permissionArray = false): array
    {
        $usuarios = [];
        if (!$result instanceof mysqli_result) {
            return $usuarios;
        }
        while ($row = $result->fetch_assoc()) {
            $usuario = $this->hidratar($row);
            if ($permissionArray) {
                $usuario->permisos = array_values(array_filter(explode(',', (string) $usuario->permisos)));
            }
            $usuarios[] = $usuario;
        }
        return $usuarios;
    }

    private function hidratar(array $row): Usuario
    {
        $usuario = new Usuario();
        $usuario->id = (int) $row['id'];
        $usuario->nombre = $row['nombre'] ?? null;
        $usuario->apellidoPaterno = $row['apellido_paterno'] ?? null;
        $usuario->apellidoMaterno = $row['apellido_materno'] ?? null;
        $usuario->email = $row['email'] ?? null;
        $usuario->telefono = $row['telefono'] ?? null;
        $usuario->calle = $row['calle'] ?? null;
        $usuario->ciudad = $row['ciudad'] ?? null;
        $usuario->pais = $row['pais'] ?? null;
        $usuario->direccion = $row['direccion'] ?? null;
        $usuario->tipoUsuario = (int) ($row['tipo_usuario'] ?? 0);
        $usuario->rol = $this->normalizarRol((string) ($row['rol'] ?? 'Usuario'));
        $usuario->departamento = $row['departamento'] ?? null;
        $usuario->imagen = $row['imagen'] ?? null;
        $usuario->permisos = $row['permiso'] ?? '';
        $usuario->activo = (int) ($row['activo'] ?? 1);
        return $usuario;
    }

    private function normalizarRol(string $rol): string
    {
        return match (strtolower(trim($rol))) {
            'admin', 'administrador' => 'Admin',
            'capturista' => 'Capturista',
            default => 'Usuario',
        };
    }
}
