<?php
namespace Apeni\JWT;

use MyData;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
checkToken();

$myData = new MyData($dbLink);

$response[DATA] = $myData->getTanks();

echo json_encode($response);

mysqli_close($dbLink);