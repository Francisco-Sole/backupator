<?php
//error_reporting(0);
require "config.php";

$mysqli = new mysqli($host_C, $usuario_C, $pasword_C, $nombreDeBaseDeDatos_C);
$mysqli->select_db($nombreDeBaseDeDatos_C);
$mysqli->query("SET NAMES 'utf8'");

$tabla = $_GET["tabla"];
$total_rows = $_GET["count"];
$current = $_GET["current"];
$variable = $_GET["variable"];
$indice = $_GET["indice"];

set_time_limit(-1);

$hoy = date(" Y-m-d H.i.s"); 
$fecha = date("Y-m-d"); 
$path = "BACKUP ".$fecha;
$ppath = $path;
if(!is_dir($path)){
	mkdir($path);
}

$path2 = $tabla;
$path = $path."/".$path2;

if(!is_dir($path)){
	mkdir($path);
}

$script = "";

$nombre_archivo = "$path/$tabla " . ($current+1000) . ".sql"; 
$numArchivo++;

$consulta = "select * from $tabla limit ". $current ." , ". 1000;
$result = mysqli_query($mysqli,$consulta);
$script .= "SET FOREIGN_KEY_CHECKS=0;\nINSERT INTO `".$tabla."` VALUES ";

while ($rows = mysqli_fetch_row($result)){
	$script .= "(";			
	$index = 1;
	foreach ($rows as $value) {
		$value = str_replace("'", "\'", $value);
		if ($index == 1) {
			$script .= "'$value'";			
		}else{
			$script .= ",'$value'";			
		}
		$index++;
	}
	$script .= "),\n";			
}

$script = substr($script, 0,-2);
$script .= "\n";	

if($archivo = fopen($nombre_archivo, "a"))
{
	fwrite($archivo, $script);
	fclose($archivo);
}

// $zip = new ZipArchive;
// if ($zip->open($ppath.'.zip', ZipArchive::CREATE) === TRUE) {
// 	$zip->addFile($nombre_archivo);	
// 	$zip->close();
// } 	

$response["tabla"] = $tabla;
$response["count"] = $total_rows;
$response["current"] = $current+1000;
$response["variable"] = $variable;
$response["indice"] = $indice;
//$response["consulta"] = $consulta;

echo json_encode($response);