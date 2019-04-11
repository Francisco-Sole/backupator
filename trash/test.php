<?php

$tabla = $_GET["tabla"];
$total_rows = $_GET["count"];
$current = $_GET["current"];

$response = [];
$response["tabla"] = $tabla;
$response["count"] = $total_rows;
$response["current"] = $current;


echo json_encode($response);