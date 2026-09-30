<?php
class Encriptador
{
    private $clave;
    private $metodo;
    private $iv;

    // Constructor para inicializar la clave, el método y el vector de inicialización
    function __construct($metodo = "aes-256-cbc")
    {
        $this->clave = "10h-2023-fdv.xc{fd}ertq.-dfvcr5,.dfs|dfblerc,.sdfb";
        $this->metodo = $metodo;
        $this->iv = random_bytes(16);
    }

    // Método para encriptar texto
    function encriptar($plaintext)
    {
        $iv = random_bytes(16);
        $ciphertext = openssl_encrypt($plaintext, 'AES-256-CBC', $this->clave, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $ciphertext);
    }

    // Método para desencriptar texto
    function desencriptar($ciphertext, $clave = "10h-2023-fdv.xc{fd}ertq.-dfvcr5,.dfs|dfblerc,.sdfb")
    {

        $ciphertext = base64_decode($ciphertext);
        $iv = substr($ciphertext, 0, 16);
        $ciphertext = substr($ciphertext, 16);
        $plaintext = openssl_decrypt($ciphertext, 'AES-256-CBC', '10h-2023-fdv.xc{fd}ertq.-dfvcr5,.dfs|dfblerc,.sdfb', OPENSSL_RAW_DATA, $iv);
        return $plaintext;
    }
}
