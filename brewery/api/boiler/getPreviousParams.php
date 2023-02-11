<?php

namespace Apeni\JWT;

use BoilerDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();
//$userID = $sessionData->userID;

$json = file_get_contents('php://input');
//$postData = json_decode($json);

$dbManager = new BoilerDataManager();

$beerID = $_GET["beerID"];

$defaultNextID = $dbManager->nextBoilingID();
$response = ["defaultNumber" => $defaultNextID];

$fullData = $dbManager->getLastBoilingData($beerID);

if (!empty($fullData)) {
    $boilingID = $fullData["ID"];
    $fullData["waterList"] = $dbManager->getWaterData($boilingID);
    $fullData["saltList"] = $dbManager->getSaltData($boilingID);
    $fullData["maltList"] = $dbManager->getMaltData($boilingID);
    $fullData["hopsList"] = $dbManager->getHopsData($boilingID);
    $fullData["delayList"] = $dbManager->getDelayData($boilingID);
    $fullData["filteringList"] = $dbManager->getFilteringData($boilingID);
    $response["params"] = $fullData;
} else {
    $response["params"] = null;
}
echo json_encode($response);

$dbManager->closeConnection();