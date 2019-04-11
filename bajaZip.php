<?php

set_time_limit(-1);

$fecha = date("Y-m-d"); 

$path = 'temp/';

$rootPath = realpath($path);

$zip = new ZipArchive();

$zip->open("ZIP/ZIP ". $fecha . ".zip", ZipArchive::CREATE);

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file)
{
    if (!$file->isDir())
    {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);
        $zip->addFile($filePath, $relativePath);
    }
}

$zip->close();

header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=ZIP ". $fecha . ".zip");
readfile("ZIP/ZIP ". $fecha . ".zip");