<?php
error_reporting(0);
$datos = json_decode($_POST["tablas"]);
$fecha = date("Y-m-d"); 
$path = "BACKUP ".$fecha;
$response = [];
for ($i=0; $i < count($datos); $i++) { 
	$dir = $path."/".$datos[$i]->nombre;
	$explorar = scandir($dir);
	$total_archivos = count($explorar)-2;
	$response[$datos[$i]->nombre] = $total_archivos;
}

echo json_encode($response);