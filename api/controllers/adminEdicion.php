<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/conectorBD.php';
require_once __DIR__ . '/../utils/encriptador.php';

final class AdministradorEdicion extends conector
{
    public const EMPRESA = ['razon' => 150, 'rfc' => 50, 'telefono' => 20, 'correo' => 100,
        'sitio_web' => 100, 'calle' => 150, 'numero' => 50, 'colonia' => 70, 'cp' => 70,
        'estado' => 70, 'regimen' => 200];
    private const ACCESOS = [
        'banco' => ['contras_banco', 'fec_actualizacion', ['usuario' => 256, 'banco' => 128], ['contrasena', 'clave_op', 'nip']],
        'sat' => ['contras_sat', 'fec_actualisacion', ['usuario' => 256], ['contrasena', 'rfc']],
        'iofacturo' => ['contras_iofac', 'fec_actualizacion', ['usuario' => 100], ['contrasena', 'rfc']],
        'correo' => ['correos', null, ['correo' => 100], []],
        'cuenta' => ['cuentas_banco', null, ['banco' => 100, 'cuenta' => 50, 'clave' => 100, 'moneda' => 50], []],
    ];

    public function buscar(string $search, int $page): array
    {
        $pattern = '%' . $search . '%';
        $where = ' WHERE razon LIKE ? OR rfc LIKE ?';
        $total = (int) $this->preparar('SELECT COUNT(*) AS total FROM empresas' . $where, [$pattern, $pattern])->fetch_assoc()['total'];
        $pages = max(1, (int) ceil($total / 20));
        $page = min($page, $pages);
        $data = $this->preparar('SELECT id, razon, rfc FROM empresas' . $where . ' ORDER BY razon, id LIMIT 20 OFFSET ?', [$pattern, $pattern, ($page - 1) * 20])->fetch_all(MYSQLI_ASSOC);
        return compact('data', 'total', 'page', 'pages');
    }

    public function detalle(int $id): array
    {
        $empresa = $this->preparar('SELECT id, ' . implode(', ', array_keys(self::EMPRESA)) . ' FROM empresas WHERE id = ?', [$id])->fetch_assoc();
        if (!$empresa) {
            throw new OutOfBoundsException('La empresa no existe.');
        }
        $accesos = [];
        foreach (self::ACCESOS as $tipo => [$table, $date, $fields, $secrets]) {
            $owner = in_array($tipo, ['correo', 'cuenta'], true) ? 'empresa' : 'id_empresa';
            $ownerValue = $tipo === 'cuenta' ? $empresa['razon'] : $id;
            // Nunca enviar contraseñas ni sus valores cifrados al navegador.
            $accesos[$tipo] = $this->preparar('SELECT id, ' . implode(', ', array_keys($fields)) . " FROM $table WHERE $owner = ? ORDER BY id", [$ownerValue])->fetch_all(MYSQLI_ASSOC);
        }
        return compact('empresa', 'accesos');
    }

    private function validar(string $field, mixed $value, int $max): string
    {
        if (!is_string($value) || mb_strlen($value) > $max) {
            throw new InvalidArgumentException("El campo $field debe contener como máximo $max caracteres.");
        }
        $value = trim($value);
        if ($field === 'correo' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Escribe un correo válido.');
        }
        if ($field === 'telefono' && $value !== '' && !preg_match('/^\d{10}$/', $value)) {
            throw new InvalidArgumentException('El teléfono debe contener 10 dígitos.');
        }
        if ($field === 'sitio_web' && $value !== '' && (!filter_var($value, FILTER_VALIDATE_URL) || !in_array(strtolower((string) parse_url($value, PHP_URL_SCHEME)), ['http', 'https'], true))) {
            throw new InvalidArgumentException('Escribe un sitio web válido con http:// o https://.');
        }
        return $value;
    }

