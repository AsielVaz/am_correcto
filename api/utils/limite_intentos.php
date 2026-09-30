<?php
// Limita los intentos de login por usuario (email) y por IP
include_once __DIR__ . '/../config/conectorBD.php';
class LimiteIntentos extends conector
{
	private int $maxIntentosUsuario = 5;
	private int $maxIntentosIp = 25;
	private int $minutos = 60;

    public function excedido($usuario, $ip)
    {
		try {
			$usuario = (int) $usuario;
			$sql = "SELECT
				SUM(CASE WHEN usuario = ? THEN 1 ELSE 0 END) AS total_usuario,
				SUM(CASE WHEN ip = ? THEN 1 ELSE 0 END) AS total_ip
				FROM login_log
				WHERE exito = 0
				AND (evento IS NULL OR evento <> 'ban')
				AND fecha > (NOW() - INTERVAL {$this->minutos} MINUTE)";
			$res = $this->preparar($sql, [$usuario, (string) $ip], 'is');
			if ($res instanceof mysqli_result && ($row = $res->fetch_assoc())) {
				$totalUsuario = (int) ($row['total_usuario'] ?? 0);
				$totalIp = (int) ($row['total_ip'] ?? 0);
				return ($usuario > 0 && $totalUsuario >= $this->maxIntentosUsuario)
					|| $totalIp >= $this->maxIntentosIp;
			}
		} catch (Throwable $exception) {
			// El registro de intentos es una protección auxiliar; su ausencia no debe bloquear usuarios válidos.
			error_log('Login rate limiter unavailable: ' . $exception->getMessage());
        }
        return false;
    }
}
