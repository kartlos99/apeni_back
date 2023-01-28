<?php

namespace Apeni\JWT;

use BoilerDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();
$userID = $sessionData->userID;

$json = file_get_contents('php://input');
$postData = json_decode($json);

$dbManager = new BoilerDataManager();

$beerID = $_GET["beerID"];

$defaultNextID = $dbManager->nextBoilingID();

$fullData = $dbManager->getLastBoilingData($beerID);
//echo json_encode($fullData);
$boilingID = $fullData["ID"];
$fullData["waterList"] = $dbManager->getWaterData($boilingID);
$fullData["saltList"] = $dbManager->getSaltData($boilingID);
$fullData["maltList"] = $dbManager->getMaltData($boilingID);
$fullData["hopsList"] = $dbManager->getHopsData($boilingID);
$fullData["delayList"] = $dbManager->getDelayData($boilingID);
$fullData["filteringList"] = $dbManager->getFilteringData($boilingID);
$fullData["defaultNumber"] = $defaultNextID;
echo json_encode($fullData);

$dbManager->closeConnection();