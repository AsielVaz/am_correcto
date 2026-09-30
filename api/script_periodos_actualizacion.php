<?php

require_once dirname(__DIR__) . '/bootstrap.php';


class CronConector
{
    // Almacenamos los valores de conexión a la base de datos como constantes
    // Creamos una variable para almacenar la conexión
    private $conn;

    // Esta función se encarga de establecer la conexión a la base de datos
    public function connect()
    {
        // Usamos la función mysqli_connect para conectarnos a la base de datos
        // y almacenamos la conexión en la variable $conn
        $this->conn = mysqli_connect((string) env('DB_HOST', 'localhost'), (string) env('DB_USERNAME', 'root'), (string) env('DB_PASSWORD', ''), (string) env('DB_DATABASE', 'am_dev'), (int) env('DB_PORT', 3306));

        // Verificamos si la conexión fue exitosa
        if (!$this->conn) {
            // Si hay un error, mostramos un mensaje y finalizamos la ejecución del script
            die("Error de conexión: " . mysqli_connect_error());
        }
    }

    // Esta función se encarga de ejecutar consultas a la base de datos
    public function ejecutar($query)
    {
        // Verificamos si la conexión a la base de datos ya está establecida
        if (!$this->conn) {
            // Si no está establecida, llamamos a la función connect para establecerla
            $this->connect();
        }

        // Ejecutamos la consulta usando la función mysqli_query
        $result = mysqli_query($this->conn, $query);

        // Verificamos si hubo un error al ejecutar la consulta
        if (!$result) {
            // Si hay un error, mostramos un mensaje y finalizamos la ejecución del script
            die("Error al ejecutar la consulta: " . mysqli_error($this->conn));
        }

        // Si la consulta se ejecutó correctamente, devolvemos el resultado
        return $result;
    }

    // Esta función se encarga de cerrar la conexión a la base de datos
    public function close()
    {
        // Usamos la función mysqli_close para cerrar la conexión
        mysqli_close($this->conn);
    }
}


class Empresa
{
    public $id;
    public $razon;
    public $finDominio;
    public $telefono;
    public $correo;
    public $sitioWeb;
    public $ultimoPdf;
    public $ultimaConstancia;
    public $periodoComprobanteDomicilio;
    public $periodoConstancia;
    public $periodo32D;
    public $periodoTelefono;
    public $periodoSitioWeb;
    public $periodoCorreo;

    public function __construct()
    {
        $this->id = 0;
        $this->razon = '';
        $this->finDominio = '';
        $this->telefono = '';
        $this->correo = '';
        $this->sitioWeb = '';
        $this->ultimoPdf = '';
        $this->ultimaConstancia = '';
        $this->periodoComprobanteDomicilio = '';
        $this->periodoConstancia = '';
        $this->periodo32D = '';
        $this->periodoTelefono = '';
        $this->periodoSitioWeb = '';
        $this->periodoCorreo = '';
    }
}


class EmpresaController
{
    // Creamos una instancia de la clase Conector
    private $conector;

    public function __construct()
    {
        $this->conector = new CronConector();
    }

    // Esta función retorna una lista de todas las empresas
    public function obtenerEmpresas()
    {
        // Realizamos la consulta a la base de datos
        $result = $this->conector->ejecutar("SELECT * FROM empresas");

        // Creamos una lista vacía para almacenar los objetos Empresa
        $empresas = [];

        // Iteramos a través de los resultados de la consulta
        while ($row = mysqli_fetch_assoc($result)) {
            // Creamos un objeto Empresa
            $empresa = new Empresa();

            // Asignamos los valores de la fila a las propiedades del objeto
            $empresa->id = $row['id'];
            $empresa->razon = $row['razon'];
            $empresa->finDominio = $row['fin_dominio'];
            $empresa->telefono = $row['telefono'];
            $empresa->correo = $row['correo'];
            $empresa->sitioWeb = $row['sitio_web'];
            $empresa->ultimoPdf = $row['ultimo_pdf'];
            $empresa->ultimaConstancia = $row['ultima_constancia'];
            $empresa->periodoComprobanteDomicilio = $row['periodo_comprobanteDom'];
            $empresa->periodoConstancia = $row['periodo_constancia'];
            $empresa->periodo32D = $row['periodo_32d'];
            $empresa->periodoTelefono = $row['periodo_telefono'];
            $empresa->periodoSitioWeb = $row['periodo_sitiow'];
            $empresa->periodoCorreo = $row['periodo_correo'];

            // Agregamos el objeto a la lista
            $empresas[] = $empresa;
        }

        // Cerramos la conexión a la base de datos
        $this->conector->close();

        // Retornamos la lista de empresas
        return $empresas;
    }