    public function guardar(int $empresaId, string $tipo, int $registroId, array $values): void
    {
        $this->iniciarTransaccion();
        try {
            $old = $this->preparar('SELECT id, ' . implode(', ', array_keys(self::EMPRESA)) . ' FROM empresas WHERE id = ? FOR UPDATE', [$empresaId])->fetch_assoc();
            if (!$old) throw new OutOfBoundsException('La empresa no existe.');
            $sets = [];
            $params = [];
            if ($tipo === 'empresa') {
                $table = 'empresas';
                $where = 'id = ?';
                $ids = [$empresaId];
                $fields = self::EMPRESA;
                $secrets = [];
                $date = null;
            } else {
                if (!isset(self::ACCESOS[$tipo])) throw new InvalidArgumentException('Tipo de edición inválido.');
                [$table, $date, $fields, $secrets] = self::ACCESOS[$tipo];
                $owner = in_array($tipo, ['correo', 'cuenta'], true) ? 'empresa' : 'id_empresa';
                $where = "id = ? AND $owner = ?";
                $ids = [$registroId, $tipo === 'cuenta' ? $old['razon'] : $empresaId];
                if (!$this->preparar("SELECT id FROM $table WHERE $where FOR UPDATE", $ids)->fetch_assoc()) {
                    throw new OutOfBoundsException('El registro no pertenece a la empresa seleccionada.');
                }
            }
            foreach ($fields as $field => $max) {
                if (!array_key_exists($field, $values)) continue;
                $value = $this->validar($field, $values[$field], $max);
                if ($field === 'razon' && $value === '') throw new InvalidArgumentException('La razón social es obligatoria.');
                if ($tipo === 'correo' && $value === '') throw new InvalidArgumentException('El correo es obligatorio.');
                $sets[] = "$field = ?";
                $params[] = $value;
            }
            foreach ($secrets as $field) {
                $value = $values[$field] ?? '';
                if (!is_string($value) || strlen($value) > 159) throw new InvalidArgumentException("El campo $field admite hasta 159 bytes.");
                if ($value === '') continue;
                $sets[] = "$field = ?";
                $params[] = (new Encriptador())->encriptar($value);
            }
            if (!$sets) throw new InvalidArgumentException('No se recibieron campos para editar.');
            if ($date) $sets[] = "$date = NOW()";
            if ($tipo === 'empresa') {
                // La relación heredada de cuentas bancarias utiliza la razón social.
                if (isset($values['razon']) && trim($values['razon']) !== $old['razon']) {
                    $duplicada = $this->preparar('SELECT id FROM empresas WHERE id <> ? AND (razon = ? OR razon = ?) LIMIT 1', [$empresaId, $old['razon'], trim($values['razon'])])->fetch_assoc();
                    if ($duplicada) throw new InvalidArgumentException('La razón social coincide con otra empresa. No se puede reasignar la información bancaria de forma segura.');
                    $this->preparar('UPDATE cuentas_banco SET empresa = ? WHERE empresa = ?', [trim($values['razon']), $old['razon']]);
                }
                foreach (['telefono' => ['periodo_telefono', 'telefono'], 'correo' => ['periodo_correo', 'correoContacto'], 'sitio_web' => ['periodo_sitiow', 'sitioWeb']] as $field => [$period, $postergado]) {
                    if (isset($values[$field]) && trim($values[$field]) !== (string) $old[$field]) {
                        $sets[] = "$period = ?";
                        $params[] = trim($values[$field]) === '' ? null : date('Y-m-d', strtotime('+1 year'));
                        $this->preparar('DELETE FROM campos_postergados WHERE id_empresa = ? AND campo_a_postergar = ?', [$empresaId, $postergado]);
                    }
                }
            }
            $this->preparar("UPDATE $table SET " . implode(', ', $sets) . " WHERE $where", array_merge($params, $ids));
            $this->confirmarTransaccion();
        } catch (Throwable $error) {
            $this->revertirTransaccion();
            throw $error;
        }
    }
}
