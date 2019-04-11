<?php
//error_reporting(0);
require "config.php";

$mysqli = new mysqli($host_C, $usuario_C, $pasword_C, $nombreDeBaseDeDatos_C);
//$mysqli = new mysqli($host_C, $usuario_C, $pasword_C, $nombreDeBaseDeDatos_C, "3307");

$mysqli->select_db($nombreDeBaseDeDatos_C);
$mysqli->query("SET NAMES 'utf8'");

$tabla = $_GET["tabla"];

set_time_limit(-1);

//$hoy = date(" Y-m-d H.i.s"); 
$fecha = date("Y-m-d"); 
$path = "DDL/";
if(!is_dir($path)){
	mkdir($path);
}

$path2 = "structure";
$path = $path."/".$path2;

if(!is_dir($path)){
	mkdir($path);
}

$script = "";

$nombre_archivo = "$path/$tabla.sql"; 
$numArchivo++;

$consulta = "SHOW CREATE TABLE $tabla";

$result = mysqli_query($mysqli,$consulta);


while ($rows = mysqli_fetch_row($result)){
	$script =  $rows[1];			
}

if($archivo = fopen($nombre_archivo, "a"))
{
	fwrite($archivo, $script);
	fclose($archivo);
}