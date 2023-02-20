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

$fermentationResult = $myData->getCurrentFermentationDataOnTank($tankID);
$activeFermentationCount = count($fermentationResult);

if ($activeFermentationCount == 1) {
    $fermentation = $fermentationResult[0];
    $fermentation["data"] = $myData->getFermentationDataByTankID($tankID);
    echo json_encode($fermentation);
} else {
    $errorCode = $activeFermentationCount == 0 ? ERROR_CODE_EMPTY_RESULT : ERROR_CODE_MULTI_RESULT;
    dieWithError(CUSTOM_HTTP_ERROR_CODE, "found " . $activeFermentationCount . " active process on the tank", $errorCode);
}

mysqli_close($dbLink);