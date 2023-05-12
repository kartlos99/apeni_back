<?php

namespace Apeni\JWT;

use MyData;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
checkToken();

$tankID = 0;
if (isset($_GET["tankID"]) && $_GET["tankID"] > 0)
    $tankID = $_GET["tankID"];
else
    dieWithDefaultHttpError("no tank id is set!", 505);
//$fermentationID = $_GET["fermentationID"];

$myData = new MyData($dbLink);

$fermentationResult = $myData->getCurrentFermentationDataOnTank($tankID);
$activeFermentationCount = count($fermentationResult);

if ($activeFermentationCount == 1) {
    $fermentation = $fermentationResult[0];
    $fermentation["data"] = $myData->getFermentationDataByID($fermentation["ID"]);
    $fermentation["pouredVolume"] =
        $myData->getAmountToFilter($fermentation["ID"]) + $myData->pouredFromFermentationToBarrels($fermentation["ID"]);
    $fermentation["brews"] = $myData->getBrewsInFermentation($fermentation["ID"]);
    echo json_encode($fermentation);
} else {
    $errorCode = $activeFermentationCount == 0 ? ERROR_CODE_EMPTY_RESULT : ERROR_CODE_MULTI_RESULT;
    dieWithError(CUSTOM_HTTP_ERROR_CODE, "found " . $activeFermentationCount . " active process on the tank", $errorCode);
}

mysqli_close($dbLink);