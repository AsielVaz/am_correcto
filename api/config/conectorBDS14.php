<?php

require_once dirname(__DIR__, 2) . '/bootstrap.php';

class conectorS14
{
    private $servername;
    private $database;
    private $username;
    private $password;
    public function __construct()
    {
        $this->servername = (string) env('S14_DB_HOST', '');
        $this->database = (string) env('S14_DB_DATABASE', '');
        $this->username = (string) env('S14_DB_USERNAME', '');
        $this->password = (string) env('S14_DB_PASSWORD', '');
    }
    public function ejecutar($query)
    {
        $conn = mysqli_connect($this->servername, $this->username, $this->password, $this->database, (int) env('S14_DB_PORT', 3306));
        if (!$conn) {
            throw new RuntimeException('No fue posible conectar con la base secundaria.');
        }
        mysqli_set_charset($conn, 'utf8mb4');
        $result = mysqli_query($conn, $query);
        return $result;
    }
}