    public function obtenerEmpresasPeriodoComprobanteDomicilio()
    {
        // Creamos la consulta SQL para obtener el ID y el valor del campo periodo_comprobanteDom de todas las empresas
        $query = "SELECT id, periodo_comprobanteDom FROM empresas";

        // Ejecutamos la consulta y almacenamos el resultado en una variable
        $result = $this->conector->ejecutar($query);

        // Verificamos si la consulta tuvo éxito
        if ($result) {
            // Si tuvo éxito, creamos una lista para almacenar los resultados
            $empresas = array();

            // Recorremos los resultados usando la función mysqli_fetch_assoc
            while ($row = mysqli_fetch_assoc($result)) {
                // Creamos un objeto Empresa
                $empresa = new Empresa();

                // Asignamos los valores del resultado a las propiedades del objeto Empresa
                $empresa->id = $row['id'];
                $empresa->periodoComprobanteDomicilio = $row['periodo_comprobanteDom'];

                // Agregamos el objeto Empresa a la lista
                $empresas[] = $empresa;
            }

            // Devolvemos la lista de empresas
            return $empresas;
        } else {
            // Si la consulta falló, devolvemos false
            return false;
        }
    }

    public function obtenerEmpresasPeriodoConstancia()
    {
        // Creamos la consulta SQL para obtener el ID y el valor del campo periodo_constancia de todas las empresas
        $query = "SELECT id, periodo_constancia FROM empresas";

        // Ejecutamos la consulta y almacenamos el resultado en una variable
        $result = $this->conector->ejecutar($query);

        // Verificamos si la consulta tuvo éxito
        if ($result) {
            // Si tuvo éxito, creamos una lista para almacenar los resultados
            $empresas = array();

            // Recorremos los resultados usando la función mysqli_fetch_assoc
            while ($row = mysqli_fetch_assoc($result)) {
                // Creamos un objeto Empresa
                $empresa = new Empresa();

                // Asignamos los valores del resultado a las propiedades del objeto Empresa
                $empresa->id = $row['id'];
                $empresa->periodoConstancia = $row['periodo_constancia'];

                // Agregamos el objeto Empresa a la lista
                $empresas[] = $empresa;
            }

            // Devolvemos la lista de empresas
            return $empresas;
        } else {
            // Si la consulta falló, devolvemos false
            return false;
        }
    }


    public function obtenerEmpresasPeriodo32D()
    {
        // Creamos la consulta SQL para obtener el ID y el valor del campo periodo_32d de todas las empresas
        $query = "SELECT id, periodo_32d FROM empresas";

        // Ejecutamos la consulta y almacenamos el resultado en una variable
        $result = $this->conector->ejecutar($query);

        // Verificamos si la consulta tuvo éxito
        if ($result) {
            // Si tuvo éxito, creamos una lista para almacenar los resultados
            $empresas = array();

            // Recorremos los resultados usando la función mysqli_fetch_assoc
            while ($row = mysqli_fetch_assoc($result)) {
                // Creamos un objeto Empresa
                $empresa = new Empresa();

                // Asignamos los valores del resultado a las propiedades del objeto Empresa
                $empresa->id = $row['id'];
                $empresa->periodo32D = $row['periodo_32d'];

                // Agregamos el objeto Empresa a la lista
                $empresas[] = $empresa;
            }


            // Devolvemos la lista de empresas
            return $empresas;
        } else {
            // Si la consulta falló, devolvemos false
            return false;
        }
    }

    public function obtenerEmpresasPeriodoTelefono()
    {
        // Creamos la consulta SQL para obtener el ID y el valor del campo periodo_telefono de todas las empresas
        $query = "SELECT id, periodo_telefono FROM empresas";

        // Ejecutamos la consulta y almacenamos el resultado en una variable
        $result = $this->conector->ejecutar($query);

        // Verificamos si la consulta tuvo éxito
        if ($result) {
            // Si tuvo éxito, creamos una lista para almacenar los resultados
            $empresas = array();

            // Recorremos los resultados usando la función mysqli_fetch_assoc
            while ($row = mysqli_fetch_assoc($result)) {
                // Creamos un objeto Empresa
                $empresa = new Empresa();

                // Asignamos los valores del resultado a las propiedades del objeto Empresa
                $empresa->id = $row['id'];
                $empresa->periodoTelefono = $row['periodo_telefono'];

                // Agregamos el objeto Empresa a la lista
                $empresas[] = $empresa;
            }


            // Devolvemos la lista de empresas
            return $empresas;
        } else {
            // Si la consulta falló, devolvemos false
            return false;
        }
    }


