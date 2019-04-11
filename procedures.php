<?php
//error_reporting(0);
require "config.php";

$mysqli = new mysqli($host_C, $usuario_C, $pasword_C, $nombreDeBaseDeDatos_C);
//$mysqli = new mysqli($host_C, $usuario_C, $pasword_C, $nombreDeBaseDeDatos_C, "3307");

$mysqli->select_db($nombreDeBaseDeDatos_C);
$mysqli->query("SET NAMES 'utf8'");

set_time_limit(-1);


$fecha = date("Y-m-d"); 
$path = "DDL/";
if(!is_dir($path)){
	mkdir($path);
}

$path2 = "procedures";
$path = $path."/".$path2;

if(!is_dir($path)){
	mkdir($path);
}

$script = "";
$nombre_archivo = "";
$total = 0;
$consulta = "show procedure status";

$result = mysqli_query($mysqli,$consulta);

while ($rows = mysqli_fetch_row($result)){
	if ($rows[0] == $nombreDeBaseDeDatos_C) {
		$total++;
		$nombre_archivo = "$path/".$rows[1].".sql";
		$consulta2 = "show create procedure " .$rows[1];
		$result2 = mysqli_query($mysqli,$consulta2);
		while ($rows2 = mysqli_fetch_row($result2)){
			$script =  $rows2[2];
		}
		
		if($archivo = fopen($nombre_archivo, "a"))
		{
			fwrite($archivo, $script);
			fclose($archivo);
		}
	}
}
echo $total;