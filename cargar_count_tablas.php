<?php
require "config.php";

$mysqli = new mysqli($host_C, $usuario_C, $pasword_C, $nombreDeBaseDeDatos_C);
//$mysqli = new mysqli($host_C, $usuario_C, $pasword_C, $nombreDeBaseDeDatos_C, "3307");

$mysqli->select_db($nombreDeBaseDeDatos_C);
$mysqli->query("SET NAMES 'utf8'");
$response = [];
$tabla = $_GET["tabla"][0];
$consulta = "SELECT COUNT(*) FROM `". $tabla."`;";
$result = mysqli_query($mysqli,$consulta);
if ($res = mysqli_fetch_row($result)){
	$response['nombre'] = $tabla;
	$response['count'] = $res[0];
}else{
	$response['nombre'] = $tabla;
	$response['count'] = 0;
}	
echo json_encode($response);