    public function obtenerEmpresasPeriodoCorreo()
    {
        // Creamos la consulta SQL para obtener el ID y el valor del campo periodo_correo de todas las empresas
        $query = "SELECT id, periodo_correo FROM empresas";

        // Ejecutamos la consulta y almacenamos el resultado en una variable
        $result = $this->conector->ejecutar($query);

        // Verificamos si la consulta tuvo éxito
        if ($result) {
            // Si tuvo éxito, creamos una lista para almacenar los resultados
            $empresas = array();

            // Recorremos los resultados usando la función mysqli_fetch_assoc
            while ($row = mysqli_fetch_assoc($result)) {
                // Creamos un objeto Empresa
                $empresa = new Empresa();

                // Asignamos los valores del resultado a las propiedades del objeto Empresa
                $empresa->id = $row['id'];
                $empresa->periodoCorreo = $row['periodo_correo'];

                // Agregamos el objeto Empresa a la lista
                $empresas[] = $empresa;
            }


            // Devolvemos la lista de empresas
            return $empresas;
        } else {
            // Si la consulta falló, devolvemos false
            return false;
        }
    }


    public function obtenerEmpresasPeriodoSitioWeb()
    {
        // Creamos la consulta SQL para obtener el ID y el valor del campo periodo_sitioWeb de todas las empresas
        $query = "SELECT id, periodo_sitiow FROM empresas";

        // Ejecutamos la consulta y almacenamos el resultado en una variable
        $result = $this->conector->ejecutar($query);

        // Verificamos si la consulta tuvo éxito
        if ($result) {
            // Si tuvo éxito, creamos una lista para almacenar los resultados
            $empresas = array();

            // Recorremos los resultados usando la función mysqli_fetch_assoc
            while ($row = mysqli_fetch_assoc($result)) {
                // Creamos un objeto Empresa
                $empresa = new Empresa();

                // Asignamos los valores del resultado a las propiedades del objeto Empresa
                $empresa->id = $row['id'];
                $empresa->periodoSitioWeb = $row['periodo_sitiow'];

                // Agregamos el objeto Empresa a la lista
                $empresas[] = $empresa;
            }

            // Devolvemos la lista de empresas
            return $empresas;
        } else {
            // Si la consulta falló, devolvemos false
            return false;
        }
    }

