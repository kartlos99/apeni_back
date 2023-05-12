<?php

namespace Apeni\JWT;

use MyData;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
checkToken();

$myData = new MyData($dbLink);

$tanks = $myData->getFermentationTanks();
$fermentations = $myData->getAllActiveFermentation();

$response = [];
foreach ($tanks as $tank) {
    $searchResult = array_filter($fermentations, function ($fermentationItem) {
        global $tank;
        return $fermentationItem["tankID"] == $tank["ID"];
    });
    $tank["fermentation"] = count($searchResult) > 0 ? array_values($searchResult)[0] : null;
    $response[] = $tank;
}

echo json_encode($response);

mysqli_close($dbLink);