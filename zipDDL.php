<?php

set_time_limit(-1);
$nombre = $_GET["tabla"];
$path = $_GET["path"];

$rootPath = realpath($path);

// Initialize archive object
$zip = new ZipArchive();
$zip->open('temp/'.$nombre.'.zip', ZipArchive::CREATE);
// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file)
{
    // Skip directories (they would be added automatically)
    if (!$file->isDir())
    {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
        $zip->addFile($filePath, $nombre."/".$relativePath);
    }
}

// Zip archive will be created only after closing object
$zip->close();
echo "1";
// header("Content-type: application/zip"); 
// header("Content-Disposition: attachment; filename=BBDD.zip");
// header("Content-length: " . filesize("BBDD.zip"));
// header("Pragma: no-cache"); 
// header("Expires: 0"); 
// readfile("BBDD.zip");
