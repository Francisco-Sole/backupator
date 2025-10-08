<?php

set_time_limit(-1);

$fecha = date("Y-m-d");
$path = "BACKUP " . $fecha;

$rootPath = realpath($path);

$zip = new ZipArchive();
$zipFile = "ZIP/" . $path . ".zip";

if (!is_dir("ZIP")) {
	mkdir("ZIP", 0777, true);
}

$res = $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

if ($res !== TRUE) {
	die("Error al crear el ZIP: código $res");
}

$zip->open("ZIP/" . $path . ".zip", ZipArchive::CREATE);

$files = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator($rootPath),
	RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {
	if (!$file->isDir()) {
		$filePath = $file->getRealPath();
		$relativePath = substr($filePath, strlen($rootPath) + 1);
		$zip->addFile($filePath, $relativePath);
	}
}

$path = "DDL";
$rootPath = realpath($path);

$files = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator($rootPath),
	RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {

	if (!$file->isDir()) {
		$filePath = $file->getRealPath();
		$relativePath = substr($filePath, strlen($rootPath) + 1);
		$zip->addFile($filePath, "DDL/" . $relativePath);
	}
}

$zip->close();
$path = "BACKUP " . $fecha;

header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=ZIP " . $path . ".zip");
readfile("ZIP/" . $path . ".zip");