    // esta funcion verifica el periodo de comprobante de domicilio y actualiza el campo periodo_comprobanteDom si es necesario
    // ejemplo: si el periodo_comprobanteDom es cadaMes-2022-12-27 entonces se tiene que actualizar al siguiente mes
    public function actualizarPeriodoComprobanteDomicilio()
    {
        // Obtenemos la lista de empresas
        $empresas = $this->obtenerEmpresasPeriodoComprobanteDomicilio();

        // Iteramos a través de la lista de empresas
        foreach ($empresas as $empresa) {
            // Obtenemos el periodo de comprobante de domicilio
            $periodo = $empresa->periodoComprobanteDomicilio;

            // Buscamos la posición del delimitador '-' en la cadena
            $pos = strpos($periodo, '-');

            // Si el delimitador se encontró, dividimos la pe$periodo en dos partes
            if ($pos !== false) {
                $frecuencia = substr($periodo, 0, $pos);
                $fechaRegistro = substr($periodo, $pos + 1);

                // Obtenemos la fecha actual
                $fechaActual = date('Y-m-d');

                // Obtenemos la fecha de registro
                $fechaRegistro = date_create($fechaRegistro);

                // Obtenemos la fecha actual
                $fechaActual = date_create($fechaActual);

                // Obtenemos la diferencia entre las dos fechas
                $diferencia = date_diff($fechaRegistro, $fechaActual);

                $diferenciaDias = $fechaRegistro->diff($fechaActual)->days;


                // Obtenemos el número de meses de diferencia
                $meses = $diferencia->m;

                // Obtenemos el número de años de diferencia
                $anios = $diferencia->y;

                // Obtenemos el número de días de diferencia
                $dias = $diferencia->d;


                // Si la frecuencia es cadaMes entonces verificamos si han pasado 30 días o 1 mes desde la fecha de registro
                if ($frecuencia == 'cadaMes' && $diferenciaDias == 30) {
                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de comprobante de domicilio con la fecha actual
                    $query = "UPDATE empresas SET periodo_comprobanteDom = 'cadaMes-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_comprobante = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'comprobanteDomicilio' AND estatus_postergado = 2";
                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de comprobante de domicilio de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de comprobante de domicilio de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // si la frecuencia es cada2Meses entonces verificamos si han pasado 60 días o 2 meses desde la fecha de registro
                else if ($frecuencia == 'cada2Meses' && $diferenciaDias == 60) {
                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de comprobante de domicilio
                    $query = "UPDATE empresas SET periodo_comprobanteDom = 'cada2Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_comprobante = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'comprobanteDomicilio' AND estatus_postergado = 2";
                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de comprobante de domicilio de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de comprobante de domicilio de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // si la frecuencia es cada3Meses entonces verificamos si han pasado 90 días o 3 meses desde la fecha de registro
                else if ($frecuencia == 'cada3Meses' && $diferenciaDias == 90) {
                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de comprobante de domicilio
                    $query = "UPDATE empresas SET periodo_comprobanteDom = 'cada3Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_comprobante = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'comprobanteDomicilio' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de comprobante de domicilio de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de comprobante de domicilio de la empresa con ID $id no se pudo actualizar";
                    }
                }

                // si la frecuencia es cada6Meses entonces verificamos si han pasado 180 días o 6 meses desde la fecha de registro
                else if ($frecuencia == 'cada6Meses' && $diferenciaDias == 180) {
                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de comprobante de domicilio
                    $query = "UPDATE empresas SET periodo_comprobanteDom = 'cada6Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_comprobante = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'comprobanteDomicilio' AND estatus_postergado = 2";
                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de comprobante de domicilio de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de comprobante de domicilio de la empresa con ID $id no se pudo actualizar";
                    }
                } else {
                    // Si no se cumple ninguna de las condiciones anteriores, mostramos un mensaje
                    echo "No hay empresas que actualizar para el periodo de comprobante de domicilio";
                }
            }
        }
    }

