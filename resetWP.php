<?php
/*
Work Path
--> BACKUP + $fecha
--> DDL
--> ZIP
*/
$fecha = date("Y-m-d"); 
$path = "BACKUP ". $fecha;
$ficheros  = scandir($path);

//2 por el 0 y el 1 son '.' y '..'
for ($i=2; $i < count($ficheros); $i++) { 
	if (is_dir($path."/".$ficheros[$i])) {
		$ficheros2  = scandir($path."/".$ficheros[$i]);
		for ($ii=2; $ii < count($ficheros2); $ii++) { 
			unlink($path."/".$ficheros[$i]."/".$ficheros2[$ii]);		
		}
		rmdir($path."/".$ficheros[$i]);
	}else{
		unlink($path."/".$ficheros[$i]);		
	}	
}
//borramos la carpeta del backup
rmdir($path);


$path = "DDL";
$ficheros  = scandir($path);

//2 por el 0 y el 1 son '.' y '..'
for ($i=2; $i < count($ficheros); $i++) { 
	if (is_dir($path."/".$ficheros[$i])) {
		$ficheros2  = scandir($path."/".$ficheros[$i]);
		for ($ii=2; $ii < count($ficheros2); $ii++) { 
			unlink($path."/".$ficheros[$i]."/".$ficheros2[$ii]);		
		}
		rmdir($path."/".$ficheros[$i]);
	}else{
		unlink($path."/".$ficheros[$i]);		
	}	
}


$path = "ZIP";
$ficheros  = scandir($path);

//2 por el 0 y el 1 son '.' y '..'
for ($i=2; $i < count($ficheros); $i++) { 
	if (is_dir($path."/".$ficheros[$i])) {
		$ficheros2  = scandir($path."/".$ficheros[$i]);
		for ($ii=2; $ii < count($ficheros2); $ii++) { 
			unlink($path."/".$ficheros[$i]."/".$ficheros2[$ii]);		
		}
		rmdir($path."/".$ficheros[$i]);
	}else{
		unlink($path."/".$ficheros[$i]);		
	}	
}

echo json_encode(true);