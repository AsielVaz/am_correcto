<?php
include_once("../config/conectorBD.php");
class AdminLoginLog extends conector
{
	public function registrar($usuario, $ip, $exito, $evento = null): bool
    {
		try {
			$this->preparar(
				'INSERT INTO login_log (usuario, ip, fecha, exito, evento) VALUES (?, ?, NOW(), ?, ?)',
				[$usuario === null ? null : (int) $usuario, (string) $ip, (int) $exito, $evento],
				'isis'
			);
			return true;
		} catch (Throwable $exception) {
			error_log('Login audit unavailable: ' . $exception->getMessage());
			return false;
		}
    }
}
