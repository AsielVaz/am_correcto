<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

/**
 * Adaptador compatible con los controladores heredados.
 * Mantiene mysqli_result como retorno y añade consultas preparadas para código nuevo.
 */
class conector
{
    protected string $servername;
    protected string $database;
    protected string $username;
    protected string $password;
    protected int $port;
    private ?mysqli $connection = null;

    public function __construct()
    {
        $this->servername = (string) env('DB_HOST', 'localhost');
        $this->database = (string) env('DB_DATABASE', 'am_dev');
        $this->username = (string) env('DB_USERNAME', 'root');
        $this->password = (string) env('DB_PASSWORD', '');
        $this->port = (int) env('DB_PORT', 3306);
    }

    protected function getConn(): mysqli
    {
        if ($this->connection instanceof mysqli) {
            return $this->connection;
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $this->connection = new mysqli(
                $this->servername,
                $this->username,
                $this->password,
                $this->database,
                $this->port
            );
            $this->connection->set_charset((string) env('DB_CHARSET', 'utf8mb4'));
        } catch (mysqli_sql_exception $exception) {
            error_log('Database connection failed: ' . $exception->getMessage());
            throw new RuntimeException('No fue posible conectar con la base de datos.');
        }

        return $this->connection;
    }

    public function ejecutar(string $query): mysqli_result|bool
    {
        try {
            return $this->getConn()->query($query);
        } catch (mysqli_sql_exception $exception) {
            error_log('Database query failed: ' . $exception->getMessage());
            throw new RuntimeException('No fue posible completar la operación solicitada.');
        }
    }

    public function preparar(string $query, array $params = [], string $types = ''): mysqli_result|bool
    {
        try {
            $statement = $this->getConn()->prepare($query);
            if ($params !== []) {
                $types = $types !== '' ? $types : $this->inferTypes($params);
                $statement->bind_param($types, ...$params);
            }
            $statement->execute();
            $result = $statement->get_result();
            return $result ?: true;
        } catch (mysqli_sql_exception $exception) {
            error_log('Prepared database query failed: ' . $exception->getMessage());
            throw new RuntimeException('No fue posible completar la operación solicitada.');
        }
    }

    public function ultimoId(): int
    {
        return $this->getConn()->insert_id;
    }

    public function filasAfectadas(): int
    {
        return $this->getConn()->affected_rows;
    }

    public function iniciarTransaccion(): void
    {
        $this->getConn()->begin_transaction();
    }

    public function confirmarTransaccion(): void
    {
        $this->getConn()->commit();
    }

    public function revertirTransaccion(): void
    {
        $this->getConn()->rollback();
    }

    private function inferTypes(array $params): string
    {
        return implode('', array_map(static fn (mixed $value): string => match (true) {
            is_int($value), is_bool($value) => 'i',
            is_float($value) => 'd',
            default => 's',
        }, $params));
    }

    public function __destruct()
    {
        if ($this->connection instanceof mysqli) {
            $this->connection->close();
        }
    }
}
