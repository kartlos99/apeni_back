<?php
namespace Apeni\JWT;

use MyData;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
checkToken();

$tankID = $_GET["tankID"];
//$fermentationID = $_GET["fermentationID"];

$myData = new MyData($dbLink);

echo json_encode($myData->getFermentationDataByTankID($tankID));

mysqli_close($dbLink);