    public function actualizarPeriodoConstanciaSituacionFiscal()
    {
        // Obtenemos la lista de empresas
        $empresas = $this->obtenerEmpresasPeriodoConstancia();

        // Iteramos a través de la lista de empresas
        foreach ($empresas as $empresa) {
            // Obtenemos el periodo de constancia de situación fiscal
            $periodo = $empresa->periodoConstancia;

            // Buscamos la posición del delimitador '-' en la cadena
            $pos = strpos($periodo, '-');

            // Si el delimitador se encontró, dividimos la pe$periodo en dos partes
            if ($pos !== false) {
                $frecuencia = substr($periodo, 0, $pos);
                $fechaRegistro = substr($periodo, $pos + 1);

                // Obtenemos la fecha actual
                $fechaActual = date('Y-m-d');

                // Obtenemos la fecha de registro
                $fechaRegistro = date_create($fechaRegistro);

                // Obtenemos la fecha actual
                $fechaActual = date_create($fechaActual);

                // Obtenemos la diferencia entre las dos fechas
                $diferencia = date_diff($fechaRegistro, $fechaActual);
                $diferenciaDias = $fechaRegistro->diff($fechaActual)->days;



                // Obtenemos el número de meses de diferencia
                $meses = $diferencia->m;

                // Obtenemos el número de años de diferencia
                $anios = $diferencia->y;

                // Obtenemos el número de días de diferencia
                $dias = $diferencia->d;

                // Si la frecuencia es cadaMes entonces verificamos si han pasado 30 días o 1 mes desde la fecha de registro
                if ($frecuencia == 'cadaMes' && $diferenciaDias == 30) {
                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de constancia de situación fiscal
                    $query = "UPDATE empresas SET periodo_constancia = 'cadaMes-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_constancia = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'constanciaSituacionFiscal' AND estatus_postergado = 2";
                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de constancia de situación fiscal de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de constancia de situación fiscal de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // si la frecuencia es cada2Meses entonces verificamos si han pasado 60 días o 2 meses desde la fecha de registro
                else if ($frecuencia == 'cada2Meses' && $diferenciaDias == 60) {
                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de constancia de situación fiscal
                    $query = "UPDATE empresas SET periodo_constancia = 'cada2Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_comprobante = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'constanciaSituacionFiscal' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de constancia de situación fiscal de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de constancia de situación fiscal de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // si la frecuencia es cada3Meses entonces verificamos si han pasado 90 días o 3 meses desde la fecha de registro
                else if ($frecuencia == 'cada3Meses' && $diferenciaDias == 90) {
                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de constancia de situación fiscal
                    $query = "UPDATE empresas SET periodo_constancia = 'cada3Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_comprobante = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'constanciaSituacionFiscal' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de constancia de situación fiscal de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de constancia de situación fiscal de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // si la frecuencia es cada6Meses entonces verificamos si han pasado 180 días o 6 meses desde la fecha de registro
                else if ($frecuencia == 'cada6Meses' && $diferenciaDias == 180) {
                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de constancia de situación fiscal
                    $query = "UPDATE empresas SET periodo_constancia = 'cada6Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_comprobante = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'constanciaSituacionFiscal' AND estatus_postergado = 2";
                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de constancia de situación fiscal de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de constancia de situación fiscal de la empresa con ID $id no se pudo actualizar";
                    }
                } else {
                    // Si no se cumple ninguna de las condiciones anteriores, mostramos un mensaje
                    echo "No hay empresas que actualizar para el periodo de constancia de situación fiscal";
                }
            }
        }
    }

    public function actualizarPeriodo32D()
    {
        // Obtenemos la lista de empresas
        $empresas = $this->obtenerEmpresasPeriodo32D();

        // Iteramos a través de la lista de empresas
        foreach ($empresas as $empresa) {
            // Obtenemos el periodo de 32D
            $periodo = $empresa->periodo32D;

            // Buscamos la posición del delimitador '-' en la cadena
            $pos = strpos($periodo, '-');

            // Si el delimitador se encontró, dividimos la pe$periodo en dos partes
            if ($pos !== false) {
                $frecuencia = substr($periodo, 0, $pos);
                $fechaRegistro = substr($periodo, $pos + 1);

                // Obtenemos la fecha actual
                $fechaActual = date('Y-m-d');

                // Obtenemos la fecha de registro
                $fechaRegistro = date_create($fechaRegistro);

                // Obtenemos la fecha actual
                $fechaActual = date_create($fechaActual);

                // Obtenemos la diferencia entre las dos fechas
                $diferencia = date_diff($fechaRegistro, $fechaActual);

                $diferenciaDias = $fechaRegistro->diff($fechaActual)->days;


                // Obtenemos el número de meses de diferencia
                $meses = $diferencia->m;

                // Obtenemos el número de años de diferencia
                $anios = $diferencia->y;

                // Obtenemos el número de días de diferencia
                $dias = $diferencia->d;

                // Si la frecuencia es cadaMes entonces verificamos si han pasado 30 días o 1 mes desde la fecha de registro
                if ($frecuencia == 'cadaMes' && $diferenciaDias == 30) {
                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de 32D
                    $query = "UPDATE empresas SET periodo_32d = 'cadaMes-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_32d = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = '32D' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de 32D de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de 32D de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // si la frecuencia es cada2Meses entonces verificamos si han pasado 60 días o 2 meses desde la fecha de registro
                else if ($frecuencia == 'cada2Meses' && $diferenciaDias == 60) {
                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de 32D
                    $query = "UPDATE empresas SET periodo_32d = 'cada2Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_32d = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = '32D' AND estatus_postergado = 2";
                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de 32D de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de 32D de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // si la frecuencia es cada3Meses entonces verificamos si han pasado 90 días o 3 meses desde la fecha de registro
                else if ($frecuencia == 'cada3Meses' && $diferenciaDias == 90) {
                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de 32D
                    $query = "UPDATE empresas SET periodo_32d = 'cada3Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_32d = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = '32D' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de 32D de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de 32D de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // si la frecuencia es cada6Meses entonces verificamos si han pasado 180 días o 6 meses desde la fecha de registro
                else if ($frecuencia == 'cada6Meses' && $diferenciaDias == 180) {
                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de 32D
                    $query = "UPDATE empresas SET periodo_32d = 'cada6Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_32d = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = '32D' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de 32D de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de 32D de la empresa con ID $id no se pudo actualizar";
                    }
                } else {
                    // Si no se cumple ninguna de las condiciones anteriores, mostramos un mensaje
                    echo "No hay empresas que actualizar para el periodo de 32D";
                }
            }
        }
    }

    public function actualizarPeriodoTelefono()
    {
        // Obtenemos la lista de empresas
        $empresas = $this->obtenerEmpresasPeriodoTelefono();

        // Iteramos a través de la lista de empresas
        foreach ($empresas as $empresa) {
            // Obtenemos el periodo de Telefono
            $periodo = $empresa->periodoTelefono;

            // Buscamos la posición del delimitador '-' en la cadena
            $pos = strpos($periodo, '-');

            // Si el delimitador se encontró, dividimos la pe$periodo en dos partes
            if ($pos !== false) {
                $frecuencia = substr($periodo, 0, $pos);
                $fechaRegistro = substr($periodo, $pos + 1);
                // Obtenemos la fecha actual
                $fechaActual = date('Y-m-d');

                // Obtenemos la fecha de registro
                $fechaRegistro = date_create($fechaRegistro);


                // Obtenemos la fecha actual
                $fechaActual = date_create($fechaActual);

                // Obtenemos la diferencia entre las dos fechas
                $diferencia = date_diff($fechaRegistro, $fechaActual);

                $diferenciaDias = $fechaRegistro->diff($fechaActual)->days;


                // Obtenemos el número de meses de diferencia
                $meses = $diferencia->m;

                // Obtenemos el número de años de diferencia
                $anios = $diferencia->y;

                // Obtenemos el número de días de diferencia
                $dias = $diferencia->d;

                // Si la frecuencia es cadaSemana entonces verificamos si han pasado 7 días desde la fecha de registro
                if ($frecuencia == 'cadaSemana' && $diferenciaDias == 7) {

                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de telefono
                    $query = "UPDATE empresas SET periodo_telefono = 'cadaSemana-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_telefono = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'telefono' AND estatus_postergado = 2";
                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de telefono de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de telefono de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // Si la frecuencia es cadaMes entonces verificamos si han pasado 30 días desde la fecha de registro
                else if ($frecuencia == 'cadaMes' && $diferenciaDias == 30) {

                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de telefono
                    $query = "UPDATE empresas SET periodo_telefono = 'cadaMes-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_telefono = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'telefono' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);
                    $result2 = $this->conector->ejecutar($query2);
                    $result3 = $this->conector->ejecutar($query3);
                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de telefono de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de telefono de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // Si la frecuencia es cada2Meses entonces verificamos si han pasado 60 días desde la fecha de registro
                else if ($frecuencia == 'cada2Meses' && $diferenciaDias == 60) {

                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de telefono
                    $query = "UPDATE empresas SET periodo_telefono = 'cada2Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_telefono = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'telefono' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de telefono de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de telefono de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // Si la frecuencia es cada3Meses entonces verificamos si han pasado 90 días desde la fecha de registro
                else if ($frecuencia == 'cada3Meses' && $diferenciaDias == 90) {

                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de telefono
                    $query = "UPDATE empresas SET periodo_telefono = 'cada3Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_telefono = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'telefono' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de telefono de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de telefono de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // Si la frecuencia es cada6Meses entonces verificamos si han pasado 180 días desde la fecha de registro
                else if ($frecuencia == 'cada6Meses' && $diferenciaDias == 180) {

                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de telefono
                    $query = "UPDATE empresas SET periodo_telefono = 'cada6Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_telefono = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'telefono' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de telefono de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de telefono de la empresa con ID $id no se pudo actualizar";
                    }
                } else {
                    // Si no se cumple ninguna de las condiciones anteriores, mostramos un mensaje
                    echo "No hay empresas que actualizar para el periodo de telefono";
                }
            }
        }
    }

    public function actualizarPeriodoCorreo()
    {
        // Obtenemos la lista de empresas
        $empresas = $this->obtenerEmpresasPeriodoCorreo();

        // Iteramos a través de la lista de empresas
        foreach ($empresas as $empresa) {
            // Obtenemos el periodo de correo
            $periodo = $empresa->periodoCorreo;

            // Buscamos la posición del delimitador '-' en la cadena
            $pos = strpos($periodo, '-');

            // Si el delimitador se encontró, dividimos la pe$periodo en dos partes
            if ($pos !== false) {
                $frecuencia = substr($periodo, 0, $pos);
                $fechaRegistro = substr($periodo, $pos + 1);
                // Obtenemos la fecha actual
                $fechaActual = date('Y-m-d');

                // Obtenemos la fecha de registro
                $fechaRegistro = date_create($fechaRegistro);


                // Obtenemos la fecha actual
                $fechaActual = date_create($fechaActual);

                // Obtenemos la diferencia entre las dos fechas
                $diferencia = date_diff($fechaRegistro, $fechaActual);

                $diferenciaDias = $fechaRegistro->diff($fechaActual)->days;

                // Obtenemos el número de meses de diferencia
                $meses = $diferencia->m;

                // Obtenemos el número de años de diferencia
                $anios = $diferencia->y;

                // Obtenemos el número de días de diferencia
                $dias = $diferencia->d;

                // Si la frecuencia es cadaSemana entonces verificamos si han pasado 7 días desde la fecha de registro
                if ($frecuencia == 'cadaSemana' && $diferenciaDias == 7) {

                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de correo
                    $query = "UPDATE empresas SET periodo_correo = 'cadaSemana-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_correo = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'correo' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de correo de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de correo de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // Si la frecuencia es cadaMes entonces verificamos si han pasado 30 días desde la fecha de registro o un mes
                else if ($frecuencia == 'cadaMes' && $diferenciaDias == 30) {

                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de correo
                    $query = "UPDATE empresas SET periodo_correo = 'cadaMes-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_correo = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'correo' AND estatus_postergado = 2";
                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de correo de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "No hay empresas que actualizar para el periodo de correo";
                    }
                }
                // Si la frecuencia es cada2Meses entonces verificamos si han pasado 60 días desde la fecha de registro o dos meses
                else if ($frecuencia == 'cada2Meses' && $diferenciaDias == 60) {

                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de correo
                    $query = "UPDATE empresas SET periodo_correo = 'cada2Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_correo = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'correo' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de correo de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "No hay empresas que actualizar para el periodo de correo";
                    }
                }
                // Si la frecuencia es cada3Meses entonces verificamos si han pasado 90 días desde la fecha de registro o tres meses
                else if ($frecuencia == 'cada3Meses' && $diferenciaDias == 90) {

                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de correo
                    $query = "UPDATE empresas SET periodo_correo = 'cada3Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_correo = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'correo' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de correo de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "No hay empresas que actualizar para el periodo de correo";
                    }
                }
                // Si la frecuencia es cada6Meses entonces verificamos si han pasado 180 días desde la fecha de registro o seis meses
                else if ($frecuencia == 'cada6Meses' && $diferenciaDias == 180) {

                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de correo
                    $query = "UPDATE empresas SET periodo_correo = 'cada6Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_correo = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'correo' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    $result2 = $this->conector->ejecutar($query2);

                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de correo de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "No hay empresas que actualizar para el periodo de correo";
                    }
                } else {
                    // Si la frecuencia no es ninguna de las anteriores, mostramos un mensaje
                    echo "No hay empresas que actualizar para el periodo de correo";
                }
            }
        }
    }

    public function actualizarPeriodoSitioWeb()
    {
        // Obtenemos la lista de empresas
        $empresas = $this->obtenerEmpresasPeriodoSitioWeb();

        // Iteramos a través de la lista de empresas
        foreach ($empresas as $empresa) {
            // Obtenemos el periodo de sitio web
            // Si la frecuencia es cadaMes entonces verificamos si la diferencia es de un mes
            $periodo = $empresa->periodoSitioWeb;

            // Buscamos la posición del delimitador '-' en la cadena
            $pos = strpos($periodo, '-');

            // Si el delimitador se encontró, dividimos la pe$periodo en dos partes
            if ($pos !== false) {
                $frecuencia = substr($periodo, 0, $pos);
                $fechaRegistro = substr($periodo, $pos + 1);
                // Obtenemos la fecha actual
                $fechaActual = date('Y-m-d');

                // Obtenemos la fecha de registro
                $fechaRegistro = date_create($fechaRegistro);


                // Obtenemos la fecha actual
                $fechaActual = date_create($fechaActual);

                // Obtenemos la diferencia entre las dos fechas
                $diferencia = date_diff($fechaRegistro, $fechaActual);

                $diferenciaDias = $fechaRegistro->diff($fechaActual)->days;

                // Obtenemos el número de meses de diferencia
                $meses = $diferencia->m;

                // Obtenemos el número de años de diferencia
                $anios = $diferencia->y;

                // Obtenemos el número de días de diferencia
                $dias = $diferencia->d;

                // // Si la frecuencia es cadaSemana entonces verificamos si han pasado 7 días desde la fecha de registro
                if ($frecuencia == 'cadaSemana' && $diferenciaDias == 7) {

                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de sitio web
                    $query = "UPDATE empresas SET periodo_sitiow = 'cadaSemana-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_sitioweb = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'sitioWeb' AND estatus_postergado = 2";
                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    // Ejecutamos la consulta
                    $result2 = $this->conector->ejecutar($query2);

                    // Ejecutamos la consulta
                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si las consultas tuvieron éxito

                    if ($result && $result2 && $result3) {
                        // Si tuvieron éxito, mostramos un mensaje
                        echo "El periodo de sitio web de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si las consultas fallaron, mostramos un mensaje
                        echo "El periodo de sitio web de la empresa con ID $id no se pudo actualizar";
                    }
                } else if ($frecuencia == 'cadaMes' && $diferenciaDias == 30) {

                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de sitio web
                    $query = "UPDATE empresas SET periodo_sitiow = 'cadaMes-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_sitioweb = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'sitioWeb' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    // Ejecutamos la consulta
                    $result2 = $this->conector->ejecutar($query2);

                    // Ejecutamos la consulta
                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de sitio web de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de sitio web de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // Si la frecuencia es cada2Meses entonces verificamos si han pasado 60 días desde la fecha de registro o dos meses
                else if ($frecuencia == 'cada2Meses' && $diferenciaDias == 60) {

                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de sitio web
                    $query = "UPDATE empresas SET periodo_sitiow = 'cada2Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_sitioweb = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'sitioWeb' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    // Ejecutamos la consulta
                    $result2 = $this->conector->ejecutar($query2);

                    // Ejecutamos la consulta
                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de sitio web de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de sitio web de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // Si la frecuencia es cada3Meses entonces verificamos si han pasado 90 días desde la fecha de registro o tres meses
                else if ($frecuencia == 'cada3Meses' && $diferenciaDias == 90) {

                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de sitio web
                    $query = "UPDATE empresas SET periodo_sitiow = 'cada3Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_sitioweb = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'sitioWeb' AND estatus_postergado = 2";

                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    // Ejecutamos la consulta
                    $result2 = $this->conector->ejecutar($query2);

                    // Ejecutamos la consulta
                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de sitio web de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de sitio web de la empresa con ID $id no se pudo actualizar";
                    }
                }
                // Si la frecuencia es cada6Meses entonces verificamos si han pasado 180 días desde la fecha de registro o seis meses
                else if ($frecuencia == 'cada6Meses' && $diferenciaDias == 180) {

                    // Obtenemos el ID de la empresa
                    $id = $empresa->id;

                    // Creamos la consulta SQL para actualizar el periodo de sitio web
                    $query = "UPDATE empresas SET periodo_sitiow = 'cada6Meses-" . $fechaActual->format('Y-m-d') . "' WHERE id = $id";

                    $query2 = "UPDATE estatus_periodos SET estatus_sitioweb = 'ACTIVO' WHERE id_empresa = $id";

                    $query3 = "DELETE FROM campos_postergados WHERE id_empresa = $id AND campo_a_postergar = 'sitioWeb' AND estatus_postergado = 2";
                    // Ejecutamos la consulta
                    $result = $this->conector->ejecutar($query);

                    // Ejecutamos la consulta
                    $result2 = $this->conector->ejecutar($query2);

                    // Ejecutamos la consulta
                    $result3 = $this->conector->ejecutar($query3);

                    // Verificamos si la consulta tuvo éxito
                    if ($result && $result2 && $result3) {
                        // Si tuvo éxito, mostramos un mensaje
                        echo "El periodo de sitio web de la empresa con ID $id se actualizó correctamente";
                    } else {
                        // Si la consulta falló, mostramos un mensaje
                        echo "El periodo de sitio web de la empresa con ID $id no se pudo actualizar";
                    }
                } else {
                    echo "No hay empresas que actualizar para el periodo de sitio web";
                }
            }
        }
    }
}


// Creamos un objeto de la clase EmpresaController
$empresaController = new EmpresaController();

$empresaController->actualizarPeriodoComprobanteDomicilio();
$empresaController->actualizarPeriodoConstanciaSituacionFiscal();
$empresaController->actualizarPeriodo32D();
$empresaController->actualizarPeriodoTelefono();
$empresaController->actualizarPeriodoCorreo();
$empresaController->actualizarPeriodoSitioWeb();
