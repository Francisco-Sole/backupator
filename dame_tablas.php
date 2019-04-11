<?php
require "config.php";

$mysqli = new mysqli($host_C, $usuario_C, $pasword_C, $nombreDeBaseDeDatos_C);
//$mysqli = new mysqli($host_C, $usuario_C, $pasword_C, $nombreDeBaseDeDatos_C, "3307");

$mysqli->select_db($nombreDeBaseDeDatos_C);
$mysqli->query("SET NAMES 'utf8'");
$response = [];
$consulta = "SHOW TABLES";
$result = mysqli_query($mysqli,$consulta);
$tablas = [];
while ($tabla = mysqli_fetch_row($result)){
	array_push($tablas, $tabla);
}


echo json_encode($tablas);
