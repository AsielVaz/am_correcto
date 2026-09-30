<?php
include_once('conectorBD.php');

$conector = new conector();


//limpia estados de cuenta
$sql = "DELETE FROM `estados_cuenta` WHERE fec_creacion < '" . date('Y-m-d', strtotime('-6 month')) . "'";
$result = $conector->ejecutar($sql);
