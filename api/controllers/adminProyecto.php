<?php
include_once('../config/conectorBD.php');
include_once('adminEmpleado.php');

class Proyecto
{
    public $id;
    public $nombre;
    public $descripcion;
    public $imagen;
    public $fechaInicio;
    public $fechaFin;
    public $presupuesto;
    public $empresa;
    public $tipoProyecto;
    public $sitioWeb;

    public function __construct($id, $nombre, $descripcion, $imagen, $fechaInicio, $fechaFin, $presupuesto, $empresa, $tipoProyecto, $sitioWeb)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->imagen = $imagen;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->presupuesto = $presupuesto;
        $this->empresa = $empresa;
        $this->tipoProyecto = $tipoProyecto;
        $this->sitioWeb = $sitioWeb;
    }
}

class EmpleadosAsignados
{
    public $id;
    public $empleado;
    public $proyecto;

    public function __construct($id, $empleado, $proyecto)
    {
        $this->id = $id;
        $this->empleado = $empleado;
        $this->proyecto = $proyecto;
    }
}

class Imagenes
{
    public $id;
    public $url;
    public $id_proyecto;

    public function __construct($id, $url, $id_proyecto)
    {
        $this->id = $id;
        $this->url = $url;
        $this->id_proyecto = $id_proyecto;
    }
}


class AdministradorProyecto extends conector
{
    function agregarProyecto($nombre, $descripcion, $imagen, $fechaInicio, $fechaFin, $presupuesto, $empresa, $tipoProyecto, $sitioWeb)
    {
        $query = "INSERT INTO `proyecto` (`nombre`, `descripcion`, `imagen`, `fecha_inicio`, `fecha_fin`, `presupuesto`, `id_empresa`, `tipo_proyecto`, `sitio_web`) VALUES ('$nombre', '$descripcion', '$imagen', '$fechaInicio', '$fechaFin', '$presupuesto', '$empresa', '$tipoProyecto', '$sitioWeb')";
        $result = $this->ejecutar($query);
        return $result;
    }

    function agregarImagen($url, $id_proyecto)
    {
        $query = "INSERT INTO `imagenes` (`url`, `id_proyecto`) VALUES ('$url', '$id_proyecto')";
        $result = $this->ejecutar($query);
        return $result;
    }

    function asignarEmpleado($proyecto, $empleado)
    {
        $query = "INSERT INTO `proyecto_empleado` (`id_proyecto`, `id_empleado`) VALUES ('$proyecto', '$empleado')";
        $result = $this->ejecutar($query);
        return $result;
    }


    function dameEmpleadosPorProyecto($proyecto)
    {
        $adminEmpleado = new AdministradorEmpleado();
        $query = "SELECT * FROM `proyecto_empleado` WHERE `id_proyecto` = '$proyecto'";
        $result = $this->ejecutar($query);
        $empleados = array();
        while ($row = mysqli_fetch_array($result)) {
            $empleados[] = $adminEmpleado->obtenerEmpleado($row['id_empleado']);
        }
        return $empleados;
    }

    function desAsignarEmpleado($id)
    {
        $query = "DELETE FROM `proyecto_empleado` WHERE `id` = '$id'";
        $result = $this->ejecutar($query);
        return $result;
    }

    function desAsignarEmpleadoPorProyecto($proyecto, $empleado)
    {
        $query = "DELETE FROM `proyecto_empleado` WHERE `id_proyecto` = '$proyecto' AND `id_empleado` = '$empleado'";
        $result = $this->ejecutar($query);
        return $result;
    }


    function dameImagenesProyecto($proyecto)
    {
        $query = "SELECT * FROM `imagenes` WHERE `id_proyecto` = '$proyecto'";
        $result = $this->ejecutar($query);
        $imagenes = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $imagenes[] = new Imagenes($row['id'], $row['url'], $row['id_proyecto']);
        }
        return $imagenes;
    }

    function eliminarProyecto($id)
    {
        $query = "DELETE FROM `proyecto` WHERE `id` = '$id'";
        $result = $this->ejecutar($query);
        return $result;
    }

    function modificarProyecto($id, $nombre, $descripcion, $imagen, $fechaInicio, $fechaFin, $presupuesto, $empresa, $tipoProyecto, $sitioWeb)
    {
        $query = "UPDATE `proyecto` SET `nombre` = '$nombre', `descripcion` = '$descripcion', `imagen` = '$imagen', `fecha_inicio` = '$fechaInicio', `fecha_fin` = '$fechaFin', `presupuesto` = '$presupuesto', `id_empresa` = '$empresa', `tipo_proyecto` = '$tipoProyecto', `sitio_web` = '$sitioWeb' WHERE `id` = '$id'";
        $result = $this->ejecutar($query);
        return $result;
    }


    function obtenerProyecto($id)
    {
        $query = "SELECT * FROM `proyecto` WHERE `id` = '$id'";
        $result = $this->ejecutar($query);
        $row = mysqli_fetch_assoc($result);
        $proyecto = new Proyecto($row['id'], $row['nombre'], $row['descripcion'], $row['imagen'], $row['fecha_inicio'], $row['fecha_fin'], $row['presupuesto'], $row['id_empresa'], $row['tipo_proyecto'], $row['sitio_web']);
        return $proyecto;
    }

    function obtenerProyectos()
    {
        $query = "SELECT * FROM `proyecto`";
        $result = $this->ejecutar($query);
        $proyectos = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $proyecto = new Proyecto($row['id'], $row['nombre'], $row['descripcion'], $row['imagen'], $row['fecha_inicio'], $row['fecha_fin'], $row['presupuesto'], $row['id_empresa'], $row['tipo_proyecto'], $row['sitio_web']);
            array_push($proyectos, $proyecto);
        }
        return $proyectos;
    }

    function getNextProyecto($id)
    {
        $query = "SELECT * FROM `proyecto` WHERE `id` > '$id' ORDER BY `id` ASC LIMIT 1";
        $result = $this->ejecutar($query);
        $row = mysqli_fetch_assoc($result);
        return $row['id'];
    }

    function getFirstProyecto()
    {
        $query = "SELECT * FROM `proyecto` ORDER BY `id` ASC LIMIT 1";
        $result = $this->ejecutar($query);
        $row = mysqli_fetch_assoc($result);
        return $row['id'];
    }
}
