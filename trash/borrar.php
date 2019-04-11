<?php
$path = $_GET["path"];
var_dump($_GET["path"]);

$directorio = $path; //Lo sacas de donde sea.. a traves de post, get, lo que quieras.

$archivos = scandir($directorio); //hace una lista de archivos del directorio


//Los borramos
for ($i=0; $i<=count($archivos); $i++) {
 unlink ($archivos[$i]); 
}

//borramos el directorio

rmdir ($directorio);