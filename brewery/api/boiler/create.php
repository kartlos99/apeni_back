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

$startDate = $postData->startDate;
if (empty($startDate))
    $startDate = $timeOnServer;

$insertBoilingResult = $dbManager->insertBoiling(
    $postData->code,
    $startDate,
    $postData->density,
    $postData->amount,
    $postData->tankID,
    $postData->beerID,
    $postData->boilingTime,
    $postData->amountToVirlpool,
    $postData->yeast,
    $postData->comment,
    $userID
);

$boilingRecordID = $insertBoilingResult[RECORD_ID_KEY];

$dbManager->insertWaterDataItems($postData->waterList, $boilingRecordID, $userID);
$dbManager->insertSaltDataItems($postData->saltList, $boilingRecordID, $userID);
$dbManager->insertMaltDataItems($postData->maltList, $boilingRecordID, $userID);
$dbManager->insertHopsDataItems($postData->hopsList, $boilingRecordID, $userID);
$dbManager->insertDelayDataItems($postData->delayList, $boilingRecordID, $userID);
$dbManager->insertFilteringDataItems($postData->filteringList, $boilingRecordID, $userID);

echo json_encode($insertBoilingResult);

$dbManager->closeConnection();