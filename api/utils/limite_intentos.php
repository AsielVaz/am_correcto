<?php
// Limita los intentos de login por usuario (email) y por IP
include_once __DIR__ . '/../config/conectorBD.php';
class LimiteIntentos extends conector
{
    // Máximo 5 intentos en 60 minutos
    private $maxIntentos = 5;
    private $minutos = 60;

    public function excedido($usuario, $ip)
    {
        $usuario = (int) $usuario;
        $sql = "SELECT COUNT(*) AS total FROM login_log WHERE (usuario = ? OR ip = ?) AND exito = 0 AND fecha > (NOW() - INTERVAL {$this->minutos} MINUTE)";
        $res = $this->preparar($sql, [$usuario, (string) $ip], 'is');
        if ($res && $row = $res->fetch_assoc()) {
            return $row['total'] >= $this->maxIntentos;
        }
        return false;
    }
}
