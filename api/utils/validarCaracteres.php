<?php


class ValidarCaracteres
{

    public function validarTexto($cadena)
    {
        if (preg_match('/^[a-zA-ZñÑáéíóúÁÉÍÓÚ0-9]{3,15}$/', $cadena)) {
            return $cadena;
        } else {
            return 0;
        }
    }

    public function validarEmail($email)
    {
        if (preg_match('/^[a-zA-Z0-9\._-]+@[a-zA-Z0-9-]{2,}[.][a-zA-Z]{2,4}$/', $email)) {
            return $email;
        } else {
            return 0;
        }
    }

    public function validarTelefono($telefono)
    {
        if (preg_match('/^[0-9-]{6,12}$/', $telefono)) {
            return $telefono;
        } else {
            return 0;
        }
    }

    public function validarContrasena($pass)
    {
        if (preg_match('/^[a-zA-Z0-9]{6,15}$/', $pass)) {
            return $pass;
        } else {
            return 0;
        }
    }
}