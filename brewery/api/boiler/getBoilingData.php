<?php

namespace Apeni\JWT;

use BoilerDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();

$dbManager = new BoilerDataManager();

$boilingID = $_GET["boilingID"];

$fullData = $dbManager->getBoilingByID($boilingID);

if (!empty($fullData)) {
    $fullData["waterList"] = $dbManager->getWaterData($boilingID);
    $fullData["saltList"] = $dbManager->getSaltData($boilingID);
    $fullData["maltList"] = $dbManager->getMaltData($boilingID);
    $fullData["hopsList"] = $dbManager->getHopsData($boilingID);
    $fullData["delayList"] = $dbManager->getDelayData($boilingID);
    $fullData["filteringList"] = $dbManager->getFilteringData($boilingID);
    $fullData["distributionList"] = $dbManager->getDistributionData($boilingID);
} else {
    dieWithDefaultHttpError(ERROR_TEXT_RECORD_NOT_FOUNDED, ERROR_CODE_RECORD_NOT_FOUNDED);
}
echo json_encode($fullData);

$dbManager->closeConnection();