<?php
namespace Apeni\JWT;

use MyData;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
checkToken();

$myData = new MyData($dbLink);

echo json_encode($myData->getTanks());

mysqli_close($dbLink);